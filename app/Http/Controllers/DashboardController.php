<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Sales;
use App\Models\Category;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $title = "dashboard";

        // Fetch data
        $total_purchases = Purchase::whereDate('expiry_date', '=', Carbon::now())->count();
        $total_categories = Category::count();
        $total_suppliers = Supplier::count();
        $total_sales = Sales::count();

        // Convert data to JSON for JavaScript
        $chartData = [
            'labels' => ['Total Purchases', 'Total Suppliers', 'Total Sales'],
            'values' => [$total_purchases, $total_suppliers, $total_sales],
        ];

        $total_expired_products = Purchase::whereDate('expiry_date', '=', Carbon::now())->count();
        $latest_sales = Sales::whereDate('created_at', '=', Carbon::now())->get();
        $today_sales = Sales::whereDate('created_at', '=', Carbon::now())->sum('total_price');

        return view('dashboard', compact(
            'title',
            'chartData',
            'total_expired_products',
            'latest_sales',
            'today_sales',
            'total_categories'
        ));
    }
}
