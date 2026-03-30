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
                'payment_method' => 'required|string',
                'cash_paid' => 'nullable|numeric',
            ]);

            $totalReceiptPrice = 0;
            $itemsToProcess = [];

            foreach ($validatedData['items'] as $itemData) {
                $inventory = \App\Models\Api\Inventory::find($itemData['id']);
                if ($inventory->quantity < $itemData['quantity']) {
                    return response()->json([
                        'success' => false,
                        'message' => "Insufficient stock for " . ($inventory->product_name ?? $inventory->name),
                    ], 400);
                }

                $totalReceiptPrice += $inventory->price * $itemData['quantity'];
                $itemsToProcess[] = [
                    'inventory' => $inventory,
                    'quantity' => $itemData['quantity'],
                    'price' => $inventory->price
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
                $qty = $processItem['quantity'];

                Sales::create([
                    'receipt_id' => $receipt->id,
                    'product_id' => $inventory->id,
                    'product_name' => $inventory->product_name ?? $inventory->name,
                    'product_sku' => $inventory->sku,
                    'quantity' => $qty,
                    'price' => $processItem['price'],
                ]);

                $inventory->decrement('quantity', $qty);
            }

            return response()->json([
                'success' => true,
                'message' => 'Checkout successful',
                'receipt_number' => $receipt->receipt_number,
                'total_price' => $totalReceiptPrice,
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
