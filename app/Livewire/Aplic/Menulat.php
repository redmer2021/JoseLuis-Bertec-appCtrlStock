<?php

namespace App\Livewire\Aplic;

use Livewire\Component;

class Menulat extends Component
{
    public function actualizarContenido($valor){
        $this->dispatch('actualizarContenido', valor: $valor); 
    }

    public function render()
    {
        return view('livewire.aplic.menulat');
    }
}
