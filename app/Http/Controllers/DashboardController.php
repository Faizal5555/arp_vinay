<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Client;


class DashboardController extends Controller
{
    //
    public function index(Request $request)
    {
        $fy = $request->fy;
        $quarter = $request->quarter;
    
        // Support "2025-26 & Q3" in FY & Quarter input
        if ($quarter && str_contains($quarter, '&')) {
            [$fyFromQuarter, $quarterFromQuarter] = array_map('trim', explode('&', $quarter));
            $fy = $fy ?? $fyFromQuarter;
            $quarter = $quarterFromQuarter;
        }
    
        $fy = $fy ? trim($fy) : null;
        $quarter = trim($quarter);
        $quarter = $quarter === '' ? null : strtoupper($quarter);
    
        $fyFrom = $request->fy_from;
        $fyTo = $request->fy_to;
    
        // Only apply quarterFilter for multi-year mode
        $multiYearMode = $fyFrom && $fyTo;
        $quarterFilter = $multiYearMode && $request->quarter ? strtoupper(trim($request->quarter)) : null;
    
        // Initialize base queries for current & pending projects
        $currentQuery = DB::table('current_projects');
        $pendingQuery = DB::table('pending_projects') 
            ->whereNotNull('fy')
          ->whereNotNull('quarter');
    
        if ($fy) {
            $currentQuery->where('fy', $fy);
            $pendingQuery->where('fy', $fy);
        }
    
        if ($quarter && !$multiYearMode) {
            $currentQuery->where('quarter', $quarter);
            $pendingQuery->where('quarter', $quarter);
        }
    
        // Build FY range list (e.g., 2023-24 to 2025-26)
        $fyRange = collect();
       if ($multiYearMode) {
    // Fix: Extract numeric start years correctly from "FY 24-25"
        $fyStartYear = (int) ('20' . substr(preg_replace('/[^0-9]/', '', $fyFrom), 0, 2));
        $fyEndYear = (int) ('20' . substr(preg_replace('/[^0-9]/', '', $fyTo), 0, 2));

        for ($y = $fyStartYear; $y <= $fyEndYear; $y++) {
            $fyLabel = 'FY ' . substr($y, 2, 2) . '-' . substr($y + 1, 2, 2);
            $fyRange->push($fyLabel);
        }
    }

    
        // Project counts
        $projectQuery = DB::table('current_projects')
        ->select('client_id');
        
        if ($fy) {
            $projectQuery->where('fy', $fy);
        }
        if ($quarter && !$multiYearMode) {
            $projectQuery->where('quarter', $quarter);
        }
        
        $pendingProjects = DB::table('pending_projects')
            ->select('client_id');
        
        if ($fy) {
            $pendingProjects->where('fy', $fy);
        }
        if ($quarter && !$multiYearMode) {
            $pendingProjects->where('quarter', $quarter);
        }
        
        // Union both
        $projectClientIds = $projectQuery->pluck('client_id');
        $pendingClientIds = $pendingProjects->pluck('client_id');
        
        $allClientIds = $projectClientIds->merge($pendingClientIds)->unique()->filter();
        
        
        // Get unique clients
        $totalClientCount = Client::count();

        // Filtered clients (based on FY and Quarter like your projects)
        $filteredClientCount = Client::whereIn('id', $allClientIds)->count();
        $currentProjectCount = $currentQuery->count();
      // Clone the unfiltered pendingQuery for counting pending projects
$pendingProjectCount = (clone $pendingQuery)
    ->whereIn('invoice_status', ['Pending', 'Partial'])
    ->count();

// Clone again for closed project count
$closedProjectCount = (clone $pendingQuery)
    ->whereIn('invoice_status', ['Paid', 'Waveoff'])
    ->count();

    
        // Totals (based on FY + Quarter)
        $currencyTotal = $pendingQuery->sum('currency_amount') + $currentQuery->sum('currency_amount');
        $originalRevenueTotal = $pendingQuery->sum('original_revenue') + $currentQuery->sum('original_revenue');
        $marginTotal = $pendingQuery->sum('margin') + $currentQuery->sum('margin');
        $invoiceAmountTotal = $pendingQuery->sum('final_invoice_amount') + $currentQuery->sum('final_invoice_amount');
    
        // Quarter-wise breakdown for a single FY
        $quarterWiseData = collect(['Q1', 'Q2', 'Q3', 'Q4'])->mapWithKeys(function ($q) use ($fy) {
            $current = DB::table('current_projects')->where('fy', $fy)->where('quarter', $q)
                ->selectRaw('
                    SUM(currency_amount) as currency,
                    SUM(original_revenue) as revenue,
                    SUM(margin) as margin,
                    SUM(final_invoice_amount) as invoice
                ')->first();
    
            $pending = DB::table('pending_projects')->where('fy', $fy)->where('quarter', $q)
                ->selectRaw('
                    SUM(currency_amount) as currency,
                    SUM(original_revenue) as revenue,
                    SUM(margin) as margin,
                    SUM(final_invoice_amount) as invoice
                ')->first();
    
            return [$q => [
                'Currency' => ($current->currency ?? 0) + ($pending->currency ?? 0),
                'Booking Revenue'  => ($current->revenue ?? 0) + ($pending->revenue ?? 0),
                'Actual Business Margins'   => ($current->margin ?? 0) + ($pending->margin ?? 0),
                'Total Invoice Value In USD'  => ($current->invoice ?? 0) + ($pending->invoice ?? 0),
            ]];
        });
    
        // Previous vs Current Quarter Comparison (optional future use)
        $latestFy = $fy ?? now()->year;
        $latestQuarter = $quarter ?? 'Q1';
    
        $previousData = DB::table('pending_projects')
            ->where('fy', $latestFy)
            ->where('quarter', '<', $latestQuarter)
            ->selectRaw('
                SUM(currency_amount) as prev_currency,
                SUM(original_revenue) as prev_revenue,
                SUM(final_invoice_amount) as prev_invoice
            ')
            ->first();
    
        $currentData = DB::table('pending_projects')
            ->where('fy', $latestFy)
            ->where('quarter', $latestQuarter)
            ->selectRaw('
                SUM(currency_amount) as curr_currency,
                SUM(original_revenue) as curr_revenue,
                SUM(final_invoice_amount) as curr_invoice
            ')
            ->first();
    
        // Define full year mode: single FY with no quarter
        $fullYearMode = $fy && !$quarter;
    
        // Multi-year data build
        $multiYearData = collect();
        if ($multiYearMode) {
            foreach ($fyRange as $fyItem) {
                $current = DB::table('current_projects')
                    ->where('fy', $fyItem)
                    ->when($quarterFilter, fn($q) => $q->where('quarter', $quarterFilter))
                    ->selectRaw('
                        SUM(currency_amount) as currency,
                        SUM(original_revenue) as revenue,
                        SUM(margin) as margin,
                        SUM(final_invoice_amount) as invoice
                    ')->first();
    
                $pending = DB::table('pending_projects')
                    ->where('fy', $fyItem)
                    ->when($quarterFilter, fn($q) => $q->where('quarter', $quarterFilter))
                    ->selectRaw('
                        SUM(currency_amount) as currency,
                        SUM(original_revenue) as revenue,
                        SUM(margin) as margin,
                        SUM(final_invoice_amount) as invoice
                    ')->first();
    
                $multiYearData[$fyItem] = [
                    'Currency' => ($current->currency ?? 0) + ($pending->currency ?? 0),
                    'Booking Revenue'  => ($current->revenue ?? 0) + ($pending->revenue ?? 0),
                    'Actual Business Margins'   => ($current->margin ?? 0) + ($pending->margin ?? 0),
                    'Total Invoice Value In USD'  => ($current->invoice ?? 0) + ($pending->invoice ?? 0),
                ];
            }
        }
    
        return view('admin.dashboard', compact(
            'fy', 'quarter',
            'totalClientCount',
            'filteredClientCount',
            'currentProjectCount',
            'pendingProjectCount',
            'closedProjectCount',
            'currencyTotal',
            'originalRevenueTotal',
            'marginTotal',
            'invoiceAmountTotal',
            'previousData',
            'currentData',
            'quarterWiseData',
            'fullYearMode',
            'fyFrom',
            'fyTo',
            'multiYearMode',
            'multiYearData',
            'quarterFilter'
        ));
    }
    

}
