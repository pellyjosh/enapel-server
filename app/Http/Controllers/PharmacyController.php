<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Api\Inventory;
use Inertia\Inertia;
use Carbon\Carbon;

class PharmacyController extends Controller
{
    private function normalizeDrugCategory(?string $category): string
    {
        $value = trim((string) $category);

        if ($value === '') {
            return 'drug';
        }

        if (preg_match('/drug/i', $value)) {
            return $value;
        }

        return 'Drug - ' . $value;
    }

    public function dashboard()
    {
        $stats = [
            'total_drugs' => Inventory::where('category', 'LIKE', '%drug%')->count(),
            'low_stock' => Inventory::where('category', 'LIKE', '%drug%')->where('quantity', '<=', 10)->count(),
            'expiring_soon' => Inventory::where('category', 'LIKE', '%drug%')
                ->whereNotNull('expiry_date')
                ->where('expiry_date', '<', now()->addDays(30))
                ->count(),
            'pending_prescriptions' => \App\Models\Prescription::where('status', 'pending')->count(),
        ];

        return Inertia::render('Pharmacy/Dashboard', [
            'metrics' => $stats
        ]);
    }

    public function catalog()
    {
        $drugs = Inventory::where('category', 'LIKE', '%drug%')->latest()->get();
        return Inertia::render('Pharmacy/DrugCatalog', ['drugs' => $drugs]);
    }

    public function storeDrug(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:2000',
            'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'batch_number' => 'nullable|string|max:255',
            'expiry_date' => 'nullable|date',
        ]);

        Inventory::create([
            'name' => $validated['name'],
            'category' => $this->normalizeDrugCategory($validated['category'] ?? null),
            'description' => $validated['description'] ?? null,
            'quantity' => $validated['quantity'],
            'price' => $validated['price'],
            'batch_number' => $validated['batch_number'] ?? null,
            'expiry_date' => $validated['expiry_date'] ?? null,
            'staffid' => $request->user()?->id,
        ]);

        return back()->with('success', 'Drug added successfully.');
    }

    public function updateDrug(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'nullable|string|max:255',
            'description' => 'nullable|string|max:2000',
            'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'batch_number' => 'nullable|string|max:255',
            'expiry_date' => 'nullable|date',
        ]);

        $drug = Inventory::findOrFail($id);
        $drug->update([
            'name' => $validated['name'],
            'category' => array_key_exists('category', $validated)
                ? $this->normalizeDrugCategory($validated['category'])
                : $drug->category,
            'description' => $validated['description'] ?? null,
            'quantity' => $validated['quantity'],
            'price' => $validated['price'],
            'batch_number' => $validated['batch_number'] ?? null,
            'expiry_date' => $validated['expiry_date'] ?? null,
        ]);

        return back()->with('success', 'Drug updated successfully.');
    }

    public function deleteDrug($id)
    {
        Inventory::where('id', $id)->delete();

        return back()->with('success', 'Drug removed successfully.');
    }

    public function pos()
    {
        $drugs = Inventory::where('category', 'LIKE', '%drug%')->where('quantity', '>', 0)->get();
        return Inertia::render('Pharmacy/Pos', ['drugs' => $drugs]);
    }

    public function prescriptions()
    {
        $prescriptions = \App\Models\Prescription::latest()->get();
        return Inertia::render('Pharmacy/Prescriptions', ['prescriptions' => $prescriptions]);
    }

    public function storePrescription(Request $request)
    {
        $validated = $request->validate([
            'patient_name' => 'required|string|max:255',
            'doctor_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
        ]);

        \App\Models\Prescription::create($validated);

        return back()->with('success', 'Prescription created successfully.');
    }

    public function updatePrescription(Request $request, $id)
    {
        $validated = $request->validate([
            'patient_name' => 'required|string|max:255',
            'doctor_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'status' => 'required|in:pending,dispensed,cancelled',
        ]);

        $prescription = \App\Models\Prescription::findOrFail($id);
        $prescription->update($validated);

        return back()->with('success', 'Prescription updated successfully.');
    }

    public function dispensePrescription($id)
    {
        $prescription = \App\Models\Prescription::findOrFail($id);
        $prescription->update(['status' => 'dispensed']);

        return back()->with('success', 'Prescription marked as dispensed.');
    }

    public function deletePrescription($id)
    {
        \App\Models\Prescription::where('id', $id)->delete();

        return back()->with('success', 'Prescription deleted successfully.');
    }

    public function sales()
    {
        $sales = \App\Models\Api\Sales::with(['product', 'receipt'])
            ->whereHas('product', function ($q) {
                $q->where('category', 'LIKE', '%drug%');
            })
            ->latest()
            ->get();
        return Inertia::render('Pharmacy/Sales', ['sales' => $sales]);
    }

    public function stock()
    {
        $inventory = Inventory::where('category', 'LIKE', '%drug%')->latest()->get();
        return Inertia::render('Pharmacy/Stock', ['inventory' => $inventory]);
    }

    public function updateStock(Request $request, $id)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:0',
            'price' => 'nullable|numeric|min:0',
            'batch_number' => 'nullable|string|max:255',
            'expiry_date' => 'nullable|date',
        ]);

        $item = Inventory::findOrFail($id);
        $update = ['quantity' => $validated['quantity']];

        if (array_key_exists('price', $validated)) {
            $update['price'] = $validated['price'];
        }

        if (array_key_exists('batch_number', $validated)) {
            $update['batch_number'] = $validated['batch_number'];
        }

        if (array_key_exists('expiry_date', $validated)) {
            $update['expiry_date'] = $validated['expiry_date'];
        }

        $item->update($update);

        return back()->with('success', 'Stock updated successfully.');
    }

    public function alerts()
    {
        $lowStock = Inventory::where('category', 'LIKE', '%drug%')->where('quantity', '<=', 10)->get();
        $expiring = Inventory::where('category', 'LIKE', '%drug%')
            ->whereNotNull('expiry_date')
            ->where('expiry_date', '<', now()->addDays(30))
            ->get();
        return Inertia::render('Pharmacy/Alerts', [
            'lowStock' => $lowStock,
            'expiring' => $expiring
        ]);
    }

    public function suppliers()
    {
        $suppliers = \App\Models\Api\Supplier::all();
        return Inertia::render('Pharmacy/Suppliers', ['suppliers' => $suppliers]);
    }

    public function orders()
    {
        $orders = \App\Models\Api\Purchase::with(['supplier', 'inventory'])->latest()->get();
        return Inertia::render('Pharmacy/Orders', ['orders' => $orders]);
    }
}
