<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Session;
use App\Services\ReportService;

class ReportController extends Controller
{
    public function __construct(private ReportService $reportService) {}

    public function index()
    {
        $sessions = Session::with('classRoom')
            ->orderByDesc('date')
            ->paginate(20);

        return view('admin.reports.index', compact('sessions'));
    }

    public function exportPdf(Session $session)
    {
        return $this->reportService->generatePdf($session);
    }

    public function exportCsv(Session $session)
    {
        return $this->reportService->generateCsv($session);
    }
}
