<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use App\Models\CurrentProject;
use App\Models\PendingProject;

class FilteredProjectsExport implements FromView
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function view(): View
    {
        $current = CurrentProject::with('client');
        $pending = PendingProject::with('client');

        foreach (['fy', 'quarter', 'client_id', 'company_name', 'invoice_status'] as $field) {
            if (!empty($this->filters[$field])) {
                $current->where($field, 'like', "%{$this->filters[$field]}%");
                $pending->where($field, 'like', "%{$this->filters[$field]}%");
            }
        }

        foreach (['pn_no', 'supplier_name'] as $field) {
            if (!empty($this->filters[$field])) {
                $current->where($field, 'like', "%{$this->filters[$field]}%");
                $pending->where($field, 'like', "%{$this->filters[$field]}%");
            }
        }

        $projects = $current->get()->merge($pending->get());

        return view('search_projects.export_view', ['projects' => $projects]);
    }
}
