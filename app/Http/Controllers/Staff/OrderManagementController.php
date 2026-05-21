<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class OrderManagementController extends Controller
{
    public function index(Request $request): View
    {
        $query = Order::with(['tickets.event'])
            ->select('id', 'uuid', 'customer_name', 'customer_email', 'customer_phone', 'quantity', 'total_amount', 'payment_status', 'mobile_provider', 'created_at')
            ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('payment_status', $request->status);
        }
        if ($request->filled('provider')) {
            $query->where('mobile_provider', $request->provider);
        }
        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('customer_name', 'like', "%{$term}%")
                    ->orWhere('customer_email', 'like', "%{$term}%")
                    ->orWhere('uuid', 'like', "%{$term}%")
                    ->orWhere('customer_phone', 'like', "%{$term}%");
            });
        }

        return view('staff.orders.index', [
            'orders' => $query->paginate(25)->withQueryString(),
            'status' => $request->status,
            'provider' => $request->provider,
            'search' => $request->search,
        ]);
    }

    public function exportCsv(): StreamedResponse
    {
        $orders = Order::select('uuid', 'customer_name', 'customer_email', 'customer_phone', 'quantity', 'total_amount', 'payment_status', 'mobile_provider', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get();

        $filename = 'commandes-' . now()->format('Y-m-d-Hi') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($orders) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['UUID', 'Nom', 'Email', 'Telephone', 'Quantite', 'Montant', 'Statut', 'Provider', 'Date']);
            foreach ($orders as $o) {
                fputcsv($file, [
                    $o->uuid,
                    $o->customer_name,
                    $o->customer_email,
                    $o->customer_phone,
                    $o->quantity,
                    $o->total_amount,
                    $o->payment_status,
                    $o->mobile_provider ?? '-',
                    $o->created_at->format('d/m/Y H:i')
                ]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
