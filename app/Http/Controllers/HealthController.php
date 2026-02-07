<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\SteeringCollection;
use App\Models\SteeringDoc;
use App\Models\UserPackage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Inertia\Inertia;

class HealthController extends Controller
{
    public function index()
    {
        $stats = [
            'users' => [
                'total' => User::count(),
                'free' => User::where('subscription_tier', 'free')->count(),
                'pro' => User::where('subscription_tier', 'pro')->count(),
                'lifetime' => User::where('subscription_tier', 'lifetime')->count(),
                'today' => User::whereDate('created_at', today())->count(),
                'this_week' => User::where('created_at', '>=', now()->subWeek())->count(),
            ],
            
            'steering_docs' => [
                'collections' => SteeringCollection::count(),
                'documents' => SteeringDoc::count(),
                'public' => SteeringCollection::where('is_public', true)->count(),
                'versions_total' => DB::table('steering_doc_versions')->count(),
                'versions_today' => DB::table('steering_doc_versions')->whereDate('created_at', today())->count(),
                'versions_this_week' => DB::table('steering_doc_versions')->where('created_at', '>=', now()->subWeek())->count(),
                'by_type' => SteeringCollection::select('type', DB::raw('count(*) as count'))
                    ->groupBy('type')
                    ->pluck('count', 'type')
                    ->toArray(),
            ],
            
            'packages' => [
                'total' => UserPackage::count(),
                'unique' => UserPackage::distinct('package_name')->count(),
            ],
            
            'queue' => [
                'horizon_status' => $this->getHorizonStatus(),
                'failed_jobs' => DB::table('failed_jobs')->count(),
            ],
            
            'system' => [
                'laravel_version' => app()->version(),
                'php_version' => PHP_VERSION,
                'environment' => app()->environment(),
                'debug' => config('app.debug'),
            ],
        ];

        return Inertia::render('Health', ['stats' => $stats]);
    }

    private function getHorizonStatus()
    {
        try {
            $masters = Cache::get('illuminate:queue:restart');
            return $masters ? 'running' : 'stopped';
        } catch (\Exception $e) {
            return 'unknown';
        }
    }
}
