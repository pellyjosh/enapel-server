<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Api\Purchase;
use App\Models\Api\Supplier;

class SupplyController extends Controller
{
    public function addOrder(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'supplier' => 'required|string|max:255',
                'company' => 'required|string|max:255',
                'phone' => 'required|string|regex:/^\d{10,11}$/',
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

            $purchase = Purchase::create([
                'supplier_id' => $supplier->id,
                'product' => $validatedData['product'],
                'quantity' => $validatedData['quantity'],
                'amount' => $validatedData['amount'],
            ]);


            return response()->json([
                'success' => true,
                'message' => 'New order recorded successfully',
                'data' => [$supplier, $purchase]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while recording the order data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function supplierData(Request $request)
    {
        try {
            $supplierData = Supplier::all()->makeHidden(['created_at', 'updated_at']);

            if ($supplierData->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No supplier data found'
                ], 404);
            }
            return response()->json([
                'success' => true,
                'message' => 'Supplier data retrieved successfully',
                'data' => $supplierData
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve supplier data',
                'error' => $exception->getMessage()
            ], 500);
        }
    }

    public function updateSupplier(Request $request)
    {
        try {
            $id = $request->id;

            $updatedData = $request->only(['supplier', 'company', 'phone',]);

            $updated = Supplier::where('id', $id)->update([
                'supplier' => $updatedData['supplier'],
                'company' => $updatedData['company'],
                'phone' => $updatedData['phone'],
            ]);
            if ($updated) {
                return response()->json([
                    'success' => true,
                    'message' => 'Suppliers data updated successfully',
                    'data' => $updatedData,
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Suppliers data not found or update failed',
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the Suppliers data',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function deleteSupplier(Request $request)
    {
        $supplier = $request->id;
        Supplier::where('id', $supplier)->delete();

        return response()->json([
            'success' => true,
            'message' => 'supplier deleted',
        ], 200);
    }
    public function getPurchase(Request $request)
    {
        try {
            $purchasedata = Purchase::selectRaw('id, supplier_id, product, quantity, amount, strftime("%Y-%m-%d", created_at) as formatted_date')
                ->with('supplier')
                ->get();

            if ($purchasedata->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No Purchase data found'
                ], 404);
            }
            return response()->json([
                'success' => true,
                'message' => 'Purchase data retrieved successfully',
                'data' => $purchasedata
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve Purchase data',
                'error' => $exception->getMessage()
            ], 500);
        }
    }
}
