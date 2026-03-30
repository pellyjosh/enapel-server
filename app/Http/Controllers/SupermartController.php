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
        $this->syncSupermartCategories();

        $products = Inventory::where(function ($query) {
            $query->whereNull('category')
                ->orWhere('category', 'NOT LIKE', '%drug%');
        })->latest()->paginate(15);

        $categories = SupermartCategory::orderBy('name')->get(['id', 'name']);

        return Inertia::render('Supermart/ProductCatalog', [
            'products' => $products,
            'categories' => $categories
        ]);
    }

    public function pos()
    {
        $products = Inventory::where(function ($query) {
            $query->whereNull('category')
                ->orWhere('category', 'NOT LIKE', '%drug%');
        })->where('quantity', '>', 0)->get();

        return Inertia::render('Supermart/Pos', ['products' => $products]);
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
        $this->syncSupermartCategories();

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
        $this->syncSupermartCategories();

        $inventory = Inventory::where(function ($q) {
            $q->whereNull('category')->orWhere('category', 'NOT LIKE', '%drug%');
        })->latest()->paginate(15);
        return Inertia::render('Supermart/Stock', ['inventory' => $inventory]);
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
        $products = Inventory::where(function ($q) {
            $q->whereNull('category')->orWhere('category', 'NOT LIKE', '%drug%');
        })->get();

        $totalStockValue = $products->sum(fn($p) => $p->quantity * $p->price);
        $lowStockItems = $products->where('quantity', '>', 0)->where('quantity', '<=', 10)->count();
        $outOfStockItems = $products->where('quantity', 0)->count();

        // Calculate sales velocity (daily average sales vs target - simplified)
        $recentSalesCount = \App\Models\Api\Sales::where('created_at', '>=', now()->subDays(7))->count();
        $dailyAverage = $recentSalesCount / 7;
        $targetDaily = 50; // Mock target
        $salesVelocity = $targetDaily > 0 ? min(100, round(($dailyAverage / $targetDaily) * 100)) : 0;

        // Inventory distribution
        $totalItems = $products->count();
        $fastMoving = $products->where('quantity', '>=', 50)->count(); // Simplified logic
        $slowMoving = $products->where('quantity', '<', 50)->count();

        $fastMovingPercent = $totalItems > 0 ? round(($fastMoving / $totalItems) * 100) : 0;
        $slowMovingPercent = $totalItems > 0 ? round(($slowMoving / $totalItems) * 100) : 0;

        return Inertia::render('Supermart/Reports', [
            'stats' => [
                'total_stock_value' => $totalStockValue,
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
            'category' => ['nullable', 'string', 'max:255', 'not_regex:/drug/i'],
            'description' => 'nullable|string|max:2000',
            'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'sku' => ['nullable', 'string', 'max:255', Rule::unique('inventories', 'sku')],
        ]);

        Inventory::create([
            'name' => $validated['name'],
            'sku' => $validated['sku'] ?? null, // Use validated SKU
            'category' => $validated['category'] ?? null,
            'description' => $validated['description'] ?? null,
            'quantity' => $validated['quantity'],
            'price' => $validated['price'],
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
            'category' => ['nullable', 'string', 'max:255', 'not_regex:/drug/i'],
            'description' => 'nullable|string|max:2000',
            'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'sku' => ['nullable', 'string', 'max:255', Rule::unique('inventories', 'sku')->ignore($id)],
        ]);

        $product = Inventory::findOrFail($id);
        $product->update([
            'name' => $validated['name'],
            'sku' => $request->sku,
            'category' => $validated['category'] ?? null,
            'description' => $validated['description'] ?? null,
            'quantity' => $validated['quantity'],
            'price' => $validated['price'],
        ]);

        if (!empty($validated['category'])) {
            SupermartCategory::firstOrCreate(['name' => $validated['category']]);
        }

        return back()->with('success', 'Product updated successfully.');
    }

    public function deleteProduct($id)
    {
        Inventory::where('id', $id)->delete();

        return back()->with('success', 'Product deleted successfully.');
    }

    public function updateStock(Request $request, $id)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:0',
            'price' => 'nullable|numeric|min:0',
        ]);

        $item = Inventory::findOrFail($id);
        $update = ['quantity' => $validated['quantity']];

        if ($request->filled('price')) {
            $update['price'] = $validated['price'];
        }

        $item->update($update);

        return back()->with('success', 'Stock updated successfully.');
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
