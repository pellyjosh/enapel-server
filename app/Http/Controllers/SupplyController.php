<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Api\Purchase;
use App\Models\Api\Supplier;

class SupplyController extends Controller
{
    public function addOrder(Request $request)
    {
        $validatedData = $request->validate([
            'supplier' => 'required|string|max:255',
            'company' => 'required|string|max:255',
            'phone' => 'required|numeric|regex:/^\d{10,11}$/',
            'product' => 'required|string|max:255',
            'quantity' => 'required|numeric|min:0',
            'amount' => 'required|numeric|min:0',
        ]);

        $supplier = Supplier::firstOrCreate(
            [
                'supplier' => $validatedData['supplier'],
                'company' => $validatedData['company'],
            ],
            ['phone' => $validatedData['phone']]
        );

           Purchase::create([
            'supplier_id' => $supplier->id,
            'product' => $validatedData['product'],
            'quantity' => $validatedData['quantity'],
            'amount' => $validatedData['amount'],
        ]);

        return redirect()->route('order')->with('success', 'Order added successfully!');
    }
    public function supplierData(Request $request)
    {

        $supplierData = Supplier::all()->makeHidden(['created_at', 'updated_at']);

        return view('user.suppliers.supply', ['supplier' => $supplierData]);
    }
    

    public function updateSupplier(Request $request, $id)
    {
        $updatedData = $request->only(['supplier', 'company', 'phone',]);

        Supplier::where('id', $id)->update([
            'supplier' => $updatedData['supplier'],
            'company' => $updatedData['company'],
            'phone' => $updatedData['phone'],
        ]);
        return redirect()->route('supplier.show')->with('success', 'supplier data updated successfully!');
    }
    public function deleteSupplier(Request $request, $id)
    {
        Supplier::where('id', $id)->delete();

        return redirect()->route('supplier.show')->with('success', 'supplier data updated successfully!');
    }
    public function getPurchase(Request $request)
    {
       
            $purchasedata = Purchase::selectRaw('id, supplier_id, product, quantity, amount, strftime("%Y-%m-%d", created_at) as formatted_date')
                ->with('supplier')
                ->get();

            return view('user.reports.purchases', ['purchases' => $purchasedata]);

    }
    public function syncPurchase(Request $request)
    {

        $purchasedata = Purchase::selectRaw('id, supplier_id, product, quantity, amount, strftime("%Y-%m-%d", created_at) as formatted_date')
        ->with('supplier')
        ->get();

        return view('sync.Sync_purchases', ['purchases' => $purchasedata]);
    }
}
