<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Api\Expenses;
use App\Models\Api\Sales;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    public function Expenses(Request $request){
        try {
            $validatedData = $request->validate([
                'type' => 'required|string|max:255',
                'amount' => 'required|numeric|min:0',
            ]);
            $expenseData = Expenses::create([
                'type' => $validatedData['type'],
                'amount' => $validatedData['amount'],
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Exopenses Data Recorded successfully',
                'data' => $expenseData,
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while recording data',
                'error' => $e->getMessage(),
            ], 500); 
        }
    }
    public function DailyFinance(Request $request)
    {
        try {
            $salesDates = Sales::selectRaw('DATE(created_at) as date')->distinct()->pluck('date');
            $expensesDates = Expenses::selectRaw('DATE(created_at) as date')->distinct()->pluck('date');

            $allDates = $salesDates->merge($expensesDates)->unique()->sort();


            $financeSummary = [];

            foreach ($allDates as $date) {
                $totalSales = Sales::whereDate('created_at', $date)->sum('price');
                $totalExpenses = Expenses::whereDate('created_at', $date)->sum('amount');
                $netprofit= $totalSales - $totalExpenses;

                if ($totalSales > 0 || $totalExpenses > 0) {
                    $financeSummary[] = [
                        'revenue' => $totalSales > 0 ? $totalSales : null,
                        'expenses' => $totalExpenses > 0 ? $totalExpenses : null,
                        'netprofit'=>$netprofit,
                        'date' => $date,
                    ];
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'finance summary retrieved successfully',
                'data' => $financeSummary,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while retrieving the summary',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

}
