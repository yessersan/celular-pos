<?php

namespace App\Livewire;

use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

class SalesReport extends Component
{
    public string $period = 'today';
    public string $search = '';
    public Collection $sales;
    public float $total = 0;

    public function mount(): void
    {
        $this->sales = new Collection();
        $this->loadReport();
    }

    public function updatedPeriod(): void
    {
        $this->loadReport();
    }

    public function updatedSearch(): void
    {
        $this->loadReport();
    }

    public function loadReport(): void
    {
        if (! in_array($this->period, ['today', 'week', 'month', 'all'], true)) {
            $this->period = 'today';
        }

        $query = Sale::with('items');
        switch ($this->period) {
            case 'today':
                $query->whereDate('created_at', Carbon::today());
                break;
            case 'week':
                $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
                break;
            case 'month':
                $query->whereMonth('created_at', Carbon::now()->month)->whereYear('created_at', Carbon::now()->year);
                break;
        }

        $term = trim($this->search);
        if ($term !== '') {
            $query->where(function ($builder) use ($term) {
                $builder->where('receipt_number', 'like', '%' . $term . '%')
                    ->orWhere('customer_name', 'like', '%' . $term . '%')
                    ->orWhere('customer_document_number', 'like', '%' . $term . '%');
            });
        }

        $this->sales = $query->orderByDesc('created_at')->get();
        $this->total = (float) $this->sales->sum(fn ($sale) => (float) $sale->total);
    }

    public function render()
    {
        return view('livewire.sales-report');
    }
}
