<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $query = Order::with('user')
            ->select('id', 'uuid', 'customer_name', 'customer_email', 'quantity', 'total_amount', 'payment_status', 'mobile_provider', 'created_at')
            ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('payment_status', $request->status);
        }
        if ($request->filled('search')) {
            $term = $request->search;
            $query->where(function ($q) use ($term) {
                $q->where('customer_name', 'like', "%{$term}%")
                  ->orWhere('customer_email', 'like', "%{$term}%")
                  ->orWhere('uuid', 'like', "%{$term}%");
            });
        }

        return view('admin.orders.index', [
            'orders' => $query->paginate(25)->withQueryString(),
            'status' => $request->status,
            'search' => $request->search,
        ]);
    }
}