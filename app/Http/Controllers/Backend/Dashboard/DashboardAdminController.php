<?php

namespace App\Http\Controllers\Backend\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Expense;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardAdminController extends Controller
{
    public function index()
    {
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfMonth();

        // 1. Layanan Laundry Non-Aktif (Real-time)
        $inactiveServices = Service::with('category')
            ->where('is_active', false)
            ->get();

        // 2. Layanan Laundry Terlaris (Bulan Ini) - Real-time
        $topProducts = OrderDetail::with(['service.category'])
            ->whereNotNull('service_id')
            ->whereHas('order', function ($q) use ($monthStart, $monthEnd) {
                $q->whereBetween('created_at', [$monthStart, $monthEnd])
                    ->where('payment_status', 'paid');
            })
            ->select('service_id', DB::raw('SUM(qty) as total_qty'), DB::raw('SUM(subtotal) as total_revenue'))
            ->groupBy('service_id')
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        // 3. Data Grafik Penjualan vs Target (Bulan Ini) - Real-time
        // Ambil total penjualan per hari
        $actualSales = Order::whereBetween('created_at', [$monthStart, $monthEnd])
            ->where('payment_status', 'paid')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(grand_total) as total'))
            ->groupBy(DB::raw('DATE(created_at)'))
            ->pluck('total', 'date');

        // Ambil target per hari
        $targets = collect();

        $dates = [];
        $salesSeries = [];
        $targetSeries = [];

        // Looping dari tanggal 1 sampai hari ini
        for ($date = $monthStart->copy(); $date->lte(Carbon::now()); $date->addDay()) {
            $dateString = $date->format('Y-m-d');
            $dates[] = $date->format('d M'); 
            $salesSeries[] = (int) $actualSales->get($dateString, 0);
            $targetSeries[] = (int) $targets->get($dateString, 0);
        }

        $chartData = [
            'categories' => $dates,
            'sales'      => $salesSeries,
            'targets'    => $targetSeries,
        ];

        // 4. Quick Summary Widget - Real-time
        $revenue = Order::whereBetween('created_at', [$monthStart, $monthEnd])
            ->where('payment_status', 'paid')
            ->sum('grand_total');

        $expense = Expense::whereBetween('date', [$monthStart->format('Y-m-d'), $monthEnd->format('Y-m-d')])->sum('amount');

        $itemsSold = OrderDetail::whereNotNull('service_id')
            ->whereHas('order', function ($q) use ($monthStart, $monthEnd) {
                $q->whereBetween('created_at', [$monthStart, $monthEnd])
                    ->where('payment_status', 'paid');
            })->sum('qty');

        $summary = [
            'revenue'    => $revenue,
            'expense'    => $expense,
            'items_sold' => $itemsSold,
            'profit'     => $revenue - $expense
        ];

        return view('backend.dashboard.index', compact('inactiveServices', 'topProducts', 'chartData', 'summary'));
    }
}