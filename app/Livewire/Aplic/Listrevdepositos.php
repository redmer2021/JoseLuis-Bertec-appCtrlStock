<?php

namespace App\Livewire\Aplic;

use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use Livewire\Component;

class Listrevdepositos extends Component
{
    use WithPagination;
    public $listaRevDepositos = [];

    public $txtBuscaNroVentas = '';
    public $txtBuscaDescArtic = '';
    public $txtBuscaRazSocial = '';

    public function Buscar1(){
        $this->txtBuscaDescArtic = '';
        $this->txtBuscaRazSocial = '';
        $this->selectDatos();
    }

    public function Buscar2(){
        $this->txtBuscaNroVentas = '';
        $this->txtBuscaRazSocial = '';
        $this->selectDatos();
    }

    public function Buscar3(){
        $this->txtBuscaNroVentas = '';
        $this->txtBuscaDescArtic = '';
        $this->selectDatos();
    }

    public function Editar(){
        dd('editando datos...');
    }

    protected function selectDatos(){
        $listadoFinal = [];
        
        // Normalizar textos (eliminar espacios a ambos lados)
        $this->txtBuscaNroVentas   = trim($this->txtBuscaNroVentas);
        $this->txtBuscaDescArtic  = trim($this->txtBuscaDescArtic);
        $this->txtBuscaRazSocial    = trim($this->txtBuscaRazSocial);

        // Traer compras 
        if ($this->txtBuscaNroVentas!=''){
            $list_ventas = DB::table('bertec_01_pend_entrega')
            ->where('nro_pedido', 'like', '%' . $this->txtBuscaNroVentas . '%')
            ->orderBy('nro_pedido')
            ->get();
        } else if ($this->txtBuscaDescArtic!=''){
            $list_ventas = DB::table('bertec_01_pend_entrega')
            ->where('descrip', 'like', '%' . $this->txtBuscaDescArtic . '%')
            ->orderBy('nro_pedido')
            ->get();
        } else if ($this->txtBuscaRazSocial!=''){
            $list_ventas = DB::table('bertec_01_pend_entrega')
            ->where('raz_social', 'like', '%' . $this->txtBuscaRazSocial . '%')
            ->orderBy('nro_pedido')
            ->get();
        } else {
            $list_ventas = DB::table('bertec_01_pend_entrega')
            ->orderBy('nro_pedido')
            ->limit(100)
            ->get();
        }

        foreach ($list_ventas as $ventas) {
            // Buscar stock del artículo
            $stocks = DB::table('bertec_01_stock_depositos')
                ->selectRaw('SUM(saldo_ctrl_stock) as total_saldo_ctrl_stock')
                ->where('cod_artic', $ventas->cod_artic)
                ->first();

            $listadoFinal[] = [
                'nro_pedido' => $ventas->nro_pedido,
                'cod_artic' => $ventas->cod_artic,
                'descrip' => $ventas->descrip,
                'renglon' => $ventas->renglon,
                'cant_pedida' => $ventas->cant_pedida,
                'pend_desc' => $ventas->pend_desc,
                'saldo_ctrl_stock'  => $stocks->total_saldo_ctrl_stock,
                'femoc' => $compras->fec_emision ?? null,
                'fec_pedido' => $ventas->fec_pedido,
                'plan_entrega' => $ventas->plan_entrega,
                'cod_vend' => $ventas->cod_vend,
                'raz_social' => $ventas->raz_social,
                'nro_o_compra' => $ventas->nro_o_compra,
            ];
        }

        $this->listaRevDepositos = $listadoFinal;
    }

    public function mount()
    {
        $this->selectDatos();
    }


    public function render()
    {
        return view('livewire.aplic.listrevdepositos');
    }
}
