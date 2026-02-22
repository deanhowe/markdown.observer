<?php

namespace App\Http\Controllers\AI;

use App\Http\Controllers\Controller;
use Inertia\Inertia;

class HomeController extends Controller
{
    public function index()
    {
        $stats = [
            'collections' => \App\Models\SteeringCollection::count(),
            'documents' => \App\Models\SteeringDoc::count(),
            'repos' => \App\Models\SteeringCollection::distinct('name')->count(),
        ];

        return Inertia::render('AiSteering/Welcome', [
            'stats' => $stats
        ]);
    }
}
