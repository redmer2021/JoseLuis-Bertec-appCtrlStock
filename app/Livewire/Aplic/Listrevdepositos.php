<?php

namespace App\Livewire\Aplic;

use App\Helpers\GeneradorTmp;
use Illuminate\Support\Facades\DB;
use Livewire\WithPagination;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Listrevdepositos extends Component
{
    use WithPagination;

    public $ordenarComo1 = 'sin';
    public $ordenarComo2 = 'sin';
    public $ordenarComo3 = 'sin';
    public $listaRevDepositos = [];
    public $verForm = false;

    public $txtBuscaNroVentas = '';
    public $txtBuscaDescArtic = '';
    public $txtBuscaRazSocial = '';
    public $txtBuscaNroOrdenCompra = '';
    public $txtFecIngresoStock='';

    public $varComprobante = '';
    public $varCodArticulo = '';
    public $varDescArticulo = '';
    public $codEstado = 0;
    public $comentarios='';
    public $tipoDeGrabacion;
    public $asignardtos_a = 0;
    public $codCtrl = 2;
    public $codColor = 2;

    public function ExportExcel(){
        // Guardar los valores actuales en la sesión
        session()->put('txtBuscaNroVentas', $this->txtBuscaNroVentas);
        session()->put('txtBuscaDescArtic', $this->txtBuscaDescArtic);
        session()->put('txtBuscaRazSocial', $this->txtBuscaRazSocial);
        session()->put('txtBuscaNroOrdenCompra', $this->txtBuscaNroOrdenCompra);

        // Redirigir al controlador que generará la descarga
        return redirect()->route('exportar.revdepositos');
    }

    public function Reordenar($campo){
        switch($campo){
            case 1:
                if ($this->ordenarComo1 == 'sin'){
                    $this->ordenarComo1 = 'desc';
                } else if ($this->ordenarComo1 == 'desc'){
                    $this->ordenarComo1 = 'asc';
                } else {
                    $this->ordenarComo1 = 'sin';
                }
                $this->ordenarComo2 = 'sin';
                $this->ordenarComo3 = 'sin';
                break;
            case 2:
                if ($this->ordenarComo2 == 'sin'){
                    $this->ordenarComo2 = 'desc';
                } else if ($this->ordenarComo2 == 'desc'){
                    $this->ordenarComo2 = 'asc';
                } else {
                    $this->ordenarComo2 = 'sin';
                }
                $this->ordenarComo1 = 'sin';
                $this->ordenarComo3 = 'sin';
                break;
            case 3:
                if ($this->ordenarComo3 == 'sin'){
                    $this->ordenarComo3 = 'desc';
                } else if ($this->ordenarComo3 == 'desc'){
                    $this->ordenarComo3 = 'asc';
                } else {
                    $this->ordenarComo3 = 'sin';
                }
                $this->ordenarComo1 = 'sin';
                $this->ordenarComo2 = 'sin';
                break;
        }
        $this->selectDatos();
    }


    public function Buscar(){
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

    public function Editar($param1, $param2, $param3){
        $this->verForm = true;
        $this->LimpiarCampos();
        $this->resetErrorBag();
        $this->varComprobante = $param1;
        $this->varCodArticulo = $param2;
        $this->varDescArticulo = $param3;

        // Buscar datos de auditoría en bertec_01_control_ventas
        $dtosAudit = DB::table('bertec_01_control_ventas')
            ->select('codEstado','comentarios','codColor', 'codCtrl')
            ->where('nroComprobante', $param1)
            ->where('codArticulo', $param2)
            ->first();


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
        GeneradorTmp::TmpVentas(auth()->user()->usrGuid);

        $this->LimpiarCampos();
        $this->selectDatos();
        $this->verForm = false;
    }


    public function CancelarEdic(){
        $this->verForm = false;
    }

    protected function selectDatos(){
        // Normalizar textos (eliminar espacios a ambos lados)
         $this->txtBuscaNroVentas   = trim($this->txtBuscaNroVentas);
         $this->txtBuscaDescArtic  = trim($this->txtBuscaDescArtic);
         $this->txtBuscaRazSocial    = trim($this->txtBuscaRazSocial);
         $this->txtBuscaNroOrdenCompra    = trim($this->txtBuscaNroOrdenCompra);
        
         // Construir query dinámica
        $query = DB::table('bertec_01_tmp_ventas')
            ->where('usrGuid', Auth::user()->usrGuid)
            ->where('codColor', 1)
            ->when($this->txtBuscaNroOrdenCompra != '', function ($q) {
                $q->where('nro_o_compra', 'like', '%' . $this->txtBuscaNroOrdenCompra . '%');
            })
            ->when($this->txtBuscaNroVentas != '', function ($q) {
                $q->where('nro_pedido', 'like', '%' . $this->txtBuscaNroVentas . '%');
            })
            ->when($this->txtBuscaDescArtic != '', function ($q) {
                $q->where('descrip', 'like', '%' . $this->txtBuscaDescArtic . '%');
            })
            ->when($this->txtBuscaRazSocial != '', function ($q) {
                $q->where('raz_social', 'like', '%' . $this->txtBuscaRazSocial . '%');
            });
        
        // Orden dinámico según variables
        if (isset($this->ordenarComo1) && $this->ordenarComo1 !== 'sin') {
            $query->orderBy('t1_cantidad', $this->ordenarComo1); // asc o desc
        }
        if (isset($this->ordenarComo2) && $this->ordenarComo2 !== 'sin') {
            $query->orderBy('plan_entrega', $this->ordenarComo2); // asc o desc
        }
        if (isset($this->ordenarComo3) && $this->ordenarComo3 !== 'sin') {
            $query->orderBy('difDiasPlanEntrega', $this->ordenarComo3); // asc o desc
        }
        
        $this->listaRevDepositos = $query->get();


        // // Construir query dinámica
        // $query = DB::table('bertec_01_tmp_ventas')
        //     ->where('usrGuid', Auth::user()->usrGuid)
        //     ->when($this->txtBuscaOrdenComp != '', function ($q) {
        //         $q->where('nro_o_compra', 'like', '%' . $this->txtBuscaOrdenComp . '%');
        //     })
        //     ->when($this->txtBuscaNroVentas != '', function ($q) {
        //         $q->where('nro_pedido', 'like', '%' . $this->txtBuscaNroVentas . '%');
        //     })
        //     ->when($this->txtBuscaDescArtic != '', function ($q) {
        //         $q->where('descrip', 'like', '%' . $this->txtBuscaDescArtic . '%');
        //     })
        //     ->when($this->txtBuscaRazSocial != '', function ($q) {
        //         $q->where('raz_social', 'like', '%' . $this->txtBuscaRazSocial . '%');
        //     });

        // // Orden dinámico según variables
        // if (isset($this->ordenarComo1) && $this->ordenarComo1 !== 'sin') {
        //     $query->orderBy('codColor', $this->ordenarComo1); // asc o desc
        // }
        // if (isset($this->ordenarComo2) && $this->ordenarComo2 !== 'sin') {
        //     $query->orderBy('faltante', $this->ordenarComo2); // asc o desc
        // }
        // if (isset($this->ordenarComo3) && $this->ordenarComo3 !== 'sin') {
        //     $query->orderBy('t1_cantidad', $this->ordenarComo3); // asc o desc
        // }
        // if (isset($this->ordenarComo4) && $this->ordenarComo4 !== 'sin') {
        //     $query->orderBy('codEstado', $this->ordenarComo4); // asc o desc
        // }
        // if (isset($this->ordenarComo5) && $this->ordenarComo5 !== 'sin') {
        //     $query->orderBy('plan_entrega', $this->ordenarComo5); // asc o desc
        // }
        // if (isset($this->ordenarComo6) && $this->ordenarComo6 !== 'sin') {
        //     $query->orderBy('difDiasPlanEntrega', $this->ordenarComo6); // asc o desc
        // }
        
        // $this->listaRevVentas = $query->get();



    }
    // protected function selectDatos($columOrden = 0){
    //     $listadoFinal = [];
        
    //     // Normalizar textos (eliminar espacios a ambos lados)
    //     $this->txtBuscaNroVentas   = trim($this->txtBuscaNroVentas);
    //     $this->txtBuscaDescArtic  = trim($this->txtBuscaDescArtic);
    //     $this->txtBuscaRazSocial    = trim($this->txtBuscaRazSocial);
    //     $this->txtBuscaNroOrdenCompra    = trim($this->txtBuscaNroOrdenCompra);

    //     $list_ventas = DB::table('bertec_01_pend_entrega')
    //     ->when($this->txtBuscaNroVentas != '', function ($query) {
    //         $query->where('nro_pedido', 'like', '%' . $this->txtBuscaNroVentas . '%');
    //     })
    //     ->when($this->txtBuscaDescArtic != '', function ($query) {
    //         $query->where('descrip', 'like', '%' . $this->txtBuscaDescArtic . '%');
    //     })
    //     ->when($this->txtBuscaRazSocial != '', function ($query) {
    //         $query->where('raz_social', 'like', '%' . $this->txtBuscaRazSocial . '%');
    //     })
    //     ->when($this->txtBuscaNroOrdenCompra != '', function ($query) {
    //         $query->where('nro_o_compra', 'like', '%' . $this->txtBuscaNroOrdenCompra . '%');
    //     })
    //     ->orderBy('nro_pedido')
    //     // ->limit(100)
    //     ->get();        
        
    //     foreach ($list_ventas as $ventas) {
    //         $dtosAudit = DB::table('bertec_01_control_ventas')
    //             ->select('codEstado','comentarios','fecModifEstado','user', 'codColor', 'codCtrl')
    //             ->where('nroComprobante', $ventas->nro_pedido)
    //             ->where('codArticulo', $ventas->cod_artic)
    //             ->first();

    //         $codEstado='';
    //         $comentarios='';
    //         $fecModifEstado='';
    //         $user='';
    //         $codColor=0;
    //         $codCtrl=0;

    //         if ($dtosAudit){
    //             $codEstado = $dtosAudit->codEstado == 1 ? 'PAD' : 'PAC';
    //             $comentarios = $dtosAudit->comentarios;
    //             $fecModifEstado = $dtosAudit->fecModifEstado;
    //             $user = $dtosAudit->user;
    //             $codColor = $dtosAudit->codColor;
    //             $codCtrl = $dtosAudit->codCtrl;
    //         }

    //         if ($codColor == 1){
    //             // revisión stock dia anterior
    //             $dtosStockDiaAnt = DB::table('bertec_01_stock_anterior')
    //                 ->select('t1_fecha_ingreso','t1_cod_articu','t1_cantidad')
    //                 ->where('t1_cod_articu', $ventas->cod_artic)
    //                 ->where('t1_fecha_ingreso', $this->txtFecIngresoStock)                    
    //                 ->first();
    //             $t1_cantidad = 0;
    //             if ($dtosStockDiaAnt){
    //                 $t1_cantidad = $dtosStockDiaAnt->t1_cantidad;
    //             }

    //             // Buscar stock del artículo
    //             $stocks = DB::table('bertec_01_stock_depositos')
    //                 ->selectRaw('SUM(saldo_ctrl_stock) as total_saldo_ctrl_stock')
    //                 ->where('cod_artic', $ventas->cod_artic)
    //                 ->first();

    //             $listadoFinal[] = [
    //                 'nro_pedido' => $ventas->nro_pedido,
    //                 'cod_artic' => $ventas->cod_artic,
    //                 'descrip' => $ventas->descrip,
    //                 'renglon' => $ventas->renglon,
    //                 'cant_pedida' => $ventas->cant_pedida,
    //                 'pend_desc' => $ventas->pend_desc,
    //                 'saldo_ctrl_stock'  => $stocks->total_saldo_ctrl_stock,
    //                 'femoc' => $compras->fec_emision ?? null,
    //                 'fec_pedido' => $ventas->fec_pedido,
    //                 'plan_entrega' => $ventas->plan_entrega,
    //                 'cod_vend' => $ventas->cod_vend,
    //                 'raz_social' => $ventas->raz_social,
    //                 'nro_o_compra' => $ventas->nro_o_compra,
    
    //                 // dtos de auditoria
    //                 'codEstado' => $codEstado,
    //                 'comentarios' => $comentarios,
    //                 'fecModifEstado' => $fecModifEstado,
    //                 'user' => $user,
    //                 'codColor' => $codColor,
    //                 'codCtrl' => $codCtrl,
    //                 't1_cantidad' => $t1_cantidad
    //             ];
    //         }
    //     }

    //     if ($columOrden == 1){
    //         $orden = ($this->ordenarComo1 == 'desc') ? 'desc' : 'asc';
    //         usort($listadoFinal, function($a, $b) use ($orden) {
    //             $ingrA = $a['t1_cantidad'];
    //             $ingrB = $b['t1_cantidad'];
    
    //             if ($orden === 'asc') {
    //                 return $ingrA <=> $ingrB; // Ascendente
    //             } else {
    //                 return $ingrB <=> $ingrA; // Descendente
    //             }
    //         });
    //     }else if ($columOrden == 2){
    //         $orden = ($this->ordenarComo2 == 'desc') ? 'desc' : 'asc';
    //         usort($listadoFinal, function($a, $b) use ($orden) {
    //             $fechaA = Carbon::createFromFormat('d/m/Y H:i:s', $a['plan_entrega']);
    //             $fechaB = Carbon::createFromFormat('d/m/Y H:i:s', $b['plan_entrega']);
    
    //             if ($orden === 'asc') {
    //                 return $fechaA <=> $fechaB; // Ascendente
    //             } else {
    //                 return $fechaB <=> $fechaA; // Descendente
    //             }
    //         });
    //     }

    //     $this->listaRevDepositos = $listadoFinal;
    // }

    public function mount()
    {
        // // Tomar el día anterior
        // $fecha = now()->subDay();

        // // Si es domingo (0) → restar 2 días (viernes)
        // // Si es sábado (6) → restar 1 día (viernes)
        // if ($fecha->isSunday()) {
        //     $fecha = $fecha->subDays(2);
        // } elseif ($fecha->isSaturday()) {
        //     $fecha = $fecha->subDay();
        // }

        // $this->txtFecIngresoStock = $fecha->toDateString();        
        $this->selectDatos();
    }


    public function render()
    {
        return view('livewire.aplic.listrevdepositos');
    }
}
