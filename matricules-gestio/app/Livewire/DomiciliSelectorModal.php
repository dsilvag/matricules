<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Dwelling;

class DomiciliSelector extends Component
{

    public function render()
    {
        return view('livewire.domicili-selector-modal', [
            'users' => Dwelling::query()->paginate(10),
        ]);
    }

}
