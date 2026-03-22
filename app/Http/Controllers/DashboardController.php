<?php

namespace App\Http\Controllers;

use App\Support\EventPlanner\DashboardData;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request, DashboardData $dashboardData): View
    {
        return view('dashboard', $dashboardData->build($request->user()));
    }
}
