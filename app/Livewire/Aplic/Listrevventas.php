<?php

namespace App\Livewire\Aplic;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class Listrevventas extends Component
{
    use WithPagination;
    public $listaRevVentas = [];

    public $verForm = false;

    public $txtBuscaOrdenComp = '';
    public $txtBuscaNroVentas = '';
    public $txtBuscaDescArtic = '';
    public $txtBuscaRazSocial = '';

    public $ordenarComo1 = 'asc';
    public $ordenarComo2 = 'asc';


    public $varComprobante = '';
    public $varCodArticulo = '';
    public $codEstado = 0;
    public $asignardtos_a = 0;
    public $codColor = 2;
    public $comentarios='';
    public $tipoDeGrabacion;

    public function Reordenar1(){
        if ($this->ordenarComo1 == 'desc'){
            $this->ordenarComo1 = 'asc';
        } else {
            $this->ordenarComo1 = 'desc';
        }
        $this->selectDatos(1);
    }

    public function Reordenar2(){
        if ($this->ordenarComo2 == 'desc'){
            $this->ordenarComo2 = 'asc';
        } else {
            $this->ordenarComo2 = 'desc';
        }
        $this->selectDatos(2);
    }

    public function Buscar1(){
        $this->txtBuscaNroVentas = '';
        $this->txtBuscaDescArtic = '';
        $this->txtBuscaRazSocial = '';
        $this->selectDatos();
    }

    public function Buscar2(){        
        $this->txtBuscaOrdenComp = '';
        $this->txtBuscaDescArtic = '';        
        $this->txtBuscaRazSocial = '';
        $this->selectDatos();
    }

    public function Buscar3(){
        $this->txtBuscaOrdenComp = '';
        $this->txtBuscaNroVentas = '';
        $this->txtBuscaRazSocial = '';
        $this->selectDatos();
    }

    public function Buscar4(){
        $this->txtBuscaOrdenComp = '';
        $this->txtBuscaNroVentas = '';
        $this->txtBuscaDescArtic = '';
        $this->selectDatos();
    }


    protected function LimpiarCampos(){
        $this->reset([
            'asignardtos_a',
            'varComprobante',
            'varCodArticulo',
            'codEstado',
            'comentarios',
            'codColor'
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
            ->select('codEstado','comentarios','codColor')
            ->where('nroComprobante', $param1)
            ->where('codArticulo', $param2)
            ->first();
           
        //por default se pone tipodegrabacion en 2, si no se encuentran datos de auditoria
        //se grabara como un nuevo elemento, si encuentra datos, tipodegrabacion tomará 1
        //y se actualizarán los datos.
        $this->tipoDeGrabacion = 2;
        $this->codColor = false;
        if ($dtosAudit){
            $this->codEstado = $dtosAudit->codEstado;
            $this->comentarios = $dtosAudit->comentarios;
            $this->codColor = $dtosAudit->codColor == 1 ? true : false;
            $this->tipoDeGrabacion = 1;
        }
        
    }


    public function CancelarEdic(){
        $this->verForm = false;
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
                        'codColor' => $this->codColor ? 1 : 2,
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
                    'codColor' => $this->codColor ? 1 : 2,
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
                        'codColor' => $this->codColor ? 1 : 2,
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
                    'codColor' => $this->codColor ? 1 : 2,
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
    
    protected function selectDatos($columOrden = 0){
        $listadoFinal = [];
        
        // Normalizar textos (eliminar espacios a ambos lados)
        $this->txtBuscaOrdenComp   = trim($this->txtBuscaOrdenComp);
        $this->txtBuscaNroVentas   = trim($this->txtBuscaNroVentas);
        $this->txtBuscaDescArtic  = trim($this->txtBuscaDescArtic);
        $this->txtBuscaRazSocial    = trim($this->txtBuscaRazSocial);

        // Traer compras 
        if ($this->txtBuscaOrdenComp!=''){
            $list_ventas = DB::table('bertec_01_pend_entrega')
            ->where('nro_o_compra', 'like', '%' . $this->txtBuscaOrdenComp . '%')
            ->orderBy('nro_pedido')
            ->get();
        } else if ($this->txtBuscaNroVentas!=''){
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
            // ->limit(100)
            ->get();
        }

        foreach ($list_ventas as $ventas) {
            // Buscar stock del artículo
            $stocks = DB::table('bertec_01_stock_depositos')
                ->selectRaw('SUM(saldo_ctrl_stock) as total_saldo_ctrl_stock, SUM(cant_comp_stock) as total_cant_comp_stock')
                ->where('cod_artic', $ventas->cod_artic)
                ->first();

            $total_pendiente = DB::table('bertec_01_compras_pend')
                ->where('cod_artic', $ventas->cod_artic)
                ->sum('cant_pendiente');

            $faltante = max(0, $stocks->total_cant_comp_stock - $stocks->total_saldo_ctrl_stock - $total_pendiente);

            $aRecibir = DB::table('bertec_01_compras_pend')
                ->where('cod_artic', $ventas->cod_artic)
                ->sum('cant_pedida');

            $notasCompras = DB::table('bertec_01_control_compras')
                ->select('fecCompra1', 'fecModif', 'comentarios1')
                ->where('codArticulo', $ventas->cod_artic)
                ->orderBy('fecCompra1', 'asc')
                ->limit(1)->first();
            
            $compras_feccompra='';
            $compras_fecmodif='';
            $compras_comentrarios='';

            if ($notasCompras){
                $compras_feccompra = $notasCompras->fecCompra1;
                $compras_fecmodif = $notasCompras->fecModif;
                $compras_comentrarios = $notasCompras->comentarios1;
            }

            $impoDolariz = ($ventas->cotiza && $ventas->cotiza != 0)
            ? $ventas->importe / $ventas->cotiza
            : 0;

            // Buscar datos de auditoría en bertec_01_control_ventas
            $dtosAudit = DB::table('bertec_01_control_ventas')
                ->select('codEstado','comentarios','fecModifEstado','user', 'codColor')
                ->where('nroComprobante', $ventas->nro_pedido)
                ->where('codArticulo', $ventas->cod_artic)
                ->first();

            $codEstado='';
            $comentarios='';
            $fecModifEstado='';
            $user='';
            $codColor=2;
            $difDiasPlanEntrega=0;

            $fechaPlan = Carbon::createFromFormat('d/m/Y H:i:s', $ventas->plan_entrega);
            $hoy = Carbon::now(); // Fecha actual
            
            // Calcula la diferencia en días (puede ser positiva o negativa)
            $difDiasPlanEntrega = (int) round($fechaPlan->diffInDays($hoy, false));
            
            if ($dtosAudit){
                $codEstado = match ($dtosAudit->codEstado) {
                    1 => 'PAD',
                    2 => 'PAC',
                    3 => 'NAP',
                    default => ''
                };
                $comentarios = $dtosAudit->comentarios;
                $fecModifEstado = $dtosAudit->fecModifEstado;
                $user = $dtosAudit->user;
                $codColor = $dtosAudit->codColor;
            }

            $precLista = DB::table('bertec_articulos')
                ->select('precio')
                ->where('cod_articulo', $ventas->cod_artic)
                ->first();
            
            $listadoFinal[] = [
                'nro_pedido' => $ventas->nro_pedido,
                'cod_artic' => $ventas->cod_artic,
                'descrip' => $ventas->descrip,
                'renglon' => $ventas->renglon,
                'cant_pedida' => $ventas->cant_pedida,
                'pend_desc' => $ventas->pend_desc,
                'saldo_ctrl_stock'  => $stocks->total_saldo_ctrl_stock,
                'cant_comp_stock'   => $stocks->total_cant_comp_stock,
                'aRecibir' => $aRecibir,
                'fec_pedido' => $ventas->fec_pedido,
                'plan_entrega' => $ventas->plan_entrega,
                'difDiasPlanEntrega' => $difDiasPlanEntrega,
                'cod_vend' => $ventas->cod_vend,
                'raz_social' => $ventas->raz_social,
                'nro_o_compra' => $ventas->nro_o_compra,
                'impoDolariz' => $impoDolariz,
                'faltante' => $faltante,

                // dtos de auditoria
                'codEstado' => $codEstado,
                'comentarios' => $comentarios,
                'fecModifEstado' => $fecModifEstado,
                'user' => $user,
                'codColor' => $codColor,

                // dtos de compras
                'compras_feccompra' => $compras_feccompra,
                'compras_fecmodif' => $compras_fecmodif,
                'compras_comentrarios' => $compras_comentrarios,
                'precLista' => $precLista?->precio ?? 0
            ];
        }

        if ($columOrden == 1){
            $this->ordenarComo2 = 'asc';
            $orden = ($this->ordenarComo1 == 'desc') ? 'desc' : 'asc';
            usort($listadoFinal, function($a, $b) use ($orden) {
                $estA = $a['codEstado'];
                $estB = $b['codEstado'];
                if ($orden === 'asc') {
                    return $estA <=> $estB; // Ascendente
                } else {
                    return $estB <=> $estA; // Descendente
                }
            });
        } else if ($columOrden == 2){
            $this->ordenarComo1 = 'asc';
            $orden = ($this->ordenarComo2 == 'desc') ? 'desc' : 'asc';
            usort($listadoFinal, function($a, $b) use ($orden) {
                $fechaA = Carbon::createFromFormat('d/m/Y H:i:s', $a['plan_entrega']);
                $fechaB = Carbon::createFromFormat('d/m/Y H:i:s', $b['plan_entrega']);
                if ($orden === 'asc') {
                    return $fechaA <=> $fechaB; // Ascendente
                } else {
                    return $fechaB <=> $fechaA; // Descendente
                }
            });
        }

        $this->listaRevVentas = $listadoFinal;
    }

    public function mount()
    {
        $this->selectDatos(1);
    }

    public function render()
    {
        return view('livewire.aplic.listrevventas');
    }
}
