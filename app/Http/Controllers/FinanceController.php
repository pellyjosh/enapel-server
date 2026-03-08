<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Api\Expenses;
use App\Models\Api\Sales;
use Illuminate\Http\Request;

class FinanceController extends Controller
{
    public function Expenses(Request $request){
            $validatedData = $request->validate([
                'type' => 'required|string|max:255',
                'amount' => 'required|numeric|min:0',
            ]);
             Expenses::create([
                'type' => $validatedData['type'],
                'amount' => $validatedData['amount'],
            ]);
            return redirect()->route('expenses')->with('success', 'Expenses added successfully!');

    }
    public function DailyFinance(Request $request)
    {
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
        return view('user.reports.finance', ['finance' => $financeSummary]);

    }
    public function syncFinance(Request $request)
    {
        $salesDates = Sales::selectRaw('DATE(created_at) as date')->distinct()->pluck('date');
        $expensesDates = Expenses::selectRaw('DATE(created_at) as date')->distinct()->pluck('date');

        $allDates = $salesDates->merge($expensesDates)->unique()->sort();


        $financeSummary = [];

        foreach ($allDates as $date) {
            $totalSales = Sales::whereDate('created_at', $date)->sum('price');
            $totalExpenses = Expenses::whereDate('created_at', $date)->sum('amount');
            $netprofit = $totalSales - $totalExpenses;

            if ($totalSales > 0 || $totalExpenses > 0) {
                $financeSummary[] = [
                    'revenue' => $totalSales > 0 ? $totalSales : null,
                    'expenses' => $totalExpenses > 0 ? $totalExpenses : null,
                    'netprofit' => $netprofit,
                    'date' => $date,
                ];
            }
        }
        return view('sync.Sync_finance', ['finance' => $financeSummary]);
    }
}
