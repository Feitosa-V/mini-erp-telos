<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\User;
use App\Models\Supplier;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index()
    {
        $dashboardData = Cache::remember('dashboard_data', now()->addMinutes(30), function () {
            return [
                'totalOrders' => Order::where('created_at', '>=', now()->subDays(30))->count(),
                'totalUsers' => User::count(),
                'totalSuppliers' => Supplier::count(),
                'totalSales' => Order::where('created_at', '>=', now()->subDays(30))->sum('total_value')
            ];
        });
    
        return inertia('Dashboard', $dashboardData);
    }
}
