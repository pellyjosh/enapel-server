<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Api\Inventory;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function getInventoryItem(Request $request)
    {
        try {
            // Get the 'search' query parameter if it exists
            $searchQuery = $request->query('search', '');

            $ItemsData = Inventory::where('name', 'like', '%' . $searchQuery . '%')->get();
            $statuses = [];

            foreach ($ItemsData as $product) {
                $status = $product->quantity > 0 ? 'Sufficient' : 'Low';
                $statuses[] = [
                    'id' => $product->id,
                    'name' => $product->name,
                    'quantity' => $product->quantity,
                    'price' => $product->price,
                    'status' => $status,
                    'created_at' => $product->created_at,
                    'updated_at' => $product->updated_at,
                ];
            }

            if ($ItemsData->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No Item data found'
                ], 404);
            }

            return response()->json([
                'success' => true,
                'message' => 'Item data retrieved successfully',
                'data' => $statuses
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve Item data',
                'error' => $exception->getMessage()
            ], 500);
        }
    }


    public function addInventoryItem(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'qty' => 'required|integer|min:0',
                'price' => 'required|numeric|min:0',
                'user_id' => 'required|integer|exists:users,id',
            ]);

            $inventory = Inventory::create([
                'name' => $validatedData['name'],
                'quantity' => $validatedData['qty'],
                'price' => $validatedData['price'],
                'staffid' => $validatedData['user_id'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'New Inventory item created successfully',
                'data' => $inventory,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating the inventory item',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateInventoryItem(Request $request)
    {
        try {
            $id = $request->id;

            $updateData = $request->validate([
                'name' => 'required|string|max:255',
                'qty' => 'required|integer|min:0',
                'price' => 'required|numeric|min:0',
            ]);


            $updated = Inventory::where('id', $id)->update([
                'name' => $updateData['name'],
                'quantity' => $updateData['qty'],
                'price' => $updateData['price'],
            ]);
            if ($updated) {
                return response()->json([
                    'success' => true,
                    'message' => 'Inventory item updated successfully',
                    'data' => $updateData,
                ], 200);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Inventory item not found or update failed',
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the inventory item',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function deleteInventoryItem(Request $request)
    {
        $item = $request->id;
        Inventory::where('id', $item)->delete();

        return response()->json([
            'success' => true,
            'message' => 'item deleted',
        ], 200);
    }
}
