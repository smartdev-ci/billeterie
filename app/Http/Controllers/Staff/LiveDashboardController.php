<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Services\Staff\LiveStatsService;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class LiveDashboardController extends Controller
{
    public function __construct(
        private LiveStatsService $statsService
    ) {}

    /**
     * Affiche le dashboard live avec les métriques initiales.
     */
    public function index(): View
    {
        $metrics = $this->statsService->getMetrics();
        return view('staff.dashboard.live', compact('metrics'));
    }

    /**
     * Endpoint JSON pour le polling temps réel (Alpine.js / fetch).
     */
    public function stats(): JsonResponse
    {
        return response()->json($this->statsService->getMetrics());
    }
}