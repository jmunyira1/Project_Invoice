<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $org = $this->org();
        $orgId = $org->id;
        $currency = $org->currency;

        // ── KPI cards ──────────────────────────────────────────────

        // Total revenue collected (all payments ever)
        $totalRevenue = DB::table('payments')
            ->where('organisation_id', $orgId)
            ->sum('amount');

        // Outstanding balance = sum of invoice totals - payments on those invoices
        // We compute per document then sum
        $outstanding = DB::table('documents as d')
            ->where('d.organisation_id', $orgId)
            ->whereIn('d.type', ['invoice', 'quote'])
            ->whereNull('d.sent_at', 'and', false) // sent docs only
            ->join(
                DB::raw('(SELECT document_id, SUM(total_price) as total FROM document_lines GROUP BY document_id) as dl'),
                'dl.document_id', '=', 'd.id'
            )
            ->leftJoin(
                DB::raw('(SELECT document_id, SUM(amount) as paid FROM payments WHERE document_id IS NOT NULL GROUP BY document_id) as p'),
                'p.document_id', '=', 'd.id'
            )
            ->selectRaw('SUM(dl.total - COALESCE(p.paid, 0)) as balance')
            ->value('balance') ?? 0;

        // Active projects count
        $activeProjects = DB::table('projects')
            ->where('organisation_id', $orgId)
            ->whereIn('status', ['active', 'quoted'])
            ->count();

        // Total clients
        $totalClients = DB::table('clients')
            ->where('organisation_id', $orgId)
            ->count();

        // This month's revenue
        $monthRevenue = DB::table('payments')
            ->where('organisation_id', $orgId)
            ->whereYear('paid_on', now()->year)
            ->whereMonth('paid_on', now()->month)
            ->sum('amount');

        // Project status breakdown
        $projectStatusCounts = DB::table('projects')
            ->where('organisation_id', $orgId)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        // ── Recent data ────────────────────────────────────────────

        $recentProjects = $org->projects()
            ->with('client')
            ->latest()
            ->take(6)
            ->get();

        $recentPayments = DB::table('payments as p')
            ->where('p.organisation_id', $orgId)
            ->join('projects as pr', 'pr.id', '=', 'p.project_id')
            ->join('clients as c', 'c.id', '=', 'pr.client_id')
            ->select('p.*', 'pr.title as project_title', 'c.name as client_name')
            ->orderByDesc('p.paid_on')
            ->take(5)
            ->get();

        // Unpaid sent invoices
        $unpaidInvoices = DB::table('documents as d')
            ->where('d.organisation_id', $orgId)
            ->where('d.type', 'invoice')
            ->whereNotNull('d.sent_at')
            ->join('projects as pr', 'pr.id', '=', 'd.project_id')
            ->join('clients as c', 'c.id', '=', 'pr.client_id')
            ->join(
                DB::raw('(SELECT document_id, SUM(total_price) as total FROM document_lines GROUP BY document_id) as dl'),
                'dl.document_id', '=', 'd.id'
            )
            ->leftJoin(
                DB::raw('(SELECT document_id, SUM(amount) as paid FROM payments WHERE document_id IS NOT NULL GROUP BY document_id) as p2'),
                'p2.document_id', '=', 'd.id'
            )
            ->selectRaw('d.id, d.number, d.issue_date, d.due_date, c.name as client_name, pr.title as project_title, dl.total, COALESCE(p2.paid, 0) as paid, (dl.total - COALESCE(p2.paid, 0)) as balance')
            ->havingRaw('balance > 0')
            ->orderBy('d.due_date')
            ->take(8)
            ->get();

        return view('dashboard.index', compact(
            'currency',
            'totalRevenue',
            'outstanding',
            'activeProjects',
            'totalClients',
            'monthRevenue',
            'projectStatusCounts',
            'recentProjects',
            'recentPayments',
            'unpaidInvoices',
        ));
    }

    private function org()
    {
        return auth()->user()->organisation;
    }
}
