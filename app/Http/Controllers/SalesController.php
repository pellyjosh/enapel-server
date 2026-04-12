<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Api\Receipt;
use App\Models\Api\Sales;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SalesController extends Controller
{
    public function checkout(Request $request)
    {
        try {
                $validatedData = $request->validate([
                'items' => 'required|array',
                'items.*.id' => 'required|exists:inventories,id',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.is_pack' => 'nullable|boolean',
                'items.*.is_carton' => 'nullable|boolean',
                'payment_method' => 'required|string',
                'cash_paid' => 'nullable|numeric',
            ]);

            $totalReceiptPrice = 0;
            $itemsToProcess = [];

            foreach ($validatedData['items'] as $itemData) {
                $inventory = \App\Models\Api\Inventory::find($itemData['id']);
                $isPack = $itemData['is_pack'] ?? false;
                $isCarton = $itemData['is_carton'] ?? false;
                
                // Prioritize carton over pack for deduction logic
                if ($isCarton) {
                    $deductQty = $inventory->packs_per_carton * $inventory->units_per_pack * $itemData['quantity'];
                    $price = $inventory->carton_price_override ?? (
                        ($inventory->pack_price_override ?? ($inventory->price * $inventory->units_per_pack)) * $inventory->packs_per_carton
                    );
                    $sellingUnit = "Carton of {$inventory->packs_per_carton} packs";
                } elseif ($isPack) {
                    $deductQty = $inventory->units_per_pack * $itemData['quantity'];
                    $price = $inventory->pack_price_override ?? ($inventory->price * $inventory->units_per_pack);
                    $sellingUnit = "Pack of {$inventory->units_per_pack}";
                } else {
                    $deductQty = $itemData['quantity'];
                    $price = $inventory->price;
                    $sellingUnit = $inventory->unit_name;
                }

                if ($inventory->quantity < $deductQty) {
                    return response()->json([
                        'success' => false,
                        'message' => "Insufficient stock for " . ($inventory->product_name ?? $inventory->name),
                    ], 400);
                }

                $totalReceiptPrice += $price * $itemData['quantity'];
                
                $itemsToProcess[] = [
                    'inventory' => $inventory,
                    'quantity' => $itemData['quantity'],
                    'deduct_qty' => $deductQty,
                    'is_pack' => $isPack,
                    'is_carton' => $isCarton,
                    'price' => $price,
                    'selling_unit' => $sellingUnit
                ];
            }

            $receiptNumber = 'REC-' . strtoupper(Str::random(10));
            $changeDue = 0;
            if ($validatedData['payment_method'] === 'cash' && isset($validatedData['cash_paid'])) {
                $changeDue = $validatedData['cash_paid'] - $totalReceiptPrice;
            }

            $receipt = Receipt::create([
                'receipt_number' => $receiptNumber,
                'total_price' => $totalReceiptPrice,
                'payment_method' => $validatedData['payment_method'],
                'cash_paid' => $validatedData['cash_paid'] ?? $totalReceiptPrice,
                'change_due' => $changeDue,
                'date' => now(),
            ]);

            foreach ($itemsToProcess as $processItem) {
                $inventory = $processItem['inventory'];
                
                Sales::create([
                    'receipt_id' => $receipt->id,
                    'product_id' => $inventory->id,
                    'product_name' => $inventory->product_name ?? $inventory->name,
                    'product_sku' => $inventory->sku,
                    'quantity' => $processItem['quantity'],
                    'is_pack' => $processItem['is_pack'],
                    'is_carton' => $processItem['is_carton'],
                    'selling_unit' => $processItem['selling_unit'],
                    'price' => $processItem['price'],
                ]);

                $inventory->decrement('quantity', $processItem['deduct_qty']);
            }

            return response()->json([
                'success' => true,
                'message' => 'Checkout successful',
                'receipt_number' => $receipt->receipt_number,
                'total_price' => $totalReceiptPrice,
                'cart_items' => $itemsToProcess, // Returning processed items for receipt clarity
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while processing the checkout',
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    public function getSales(Request $request)
    {
        $SalesData = Sales::all()
            ->makeHidden(['receipt_id'])
            ->map(function ($sale) {
                $sale->date = Carbon::parse($sale->date)->format('d-m-Y'); // Format to YYYY-MM-DD
                return $sale;
            });

        return view('user.reports.sales', ['sales' => $SalesData]);
    }
    public function syncSales(Request $request)
    {
        $SalesData = Sales::all()
            ->makeHidden(['receipt_id'])
            ->map(function ($sale) {
                $sale->date = Carbon::parse($sale->date)->format('d-m-Y'); // Format to YYYY-MM-DD
                return $sale;
            });

        return view('sync.Sync_sales', ['sales' => $SalesData]);
    }
    // public function getSalesByReceipt($receiptNumber)
    // {
    //     try {
    //         $receipt = Receipt::where('receipt_number', $receiptNumber)->with('sales')->first();

    //         if (!$receipt) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Receipt not found',
    //             ], 404);
    //         }

    //         $salesData = $receipt->sales->makeHidden(['receipt_id']);

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Sales retrieved successfully',
    //             'receipt' => [
    //                 'receipt_number' => $receipt->receipt_number,
    //                 'total_price' => $receipt->total_price,
    //                 'items' => $salesData,
    //             ],
    //         ], 200);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'An error occurred while retrieving sales',
    //             'error' => $e->getMessage(),
    //         ], 500);
    //     }
    // }

}
