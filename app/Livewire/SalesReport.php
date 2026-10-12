<?php

namespace App\Livewire;

use App\Models\Sale;
use Carbon\Carbon;
use Livewire\Component;
use Illuminate\Database\Eloquent\Collection;

class SalesReport extends Component
{
    public string $period = 'today';
    public string $search = '';
    public string $documentType = '';

    /** @var Collection<int, Sale> */
    public Collection $sales;

    public float $total = 0;

    public function mount(): void
    {
        $this->sales = new Collection();

        $this->loadReport();
    }

    public function updatedPeriod(): void { $this->loadReport(); }
    public function updatedSearch(): void { $this->loadReport(); }
    public function updatedDocumentType(): void { $this->loadReport(); }

    public function loadReport(): void
    {
        // Validar el periodo seleccionado.
        if (! in_array($this->period, ['today', 'week', 'month'], true)) {
            $this->period = 'today';
        }

        $query = Sale::with('items')
            ->when(trim($this->search) !== '', function ($q) {
                $term = trim($this->search);
                $q->where(function ($inner) use ($term) {
                    $inner->where('receipt_number', 'like', '%'.$term.'%')
                        ->orWhere('customer_name', 'like', '%'.$term.'%')
                        ->orWhere('customer_document', 'like', '%'.$term.'%');
                });
            })
            ->when(in_array($this->documentType, ['boleta', 'factura'], true), fn ($q) => $q->where('document_type', $this->documentType));

        switch ($this->period) {
            case 'today':
                $query->whereDate('created_at', Carbon::today());
                break;

            case 'week':
                $query->whereBetween('created_at', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek(),
                ]);
                break;

            case 'month':
                $query->whereMonth('created_at', Carbon::now()->month)
                    ->whereYear('created_at', Carbon::now()->year);
                break;
        }

        $this->sales = $query
            ->orderByDesc('created_at')
            ->get();

        $this->total = (float) $this->sales->sum(
            fn ($sale) => (float) $sale->total
        );
    }

    public function render()
    {
        return view('livewire.sales-report');
    }
}
