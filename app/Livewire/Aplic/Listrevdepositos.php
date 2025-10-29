<?php

namespace App\Livewire\Aplic;

use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use Livewire\Component;

class Listrevdepositos extends Component
{
    use WithPagination;
    public $listaRevDepositos = [];

    public $verForm = false;

    public $txtBuscaNroVentas = '';
    public $txtBuscaDescArtic = '';
    public $txtBuscaRazSocial = '';
    public $txtBuscaNroOrdenCompra = '';

    public $varComprobante = '';
    public $varCodArticulo = '';
    public $codEstado = 0;
    public $comentarios='';
    public $tipoDeGrabacion;
    public $asignardtos_a = 0;
    public $codCtrl = 2;
    public $codColor = 2;

    public function Buscar1(){
        $this->txtBuscaDescArtic = '';
        $this->txtBuscaRazSocial = '';
        $this->txtBuscaNroOrdenCompra = '';
        $this->selectDatos();
    }

    public function Buscar2(){
        $this->txtBuscaNroVentas = '';
        $this->txtBuscaRazSocial = '';
        $this->txtBuscaNroOrdenCompra = '';
        $this->selectDatos();
    }

    public function Buscar3(){
        $this->txtBuscaNroVentas = '';
        $this->txtBuscaDescArtic = '';
        $this->txtBuscaNroOrdenCompra = '';
        $this->selectDatos();
    }

    public function Buscar4(){
        $this->txtBuscaNroVentas = '';
        $this->txtBuscaDescArtic = '';
        $this->txtBuscaRazSocial = '';
        $this->selectDatos();
    }

    protected function LimpiarCampos(){
        $this->reset([
            'asignardtos_a',
            'varComprobante',
            'varCodArticulo',
            'codEstado',
            'comentarios',
            'codCtrl'
        ]);
    }

    public function Editar($param1, $param2){
        $this->verForm = true;
        $this->LimpiarCampos();
        $this->resetErrorBag();
        $this->varComprobante = $param1;
        $this->varCodArticulo = $param2;

        // Buscar datos de auditoría en bertec_01_control_ventas
        $dtosAudit = DB::table('bertec_01_control_ventas')
            ->select('codEstado','comentarios','codColor', 'codCtrl')
            ->where('nroComprobante', $param1)
            ->where('codArticulo', $param2)
            ->first();

        // $dtosAudit = DB::table('bertec_01_control_depositos')
        //     ->select('codEstado','comentarios', 'codCtrl')
        //     ->where('nroComprobante', $param1)
        //     ->where('codArticulo', $param2)
        //     ->first();

        //por default se pone tipodegrabacion en 2, si no se encuentran datos de auditoria
        //se grabara como un nuevo elemento, si encuentra datos, tipodegrabacion tomará 1
        //y se actualizarán los datos.
        $this->tipoDeGrabacion = 2;
        if ($dtosAudit){
            $this->codEstado = $dtosAudit->codEstado;
            $this->comentarios = $dtosAudit->comentarios;
            $this->codCtrl = $dtosAudit->codCtrl == 1 ? true : false;
            $this->codColor = $dtosAudit->codColor == 1 ? true : false;

            $this->tipoDeGrabacion = 1;
        }
        
    }

    public function GrabarDtos(){
        $validatedData = $this->validate(
            [
                'asignardtos_a' => 'in:1,2',
            ],
            [
                'asignardtos_a.in'=>'Debe seleccionar seleccionar a quien asigna los datos a ingresar',
            ]
        );

        if ($this->tipoDeGrabacion == 2) {
            // === INSERCIÓN ===
            if ($this->asignardtos_a == 1) {
                $articulos = DB::table('bertec_01_pend_entrega')
                    ->where('nro_pedido', $this->varComprobante)
                    ->pluck('cod_artic');
        
                foreach ($articulos as $articulo) {
                    DB::table('bertec_01_control_ventas')->insert([
                        'nroComprobante' => $this->varComprobante,
                        'codArticulo'    => $articulo,
                        'codEstado' => $this->codEstado,
                        'fecModifEstado' => $this->codEstado ? now() : null,
                        'comentarios'    => $this->comentarios ?? null,
                        'user' => auth()->user()->name,
                        'codCtrl' => $this->codCtrl ? 1 : 2,
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ]);
                }
            } else {
                    DB::table('bertec_01_control_ventas')->insert([
                    'nroComprobante' => $this->varComprobante,
                    'codArticulo'    => $this->varCodArticulo,
                    'codEstado' => $this->codEstado,
                    'fecModifEstado' => $this->codEstado ? now() : null,
                    'comentarios'    => $this->comentarios ?? null,
                    'user' => auth()->user()->name,
                    'codCtrl' => $this->codCtrl ? 1 : 2,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            }
        } else {
            // === ACTUALIZACIÓN ===
            if ($this->asignardtos_a == 1) {
                $items = DB::table('bertec_01_control_ventas')
                    ->where('nroComprobante', $this->varComprobante)
                    ->get();
        
                foreach ($items as $item) {
                    $updateData = [
                        'codEstado' => $this->codEstado,
                        'fecModifEstado' => $this->codEstado ? now() : null,
                        'comentarios' => $this->comentarios ?? null,
                        'user' => auth()->user()->name,
                        'codCtrl' => $this->codCtrl ? 1 : 2,
                        'updated_at'  => now(),
                    ];
                                    
                    DB::table('bertec_01_control_ventas')
                        ->where('id', $item->id)
                        ->update($updateData);
                }
            } else {
                $item = DB::table('bertec_01_control_ventas')
                    ->where('nroComprobante', $this->varComprobante)
                    ->where('codArticulo', $this->varCodArticulo)
                    ->first();
        
                $updateData = [
                    'codEstado' => $this->codEstado,
                    'fecModifEstado' => $this->codEstado ? now() : null,
                    'comentarios' => $this->comentarios ?? null,
                    'user' => auth()->user()->name,
                    'codCtrl' => $this->codCtrl ? 1 : 2,
                    'updated_at'  => now(),
                ];
        
                DB::table('bertec_01_control_ventas')
                    ->where('nroComprobante', $this->varComprobante)
                    ->where('codArticulo', $this->varCodArticulo)
                    ->update($updateData);
            }
        }

        $this->LimpiarCampos();
        $this->selectDatos();
        $this->verForm = false;
    }


    public function CancelarEdic(){
        $this->verForm = false;
    }

    protected function selectDatos(){
        $listadoFinal = [];
        
        // Normalizar textos (eliminar espacios a ambos lados)
        $this->txtBuscaNroVentas   = trim($this->txtBuscaNroVentas);
        $this->txtBuscaDescArtic  = trim($this->txtBuscaDescArtic);
        $this->txtBuscaRazSocial    = trim($this->txtBuscaRazSocial);
        $this->txtBuscaNroOrdenCompra    = trim($this->txtBuscaNroOrdenCompra);

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
        } else if ($this->txtBuscaNroOrdenCompra!=''){
            $list_ventas = DB::table('bertec_01_pend_entrega')
            ->where('nro_o_compra', 'like', '%' . $this->txtBuscaNroOrdenCompra . '%')
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

            $dtosAudit = DB::table('bertec_01_control_ventas')
                ->select('codEstado','comentarios','fecModifEstado','user', 'codColor', 'codCtrl')
                ->where('nroComprobante', $ventas->nro_pedido)
                ->where('codArticulo', $ventas->cod_artic)
                ->first();

            $codEstado='';
            $comentarios='';
            $fecModifEstado='';
            $user='';
            $codColor=0;
            $codCtrl=0;

            if ($dtosAudit){
                $codEstado = $dtosAudit->codEstado == 1 ? 'PAD' : 'PAC';
                $comentarios = $dtosAudit->comentarios;
                $fecModifEstado = $dtosAudit->fecModifEstado;
                $user = $dtosAudit->user;
                $codColor = $dtosAudit->codColor;
                $codCtrl = $dtosAudit->codCtrl;
            }

            if ($codColor == 1){
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
    
                    // dtos de auditoria
                    'codEstado' => $codEstado,
                    'comentarios' => $comentarios,
                    'fecModifEstado' => $fecModifEstado,
                    'user' => $user,
                    'codColor' => $codColor,
                    'codCtrl' => $codCtrl
                ];
            }
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
