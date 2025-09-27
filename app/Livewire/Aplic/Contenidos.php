<?php

namespace App\Livewire\Aplic;

use Livewire\Component;
use Livewire\Attributes\On;

class Contenidos extends Component
{

    public $seleccion = null; // guardará qué sección mostrar

    #[On('actualizarContenido')] 
    public function actualizarContenido($valor)
    {
        $this->seleccion = $valor;
    }
    
    
    public function render()
    {
        return view('livewire.aplic.contenidos');
    }
}
