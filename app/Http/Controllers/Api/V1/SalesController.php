<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Api\Receipt;
use App\Models\Api\Sales;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SalesController extends Controller
{

    public function checkout(Request $request)
    {
        try {
            $validatedData = $request->validate([
                'posCode' => 'required|string|unique:receipts,receipt_number',
                'total' => 'required|numeric|min:0',
                'method' => 'required|string|in:pos,cash,card,transfer',
                'cashPaid' => 'nullable|numeric|min:0',
                'change' => 'required|numeric|min:0',
                'items' => 'required|array|min:1',
                'items.*.productId' => 'required|integer|exists:inventories,id',
                'items.*.name' => 'required|string|max:255',
                'items.*.quantity' => 'required|integer|min:1',
                'items.*.price' => 'required|numeric|min:0',
                'date' => 'required|date',
            ]);

            // while (Receipt::where('receipt_number', $validatedData['posCode'])->exists()) {
            //     $validatedData['posCode'] = 'POS-' . now()->format('YmdHis') . '-' . rand(1000, 9999);
            // }

            return DB::transaction(function () use ($validatedData) {
                $receipt = Receipt::create([
                    'receipt_number' => $validatedData['posCode'],
                    'total_price' => $validatedData['total'],
                    'payment_method' => $validatedData['method'],
                    'cash_paid' => $validatedData['cashPaid'] ?? 0,
                    'change_due' => $validatedData['change'],
                    'date' => $validatedData['date'],
                ]);

                foreach ($validatedData['items'] as $item) {
                    Sales::create([
                        'product_id' => $item['productId'],
                        'product_name' => $item['name'],
                        'quantity' => $item['quantity'],
                        'price' => $item['price'],
                        'receipt_id' => $receipt->id,
                    ]);
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Checkout successful',
                    'orderId' => $receipt->id,
                    'receipt_number' => $receipt->receipt_number,
                    'total_price' => $receipt->total_price,
                    'payment_method' => $receipt->payment_method,
                    'amount_paid' => $receipt->amount_paid,
                    'change_due' => $receipt->change_due,
                ], 201);
            });
        } catch (\Throwable $e) {
            Log::error('Checkout Error: ', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred during checkout. Please try again.',
                'error' => 'Internal Server Error',
            ], 500);
        }
    }


    public function generatePosCode()
    {
        $posCode = 'POS-' . now()->format('YmdHis') . '-' . rand(1000, 9999);

        return response()->json([
            'success' => true,
            'posCode' => $posCode
        ]);
    }

    public function getSales(Request $request)
    {
        try {
            $SalesData = Sales::all()->makeHidden(['receipt_id']);

            if ($SalesData->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'No record data found'
                ], 404);
            }
            return response()->json([
                'success' => true,
                'message' => 'Sales data retrieved successfully',
                'data' => $SalesData
            ], 200);
        } catch (\Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve sales data',
                'error' => $exception->getMessage()
            ], 500);
        }
    }

    public function getSalesByReceipt($receiptNumber)
    {
        try {
            $receipt = Receipt::where('receipt_number', $receiptNumber)->with('sales')->first();

            if (!$receipt) {
                return response()->json([
                    'success' => false,
                    'message' => 'Receipt not found',
                ], 404);
            }

            $salesData = $receipt->sales->makeHidden(['receipt_id']);

            return response()->json([
                'success' => true,
                'message' => 'Sales retrieved successfully',
                'receipt' => [
                    'receipt_number' => $receipt->receipt_number,
                    'total_price' => $receipt->total_price,
                    'items' => $salesData,
                ],
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving sales',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
