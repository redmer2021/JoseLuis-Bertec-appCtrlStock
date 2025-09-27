<?php

namespace App\Livewire\Aplic;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Listingresos extends Component
{
    use WithPagination;

    public function render()
    {
        $query = DB::table('bertec_01_stock_ingresos');
        // if ($this->buscar1 != '' && $this->buscar2 == 0) {
        //     $query->where(function($q) {
        //         $q->where('nombreEncuestador', 'like', '%' . $this->buscar1 . '%')
        //           ->orWhere('nroTelef', 'like', '%' . $this->buscar1 . '%');
        //     });
        // } elseif ($this->buscar1 == '' && $this->buscar2 > 0) {
        //     $query->where('estado', $this->buscar2);
        // } elseif ($this->buscar1 != '' && $this->buscar2 > 0) {
        //     $query->where(function($q) {
        //         $q->where('nombreEncuestador', 'like', '%' . $this->buscar1 . '%')
        //           ->orWhere('nroTelef', 'like', '%' . $this->buscar1 . '%');
        //     })
        //     ->where('estado', $this->buscar2);
        // }
        
        $lista = $query->orderByDesc('cod_artic')->paginate(22);

        return view('livewire.aplic.listingresos', ['lista' => $lista]);
    }
}
