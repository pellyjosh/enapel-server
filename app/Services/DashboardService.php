<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Api\Inventory;
use App\Models\Guest;
use App\Models\Room;
use App\Models\PaymentRecord as Sales;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    /**
     * Get aggregated metrics based on enabled modules.
     */
    public function getMetrics(array $modules): array
    {
        $metrics = [
            'overview' => $this->getOverviewMetrics(),
        ];

        if (in_array('hotel', $modules)) {
            $metrics['hotel'] = $this->getHotelMetrics();
        }

        if (in_array('inventory', $modules) || in_array('pharmacy', $modules) || in_array('supermart', $modules)) {
            $metrics['commerce'] = $this->getCommerceMetrics();
        }

        return $metrics;
    }

    private function getOverviewMetrics(): array
    {
        return [
            'total_revenue' => Sales::sum('amount'),
            'today_revenue' => Sales::whereDate('created_at', Carbon::today())->sum('amount'),
            'total_staff' => User::count(),
            'revenue_trend' => $this->getRevenueTrend(),
        ];
    }

    private function getHotelMetrics(): array
    {
        $totalRooms = Room::count();
        $occupiedRooms = Booking::where('is_checked_out', false)->count();

        return [
            'occupancy_rate' => $totalRooms > 0 ? round(($occupiedRooms / $totalRooms) * 100, 1) : 0,
            'active_bookings' => Booking::where('is_checked_out', false)->count(),
            'today_arrivals' => Booking::whereDate('check_in', Carbon::today())->count(),
            'recent_guests' => Guest::latest()->take(5)->get(['name', 'email', 'phone']),
        ];
    }

    private function getCommerceMetrics(): array
    {
        return [
            'low_stock_items' => Inventory::where('quantity', '<=', 10)->count(),
            'total_items' => Inventory::count(),
            'top_selling' => DB::table('sales')
                ->select('product_name', DB::raw('SUM(quantity) as total_sold'))
                ->groupBy('product_name')
                ->orderByDesc('total_sold')
                ->take(5)
                ->get(),
        ];
    }

    private function getRevenueTrend(): array
    {
        return Sales::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(amount) as amount')
        )
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->toArray();
    }
}
