<?php

namespace App\Services\Analytics;

use App\Models\Event;
use App\Models\Order;
use App\Models\Ticket;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    public function getOverview(): array
    {
        return Cache::remember('analytics.overview', 300, function () {
            $event = Event::current();
            $ticketsSold = $event ? $event->tickets_sold : 0;
            $maxTickets = $event ? $event->max_tickets : 0;
            $revenue = Order::where('payment_status', 'completed')->sum('total_amount');
            $totalOrders = Order::where('payment_status', 'completed')->count();

            return [
                'tickets_sold'      => $ticketsSold,
                'tickets_remaining' => max(0, $maxTickets - $ticketsSold),
                'revenue'           => $revenue,
                'total_orders'      => $totalOrders,
            ];
        });
    }

    public function getDailySales(int $days = 14): array
    {
        return Cache::remember("analytics.daily.{$days}", 300, function () use ($days) {
            $rawData = Order::where('payment_status', 'completed')
                ->where('created_at', '>=', now()->subDays($days))
                ->selectRaw('DATE(created_at) as date, COUNT(*) as count, SUM(total_amount) as revenue')
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->keyBy('date');

            $labels = collect();
            $sales  = collect();
            $rev    = collect();

            for ($i = $days - 1; $i >= 0; $i--) {
                $date = now()->subDays($i);
                $labels->push($date->format('d M'));

                $match = $rawData->get($date->format('Y-m-d'));
                $sales->push($match ? (int) $match->count : 0);
                $rev->push($match ? (int) $match->revenue : 0);
            }

            return [
                'labels'   => $labels,
                'sales'    => $sales,
                'revenues' => $rev,
            ];
        });
    }

    public function getRecentOrders(int $limit = 10)
    {
        return Order::select('id', 'uuid', 'customer_name', 'customer_email', 'total_amount', 'created_at')
            ->where('payment_status', 'completed')
            ->latest()
            ->take($limit)
            ->get();
    }

    public function getRecentValidations(int $limit = 10)
    {
        return Ticket::with(['validator:id,name', 'event:id,name'])
            ->select('id', 'uuid', 'customer_email', 'used_at', 'validated_by', 'event_id')
            ->where('status', 'used')
            ->whereNotNull('used_at')
            ->latest('used_at')
            ->take($limit)
            ->get();
    }

    public function clearCache(): void
    {
        Cache::forget('analytics.overview');
        Cache::forget('analytics.daily.14');
    }
}