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
    // public function checkout(Request $request)
    // {
    //     try {
    //         $validatedData = $request->validate([
    //             'items' => 'required|array',
    //             'items.*.name' => 'required|string|max:255',
    //             'items.*.qty' => 'required|integer|min:0',
    //             'items.*.price' => 'required|numeric|min:0',
    //         ]);

    //         $receiptNumber = Str::uuid()->toString();

    //         $receipt = Receipt::create([
    //             'receipt_number' => $receiptNumber,
    //             'total_price' => 0, 
    //         ]);

    //         $totalReceiptPrice = 0; 

    //         foreach ($validatedData['items'] as $item) {
    //             // $itemTotalPrice = $item['qty'] * $item['price'];
    //             $totalReceiptPrice += $item['price']; 

    //             Sales::create([
    //                 'product' => $item['name'],
    //                 'amount' => $item['qty'],
    //                 'price' => $item['price'],
    //                 'receipt_id' => $receipt->id,
    //             ]);
    //         }

    //         $receipt->update(['total_price' => $totalReceiptPrice]);

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Sales grouped successfully under receipt',
    //             'receipt_number' => $receipt->receipt_number,
    //             'total_price' => $totalReceiptPrice,
    //         ], 201);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'An error occurred while processing the checkout',
    //             'error' => $e->getMessage(),
    //         ], 500);
    //     }
    // }


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
