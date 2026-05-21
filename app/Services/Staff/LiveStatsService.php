<?php

namespace App\Services\Staff;

use App\Models\Event;
use App\Models\Ticket;
use App\Models\Order;
use Illuminate\Support\Facades\Cache;

class LiveStatsService
{
    public function getMetrics(): array
    {
        return Cache::remember('staff.live_stats', 30, function () {
            $event = Event::first();
            $sold = $event?->tickets_sold ?? 0;
            $max = $event?->max_tickets ?? 1;

            return [
                'sold' => $sold,
                'remaining' => max(0, $max - $sold),
                'fill_rate' => round(($sold / $max) * 100, 1),
                'completed_orders' => Order::where('payment_status', 'completed')->count(),
                'pending_payments' => Order::where('payment_status', 'pending')->count(),
                'recent_validations' => Ticket::where('status', 'used')
                    ->where('used_at', '>=', now()->subHour())
                    ->count(),
                'revenue' => Order::where('payment_status', 'completed')->sum('total_amount'),
            ];
        });
    }

    public function clearCache(): void
    {
        Cache::forget('staff.live_stats');
    }
}