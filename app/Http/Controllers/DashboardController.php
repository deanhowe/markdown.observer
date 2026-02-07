<?php

namespace App\Http\Controllers;

use Inertia\Inertia;

class DashboardController extends Controller
{
    /**
     * Display the dashboard page.
     *
     * @return \Inertia\Response
     */
    public function index()
    {
        $packages = \App\Models\UserPackage::where('user_id', auth()->id())
            ->with('docs')
            ->get();

        return Inertia::render('Dashboard', ['packages' => $packages]);
    }
}
