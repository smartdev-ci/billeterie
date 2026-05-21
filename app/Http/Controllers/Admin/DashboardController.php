<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Order;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $event = Event::firstOrFail();
        
        $stats = [
            'tickets_sold'      => $event->tickets_sold,
            'tickets_remaining' => max(0, $event->max_tickets - $event->tickets_sold),
            'revenue'           => Order::where('payment_status', 'completed')->sum('total_amount'),
            'total_orders'      => Order::where('payment_status', 'completed')->count(),
        ];

        // Données pour le graphique (7 derniers jours)
        $chartData = Order::where('payment_status', 'completed')
            ->where('created_at', '>=', now()->subDays(7))
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as revenue, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $labels = collect();
        $revenues = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $labels->push(now()->subDays($i)->format('d M'));
            $revenues->push($chartData->has($date) ? (int) $chartData[$date]->revenue : 0);
        }

        $recentOrders = Order::select('uuid', 'customer_name', 'customer_email', 'total_amount', 'payment_status', 'created_at')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'labels', 'revenues', 'recentOrders'));
    }
}