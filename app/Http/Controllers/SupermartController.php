<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Api\Inventory;
use App\Models\SupermartCategory;
use Inertia\Inertia;
use Illuminate\Validation\Rule;

class SupermartController extends Controller
{
    private function syncSupermartCategories(): void
    {
        $inventoryCategories = Inventory::whereNotNull('category')
            ->where('category', 'NOT LIKE', '%drug%')
            ->distinct()
            ->pluck('category')
            ->map(function ($name) {
                return trim($name);
            })
            ->filter();

        foreach ($inventoryCategories as $name) {
            SupermartCategory::firstOrCreate(['name' => $name]);
        }
    }

    public function dashboard()
    {
        $stats = [
            'total_products' => Inventory::where(function ($q) {
                $q->whereNull('category')->orWhere('category', 'NOT LIKE', '%drug%');
            })->count(),
            'out_of_stock' => Inventory::where('quantity', 0)->count(),
            'low_stock' => Inventory::where(function ($q) {
                $q->whereNull('category')->orWhere('category', 'NOT LIKE', '%drug%');
            })->where('quantity', '>', 0)->where('quantity', '<=', 10)->count(),
            'total_sales' => \App\Models\Api\Sales::count(),
        ];

        return Inertia::render('Supermart/Dashboard', [
            'metrics' => $stats
        ]);
    }

    public function catalog()
    {
        $products = Inventory::where(function ($query) {
            $query->whereNull('category')
                ->orWhere('category', 'NOT LIKE', '%drug%');
        })
        ->with(['variations', 'parent'])
        ->whereNull('parent_id') // Group by main product for the list
        ->latest()
        ->paginate(15);

        $categories = SupermartCategory::orderBy('name')->get(['id', 'name']);

        return Inertia::render('Supermart/ProductCatalog', [
            'products' => $products,
            'categories' => $categories,
            'all_products' => Inventory::all(['id', 'name']) // For selecting parents
        ]);
    }

    public function pos()
    {
        $products = Inventory::where(function ($query) {
            $query->whereNull('category')
                ->orWhere('category', 'NOT LIKE', '%drug%');
        })
        ->whereNull('parent_id')
        ->with('variations')
        ->where('quantity', '>', 0)
        ->get();

        $categories = SupermartCategory::orderBy('name')->get(['id', 'name']);

        return Inertia::render('Supermart/Pos', [
            'products' => $products,
            'categories' => $categories
        ]);
    }

    public function orders()
    {
        $orders = \App\Models\Api\Purchase::with(['supplier', 'inventory'])->whereHas('inventory', function ($q) {
            $q->whereNull('category')->orWhere('category', 'NOT LIKE', '%drug%');
        })->latest()->paginate(15);
        return Inertia::render('Supermart/Orders', ['orders' => $orders]);
    }

    public function categories()
    {

        $counts = Inventory::whereNotNull('category')
            ->where('category', 'NOT LIKE', '%drug%')
            ->selectRaw('category, count(*) as total')
            ->groupBy('category')
            ->pluck('total', 'category');

        $categoriesPaginator = SupermartCategory::orderBy('name')->paginate(15);

        $categories = $categoriesPaginator->getCollection()->map(function ($category) use ($counts) {
            return [
                'id' => $category->id,
                'name' => $category->name,
                'description' => $category->description,
                'items_count' => (int) ($counts[$category->name] ?? 0),
            ];
        });

        $categoriesPaginator->setCollection($categories);

        return Inertia::render('Supermart/Categories', ['categories' => $categoriesPaginator]);
    }

    public function stock()
    {
        $inventory = Inventory::where(function ($q) {
            $q->whereNull('category')->orWhere('category', 'NOT LIKE', '%drug%');
        })->latest()->paginate(15);

        // Fetch all products (including variants) for the adjustment dropdown
        $allProducts = Inventory::where(function ($q) {
            $q->whereNull('category')->orWhere('category', 'NOT LIKE', '%drug%');
        })->select('id', 'name', 'variation_name', 'quantity', 'price', 'expiry_date')->get();

        $suppliers = \App\Models\Api\Supplier::all();

        return Inertia::render('Supermart/Stock', [
            'inventory' => $inventory,
            'allProducts' => $allProducts,
            'suppliers' => $suppliers
        ]);
    }

    public function suppliers()
    {
        $suppliers = \App\Models\Api\Supplier::paginate(15);
        return Inertia::render('Supermart/Suppliers', ['suppliers' => $suppliers]);
    }

    public function invoices()
    {
        $receipts = \App\Models\Api\Receipt::with('sales.product')->latest()->paginate(15);
        return Inertia::render('Supermart/Invoices', ['receipts' => $receipts]);
    }

    public function reports()
    {
        // Use SQL aggregation instead of loading all products into memory
        $query = Inventory::where(function ($q) {
            $q->whereNull('category')->orWhere('category', 'NOT LIKE', '%drug%');
        });

        $totalStockValue = (clone $query)->selectRaw('SUM(quantity * price) as total')->value('total') ?? 0;
        $lowStockItems = (clone $query)->where('quantity', '>', 0)->where('quantity', '<=', 10)->count();
        $outOfStockItems = (clone $query)->where('quantity', 0)->count();

        // Calculate sales velocity (daily average sales vs target - simplified)
        $recentSalesCount = \App\Models\Api\Sales::where('created_at', '>=', now()->subDays(7))->count();
        $dailyAverage = $recentSalesCount / 7;
        $targetDaily = 50; // Mock target
        $salesVelocity = $targetDaily > 0 ? min(100, round(($dailyAverage / $targetDaily) * 100)) : 0;

        // Inventory distribution
        $totalItems = (clone $query)->count();
        $fastMoving = (clone $query)->where('quantity', '>=', 50)->count();
        $slowMoving = $totalItems - $fastMoving;

        $fastMovingPercent = $totalItems > 0 ? round(($fastMoving / $totalItems) * 100) : 0;
        $slowMovingPercent = $totalItems > 0 ? round(($slowMoving / $totalItems) * 100) : 0;

        return Inertia::render('Supermart/Reports', [
            'stats' => [
                'total_stock_value' => (float)$totalStockValue,
                'low_stock_items' => $lowStockItems,
                'out_of_stock_items' => $outOfStockItems,
                'sales_velocity' => $salesVelocity,
                'fast_moving_percent' => $fastMovingPercent,
                'slow_moving_percent' => $slowMovingPercent,
            ]
        ]);
    }

    public function storeProduct(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => ['nullable', 'string', 'max:255', Rule::unique('inventories', 'sku')],
            'category' => ['nullable', 'string', 'max:255', 'not_regex:/drug/i'],
            'description' => 'nullable|string|max:2000',
            'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'unit_name' => 'nullable|string|max:255',
            'units_per_pack' => 'nullable|integer|min:1',
            'pack_price_override' => 'nullable|numeric|min:0',
            'parent_id' => 'nullable|exists:inventories,id',
            'variation_name' => 'nullable|string|max:255',
            'packs_per_carton' => 'nullable|integer|min:1',
            'carton_price_override' => 'nullable|numeric|min:0',
        ]);

        Inventory::create([
            'name' => $validated['name'],
            'sku' => $validated['sku'] ?? null,
            'category' => $validated['category'] ?? null,
            'description' => $validated['description'] ?? null,
            'quantity' => $validated['quantity'],
            'price' => $validated['price'],
            'cost_price' => $validated['cost_price'] ?? 0,
            'unit_name' => $validated['unit_name'] ?? 'Piece',
            'units_per_pack' => $validated['units_per_pack'] ?? 1,
            'pack_price_override' => $validated['pack_price_override'] ?? null,
            'parent_id' => $validated['parent_id'] ?? null,
            'variation_name' => $validated['variation_name'] ?? null,
            'packs_per_carton' => $validated['packs_per_carton'] ?? 1,
            'carton_price_override' => $validated['carton_price_override'] ?? null,
            'staffid' => $request->user()?->id,
        ]);

        if (!empty($validated['category'])) {
            SupermartCategory::firstOrCreate(['name' => $validated['category']]);
        }

        return back()->with('success', 'Product added successfully.');
    }

    public function updateProduct(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'sku' => ['nullable', 'string', 'max:255', Rule::unique('inventories', 'sku')->ignore($id)],
            'category' => ['nullable', 'string', 'max:255', 'not_regex:/drug/i'],
            'description' => 'nullable|string|max:2000',
            'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'cost_price' => 'nullable|numeric|min:0',
            'unit_name' => 'nullable|string|max:255',
            'units_per_pack' => 'nullable|integer|min:1',
            'pack_price_override' => 'nullable|numeric|min:0',
            'parent_id' => 'nullable|exists:inventories,id',
            'variation_name' => 'nullable|string|max:255',
            'packs_per_carton' => 'nullable|integer|min:1',
            'carton_price_override' => 'nullable|numeric|min:0',
        ]);

        $product = Inventory::findOrFail($id);
        $product->update([
            'name' => $validated['name'],
            'sku' => $validated['sku'],
            'category' => $validated['category'] ?? null,
            'description' => $validated['description'] ?? null,
            'quantity' => $validated['quantity'],
            'price' => $validated['price'],
            'cost_price' => $validated['cost_price'] ?? 0,
            'unit_name' => $validated['unit_name'] ?? 'Piece',
            'units_per_pack' => $validated['units_per_pack'] ?? 1,
            'pack_price_override' => $validated['pack_price_override'] ?? null,
            'parent_id' => $validated['parent_id'] ?? null,
            'variation_name' => $validated['variation_name'] ?? null,
            'packs_per_carton' => $validated['packs_per_carton'] ?? 1,
            'carton_price_override' => $validated['carton_price_override'] ?? null,
        ]);

        if (!empty($validated['category'])) {
            SupermartCategory::firstOrCreate(['name' => $validated['category']]);
        }

        return back()->with('success', 'Product updated successfully.');
    }

    public function deleteProduct($id)
    {
        $product = Inventory::with('variations')->findOrFail($id);

        // Block deletion if the product itself still has stock
        if ($product->quantity > 0) {
            return back()->withErrors([
                'delete' => "Cannot delete \"{$product->name}\" — it still has {$product->quantity} unit(s) in inventory. Reduce the stock to zero before deleting."
            ]);
        }

        // Block deletion if any variation still has stock
        $variationsWithStock = $product->variations->where('quantity', '>', 0);
        if ($variationsWithStock->count() > 0) {
            $names = $variationsWithStock->pluck('variation_name')->filter()->join(', ');
            return back()->withErrors([
                'delete' => "Cannot delete \"{$product->name}\" — {$variationsWithStock->count()} variation(s) still have inventory" . ($names ? " ({$names})" : '') . ". Clear all variation stock first."
            ]);
        }

        $product->delete();

        return back()->with('success', 'Product deleted successfully.');
    }

    public function updateStock(Request $request, $id)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:0',
            'price' => 'nullable|numeric|min:0',
            'expiry_date' => 'nullable|date',
            'supplier_id' => 'required|exists:suppliers,id',
        ]);

        $item = Inventory::findOrFail($id);
        $oldQuantity = $item->quantity;
        $newQuantity = $validated['quantity'];
        $delta = $newQuantity - $oldQuantity;

        $update = ['quantity' => $newQuantity];

        if ($request->filled('price')) {
            $update['price'] = $validated['price'];
        }

        if ($request->has('expiry_date')) {
            $update['expiry_date'] = $validated['expiry_date'];
        }

        $item->update($update);

        // Record the movement in PROCUREMENT history (Purchases)
        \App\Models\Api\Purchase::create([
            'supplier_id' => $validated['supplier_id'],
            'inventory_id' => $item->id,
            'product' => $item->name . ($item->variation_name ? " ({$item->variation_name})" : ""),
            'quantity' => $delta, // Record the delta (positive or negative)
            'amount' => $validated['price'] ?? $item->price,
            'expiry_date' => $validated['expiry_date'] ?? $item->expiry_date,
        ]);

        // Log Activity
        \App\Models\ActivityLog::create([
            'user_id' => $request->user()?->id,
            'action' => 'stock_adjustment',
            'description' => "Adjusted stock for \"{$item->name}\" from {$oldQuantity} to {$newQuantity} (Delta: {$delta}).",
            'module' => 'supermart',
            'ip_address' => $request->ip(),
        ]);

        return back()->with('success', 'Stock updated successfully.');
    }

    public function storeSupplier(Request $request)
    {
        $validated = $request->validate([
            'supplier' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
        ]);

        $supplier = \App\Models\Api\Supplier::create($validated);

        if ($request->wantsJson()) {
            return response()->json($supplier);
        }

        return back()->with('success', 'Supplier connected successfully.');
    }

    public function updateSupplier(Request $request, $id)
    {
        $validated = $request->validate([
            'supplier' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
        ]);

        $supplier = \App\Models\Api\Supplier::findOrFail($id);
        $supplier->update($validated);

        return back()->with('success', 'Supplier updated successfully.');
    }

    public function deleteSupplier($id)
    {
        $supplier = \App\Models\Api\Supplier::findOrFail($id);
        $supplier->delete();

        return back()->with('success', 'Supplier disconnected successfully.');
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'not_regex:/drug/i', Rule::unique('supermart_categories', 'name')],
            'description' => 'nullable|string|max:1000',
        ]);

        SupermartCategory::create($validated);

        return back()->with('success', 'Category added successfully.');
    }

    public function updateCategory(Request $request, SupermartCategory $category)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'not_regex:/drug/i', Rule::unique('supermart_categories', 'name')->ignore($category->id)],
            'description' => 'nullable|string|max:1000',
        ]);

        $previousName = $category->name;
        $category->update($validated);

        if ($previousName !== $category->name) {
            Inventory::where('category', $previousName)->update(['category' => $category->name]);
        }

        return back()->with('success', 'Category updated successfully.');
    }

    public function deleteCategory(SupermartCategory $category)
    {
        Inventory::where('category', $category->name)->update(['category' => null]);
        $category->delete();

        return back()->with('success', 'Category removed successfully.');
    }
}
