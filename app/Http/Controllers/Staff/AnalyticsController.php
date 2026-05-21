<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Analytics\AnalyticsService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class AnalyticsController extends Controller
{
    public function __construct(private AnalyticsService $analyticsService)
    {
        $this->middleware(['auth', 'role:admin,organizer']);
    }

    public function index()
    {
        $stats = $this->analyticsService->getOverview();
        $chartData = $this->analyticsService->getDailySales(14);
        $recentOrders = $this->analyticsService->getRecentOrders();
        $recentValidations = $this->analyticsService->getRecentValidations();

        return view('staff.analytics.index', compact('stats', 'chartData', 'recentOrders', 'recentValidations'));
    }

    public function exportCsv(): Response
    {
        $orders = Order::where('payment_status', 'completed')
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'analytics-export-' . now()->format('Y-m-d') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($orders) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['ID', 'UUID', 'Client', 'Email', 'Quantite', 'Montant', 'Date']);

            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->id,
                    $order->uuid,
                    $order->customer_name,
                    $order->customer_email,
                    $order->quantity,
                    $order->total_amount,
                    $order->created_at->format('d/m/Y H:i'),
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function exportPdf()
    {
        $stats = $this->analyticsService->getOverview();
        $orders = $this->analyticsService->getRecentOrders(100);

        $pdf = Pdf::loadView('staff.analytics.pdf', compact('stats', 'orders'));
        return $pdf->download('analytics-report-' . now()->format('Y-m-d') . '.pdf');
    }
}