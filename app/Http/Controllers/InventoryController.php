<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Api\Inventory;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function showInventory()
    {
        $items = Inventory::all();
        $statuses = [];

        foreach ($items as $product) {
            $status = $product->quantity > 0 ? 'Sufficient' : 'Reorder';

            $statuses[] = [
                'id' => $product->id,
                'product' => $product->name,
                'quantity' => $product->quantity,
                'price' => $product->price,
                'status' => $status,
            ];
        }

        return view('user.inventory.inventory', ['items' => $statuses]);
    }
    public function syncStock()
    {
        $items = Inventory::all();
        $statuses = [];

        foreach ($items as $product) {
            $status = $product->quantity > 0 ? 'Sufficient' : 'Reorder';

            $statuses[] = [
                'id' => $product->id,
                'product' => $product->name,
                'quantity' => $product->quantity,
                'price' => $product->price,
                'status' => $status,
            ];
        }

        return view('sync.Sync_stock', ['items' => $statuses]);
    }
    public function showStock()
    {
        $items = Inventory::all();
        $statuses = [];

        foreach ($items as $product) {
            $status = $product->quantity > 0 ? 'Sufficient' : 'Reorder';

            $statuses[] = [
                'id' => $product->id,
                'product' => $product->name,
                'quantity' => $product->quantity,
                'price' => $product->price,
                'status' => $status,
            ];
        }

        return view('user.reports.stock', ['items' => $statuses]);
    }

    public function storeInventory(Request $request)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'qty' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'user_id' => 'required|integer|exists:users,id',
        ]);

        Inventory::create([
            'name' => $validatedData['name'],
            'quantity' => $validatedData['qty'],
            'price' => $validatedData['price'],
            'staffid' => $validatedData['user_id'],
        ]);

        return redirect()->route('inventory.show')->with('success', 'Inventory item added successfully!');
    }
 
    public function updateInventory(Request $request, $id)
    {
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'quantity' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
        ]);

        $item = Inventory::findOrFail($id);
        $item->update([
            'name' => $validatedData['name'],
            'quantity' => $validatedData['quantity'],
            'price' => $validatedData['price'],
        ]);

        return redirect()->route('inventory.show')->with('success', 'Inventory item updated successfully!');
    }

    public function deleteInventory($id)
    {
        Inventory::where('id', $id)->delete();
        return redirect()->route('inventory.show')->with('success', 'Inventory item deleted successfully!');
    }
}
