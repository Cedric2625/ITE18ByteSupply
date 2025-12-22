<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Buyer;
use App\Models\HardwareComponent;
use App\Models\Supplier;
use App\Models\Category;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'orders' => Order::count(),
            'buyers' => Buyer::count(),
            'components' => HardwareComponent::count(),
            'suppliers' => Supplier::count(),
            'categories' => Category::count(),
        ];

        $recentOrders = Order::with(['buyer'])
            ->latest('created_at')
            ->take(10)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentOrders'));
    }
}


