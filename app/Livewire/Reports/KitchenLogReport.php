<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\OrderKitchenLog;

class KitchenLogReport extends Component
{
    use WithPagination;

    public $start_date;
    public $end_date;

    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
        $this->start_date = now()->format('Y-m-d');
        $this->end_date = now()->format('Y-m-d');
    }

    public function render()
    {
        $kitchenLogs = OrderKitchenLog::with(['sale', 'approvedBy'])
            ->where('approved_by', 1)
            ->whereDate('created_at', '>=', $this->start_date)
            ->whereDate('created_at', '<=', $this->end_date)
            ->latest()
            ->paginate(20);

        return view('livewire.reports.kitchen-log-report', [
            'kitchenLogs' => $kitchenLogs
        ]);
    }
}
