<?php

namespace App\Livewire;

use App\Models\Service;
use Illuminate\Support\Collection;
use Livewire\Component;

class ServiceManager extends Component
{
    /** @var Collection<int, Service> */
    public Collection $services;

    public string $name = '';
    public float $price = 0;
    public ?int $editId = null;
    public bool $showForm = false;

    protected array $rules = [
        'name' => 'required|string|max:255',
        'price' => 'required|numeric|min:0',
    ];

    public function mount(): void
    {
        $this->services = collect();
        $this->loadServices();
    }

    public function loadServices(): void
    {
        $this->services = Service::orderBy('name')->get();
    }

    public function save(): void
    {
        $this->validate();

        if ($this->editId) {
            Service::find($this->editId)->update([
                'name' => $this->name,
                'price' => $this->price,
            ]);
        } else {
            Service::create([
                'name' => $this->name,
                'price' => $this->price,
            ]);
        }

        $this->resetForm();
        $this->loadServices();
        session()->flash('message', 'Servicio guardado correctamente.');
    }

    public function edit(int $id): void
    {
        $service = Service::findOrFail($id);
        $this->editId = $service->id;
        $this->name = $service->name;
        $this->price = (float) $service->price;
        $this->showForm = true;
    }

    public function delete(int $id): void
    {
        Service::findOrFail($id)->delete();
        $this->loadServices();
        session()->flash('message', 'Servicio eliminado.');
    }

    public function resetForm(): void
    {
        $this->reset(['name', 'price', 'editId']);
        $this->showForm = false;
    }

    public function render()
    {
        return view('livewire.service-manager');
    }
}