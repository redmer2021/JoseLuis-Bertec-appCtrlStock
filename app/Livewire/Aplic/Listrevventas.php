<?php

namespace App\Livewire\Aplic;

use App\Helpers\GeneradorTmp;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class Listrevventas extends Component
{
    use WithPagination;
    public $listaRevVentas = [];

    public $verForm = false;

    public $txtBuscaOrdenComp = '';
    public $txtBuscaNroVentas = '';
    public $txtBuscaDescArtic = '';
    public $txtBuscaRazSocial = '';
    public $txtFecIngresoStock = '';

    public $ordenarComo1 = 'sin';
    public $ordenarComo2 = 'sin';
    public $ordenarComo3 = 'sin';
    public $ordenarComo4 = 'sin';
    public $ordenarComo5 = 'sin';
    public $ordenarComo6 = 'sin';


    public $varComprobante = '';
    public $varCodArticulo = '';
    public $varDescArticulo = '';
    public $codEstado = 0;
    public $asignardtos_a = 0;
    public $codColor = 2;
    public $comentarios='';
    public $tipoDeGrabacion;

    public function ExportExcel(){
        // Guardar los valores actuales en la sesión
        session()->put('txtBuscaOrdenComp', $this->txtBuscaOrdenComp);
        session()->put('txtBuscaNroVentas', $this->txtBuscaNroVentas);
        session()->put('txtBuscaDescArtic', $this->txtBuscaDescArtic);
        session()->put('txtBuscaRazSocial', $this->txtBuscaRazSocial);
        session()->put('txtFecIngresoStock', $this->txtFecIngresoStock);

        // Redirigir al controlador que generará la descarga
        return redirect()->route('exportar.revventas');
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
                $this->ordenarComo4 = 'sin';
                $this->ordenarComo5 = 'sin';
                $this->ordenarComo6 = 'sin';
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
                $this->ordenarComo4 = 'sin';
                $this->ordenarComo5 = 'sin';
                $this->ordenarComo6 = 'sin';
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
                $this->ordenarComo4 = 'sin';
                $this->ordenarComo5 = 'sin';
                $this->ordenarComo6 = 'sin';
                break;
            case 4:
                if ($this->ordenarComo4 == 'sin'){
                    $this->ordenarComo4 = 'desc';
                } else if ($this->ordenarComo4 == 'desc'){
                    $this->ordenarComo4 = 'asc';
                } else {
                    $this->ordenarComo4 = 'sin';
                }
                $this->ordenarComo1 = 'sin';
                $this->ordenarComo2 = 'sin';
                $this->ordenarComo3 = 'sin';
                $this->ordenarComo5 = 'sin';
                $this->ordenarComo6 = 'sin';
                break;
            case 5:
                if ($this->ordenarComo5 == 'sin'){
                    $this->ordenarComo5 = 'desc';
                } else if ($this->ordenarComo5 == 'desc'){
                    $this->ordenarComo5 = 'asc';
                } else {
                    $this->ordenarComo5 = 'sin';
                }
                $this->ordenarComo1 = 'sin';
                $this->ordenarComo2 = 'sin';
                $this->ordenarComo3 = 'sin';
                $this->ordenarComo4 = 'sin';
                $this->ordenarComo6 = 'sin';
                break;
            case 6:
                if ($this->ordenarComo6 == 'sin'){
                    $this->ordenarComo6 = 'desc';
                } else if ($this->ordenarComo6 == 'desc'){
                    $this->ordenarComo6 = 'asc';
                } else {
                    $this->ordenarComo6 = 'sin';
                }
                $this->ordenarComo1 = 'sin';
                $this->ordenarComo2 = 'sin';
                $this->ordenarComo3 = 'sin';
                $this->ordenarComo4 = 'sin';
                $this->ordenarComo5 = 'sin';
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
            'codColor'
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
        GeneradorTmp::TmpVentas(auth()->user()->usrGuid);
        
        $this->LimpiarCampos();
        $this->selectDatos();
        $this->verForm = false;
    }
    protected function selectDatos($columOrden = 0){
        // Normalizar textos (eliminar espacios a ambos lados)
        $this->txtBuscaOrdenComp   = trim($this->txtBuscaOrdenComp);
        $this->txtBuscaNroVentas   = trim($this->txtBuscaNroVentas);
        $this->txtBuscaDescArtic  = trim($this->txtBuscaDescArtic);
        $this->txtBuscaRazSocial    = trim($this->txtBuscaRazSocial);

        // Construir query dinámica
        $query = DB::table('bertec_01_tmp_ventas')
            ->where('usrGuid', Auth::user()->usrGuid)
            ->when($this->txtBuscaOrdenComp != '', function ($q) {
                $q->where('nro_o_compra', 'like', '%' . $this->txtBuscaOrdenComp . '%');
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
            $query->orderBy('codColor', $this->ordenarComo1); // asc o desc
        }
        if (isset($this->ordenarComo2) && $this->ordenarComo2 !== 'sin') {
            $query->orderBy('faltante', $this->ordenarComo2); // asc o desc
        }
        if (isset($this->ordenarComo3) && $this->ordenarComo3 !== 'sin') {
            $query->orderBy('t1_cantidad', $this->ordenarComo3); // asc o desc
        }
        if (isset($this->ordenarComo4) && $this->ordenarComo4 !== 'sin') {
            $query->orderBy('codEstado', $this->ordenarComo4); // asc o desc
        }
        if (isset($this->ordenarComo5) && $this->ordenarComo5 !== 'sin') {
            $query->orderBy('plan_entrega', $this->ordenarComo5); // asc o desc
        }
        if (isset($this->ordenarComo6) && $this->ordenarComo6 !== 'sin') {
            $query->orderBy('difDiasPlanEntrega', $this->ordenarComo6); // asc o desc
        }
        
        $this->listaRevVentas = $query->get();


        // // Construir q base
        // $query = DB::table('bertec_01_tmp_compras')
        //     ->where('usrGuid', Auth::user()->usrGuid)
        //     ->when($this->txtBuscaNroCompras != '', function ($q) {
        //         $q->where('nro_compra', 'like', '%' . $this->txtBuscaNroCompras . '%');
        //     })
        //     ->when($this->txtBuscaDescArtic != '', function ($q) {
        //         $q->where('descrip', 'like', '%' . $this->txtBuscaDescArtic . '%');
        //     })
        //     ->when($this->txtBuscaRazSocial != '', function ($q) {
        //         $q->where('raz_social', 'like', '%' . $this->txtBuscaRazSocial . '%');
        //     });

        //     // Orden dinámico según variables
        //     if (isset($this->ordenarComo1) && $this->ordenarComo1 !== 'sin') {
        //         $query->orderBy('faltante', $this->ordenarComo1); // asc o desc
        //     }

        //     if (isset($this->ordenarComo2) && $this->ordenarComo2 !== 'sin') {
        //         $query->orderBy('fecCompra1', $this->ordenarComo2); // asc o desc
        //     }

        //     // Ejecutar consulta final
        //     $this->listRevCompras = $query->get();

    }

    
    // protected function selectDatos_old($columOrden = 0){
    //     $listadoFinal = [];
        
    //     // Normalizar textos (eliminar espacios a ambos lados)
    //     $this->txtBuscaOrdenComp   = trim($this->txtBuscaOrdenComp);
    //     $this->txtBuscaNroVentas   = trim($this->txtBuscaNroVentas);
    //     $this->txtBuscaDescArtic  = trim($this->txtBuscaDescArtic);
    //     $this->txtBuscaRazSocial    = trim($this->txtBuscaRazSocial);

    //     // Construir query dinámica
    //     $list_ventas = DB::table('vta_bertec_01_pend_entrega')
    //         ->when($this->txtBuscaOrdenComp != '', function ($query) {
    //             $query->where('nro_o_compra', 'like', '%' . $this->txtBuscaOrdenComp . '%');
    //         })
    //         ->when($this->txtBuscaNroVentas != '', function ($query) {
    //             $query->where('nro_pedido', 'like', '%' . $this->txtBuscaNroVentas . '%');
    //         })
    //         ->when($this->txtBuscaDescArtic != '', function ($query) {
    //             $query->where('descrip', 'like', '%' . $this->txtBuscaDescArtic . '%');
    //         })
    //         ->when($this->txtBuscaRazSocial != '', function ($query) {
    //             $query->where('raz_social', 'like', '%' . $this->txtBuscaRazSocial . '%');
    //         })
    //         ->get();

    //     foreach ($list_ventas as $ventas) {
    //         // Buscar stock del artículo
    //         $stocks = DB::table('bertec_01_stock_depositos')
    //             ->selectRaw('SUM(saldo_ctrl_stock) as total_saldo_ctrl_stock, SUM(cant_comp_stock) as total_cant_comp_stock')
    //             ->where('cod_artic', $ventas->cod_artic)
    //             ->first();

    //         $total_pendiente = DB::table('bertec_01_compras_pend')
    //             ->where('cod_artic', $ventas->cod_artic)
    //             ->sum('cant_pendiente');

    //         $faltante = max(0, $stocks->total_cant_comp_stock - $stocks->total_saldo_ctrl_stock - $total_pendiente);

    //         $aRecibir = DB::table('bertec_01_compras_pend')
    //             ->where('cod_artic', $ventas->cod_artic)
    //             ->sum('cant_pedida');

    //         $notasCompras = DB::table('bertec_01_control_compras')
    //             ->select('fecCompra1', 'fecModif', 'comentarios1')
    //             ->where('codArticulo', $ventas->cod_artic)
    //             ->orderBy('fecCompra1', 'asc')
    //             ->limit(1)->first();
            
    //         $compras_feccompra='';
    //         $compras_fecmodif='';
    //         $compras_comentrarios='';

    //         if ($notasCompras){
    //             $compras_feccompra = $notasCompras->fecCompra1;
    //             $compras_fecmodif = $notasCompras->fecModif;
    //             $compras_comentrarios = $notasCompras->comentarios1;
    //         }

    //         if (!empty($ventas->cotiza) && $ventas->cotiza != 0 && !empty($ventas->pend_factu) && $ventas->pend_factu != 0) {        
    //             $impoDolariz = ($ventas->importe / $ventas->pend_factu) / $ventas->cotiza;
    //         } else {
    //             $impoDolariz = 0;
    //         }
        
    //         // Buscar datos de auditoría en bertec_01_control_ventas
    //         $dtosAudit = DB::table('bertec_01_control_ventas')
    //             ->select('codEstado','comentarios','fecModifEstado','user', 'codColor')
    //             ->where('nroComprobante', $ventas->nro_pedido)
    //             ->where('codArticulo', $ventas->cod_artic)
    //             ->first();

    //         $codEstado='';
    //         $comentarios='';
    //         $fecModifEstado='';
    //         $user='';
    //         $codColor=2;
    //         $difDiasPlanEntrega=0;

    //         $fechaPlan = Carbon::createFromFormat('d/m/Y H:i:s', $ventas->plan_entrega);
    //         $hoy = Carbon::now(); // Fecha actual
            
    //         // Calcula la diferencia en días (puede ser positiva o negativa)
    //         $difDiasPlanEntrega = (int) round($fechaPlan->diffInDays($hoy, false));
            
    //         if ($dtosAudit){
    //             $codEstado = match ($dtosAudit->codEstado) {
    //                 1 => 'PAD',
    //                 2 => 'PAC',
    //                 3 => 'NAP',
    //                 default => ''
    //             };
    //             $comentarios = $dtosAudit->comentarios;
    //             $fecModifEstado = $dtosAudit->fecModifEstado;
    //             $user = $dtosAudit->user;
    //             $codColor = $dtosAudit->codColor;
    //         }

    //         $precLista = DB::table('bertec_articulos')
    //             ->select('precio')
    //             ->where('cod_articulo', $ventas->cod_artic)
    //             ->first();

    //         //Calcular diferencia porcentual
    //         $difPorcentual = 0;
    //         if ($impoDolariz > 0 && $impoDolariz < ($precLista->precio ?? 0)) {
    //             $diferencia = $precLista->precio - $impoDolariz;
    //             $difPorcentual = ($diferencia / $precLista->precio) * 100;
    //         }
    //         $colorCelda = 0;
    //         if ($difPorcentual>=0.1 and $difPorcentual<=20){
    //             $colorCelda = 1;
    //         } else if ($difPorcentual > 20 and $difPorcentual<=25){
    //             $colorCelda = 2;
    //         } else if ($difPorcentual>25){
    //             $colorCelda = 3;
    //         }

    //         // revisión stock dia anterior
    //         $dtosStockDiaAnt = DB::table('bertec_01_stock_anterior')
    //             ->select('t1_fecha_ingreso','t1_cod_articu','t1_cantidad')
    //             ->where('t1_cod_articu', $ventas->cod_artic)
    //             ->where('t1_fecha_ingreso', $this->txtFecIngresoStock)                    
    //             ->first();
    //         $t1_cantidad = 0;
    //         if ($dtosStockDiaAnt){
    //             $t1_cantidad = $dtosStockDiaAnt->t1_cantidad;
    //         }

    //         $listadoFinal[] = [
    //             'nro_pedido' => $ventas->nro_pedido,
    //             'cod_artic' => $ventas->cod_artic,
    //             'descrip' => $ventas->descrip,
    //             'renglon' => $ventas->renglon,
    //             'cant_pedida' => $ventas->cant_pedida,
    //             'pend_desc' => $ventas->pend_desc,
    //             'saldo_ctrl_stock'  => $stocks->total_saldo_ctrl_stock,
    //             'cant_comp_stock'   => $stocks->total_cant_comp_stock,
    //             'aRecibir' => $aRecibir,
    //             'fec_pedido' => $ventas->fec_pedido,
    //             'plan_entrega' => $ventas->plan_entrega,
    //             'difDiasPlanEntrega' => $difDiasPlanEntrega,
    //             'cod_vend' => $ventas->cod_vend,
    //             'raz_social' => $ventas->raz_social,
    //             'nro_o_compra' => $ventas->nro_o_compra,
    //             'impoDolariz' => $impoDolariz,
    //             'faltante' => $faltante,

    //             // dtos de auditoria
    //             'codEstado' => $codEstado,
    //             'comentarios' => $comentarios,
    //             'fecModifEstado' => $fecModifEstado,
    //             'user' => $user,
    //             'codColor' => $codColor,

    //             // dtos de compras
    //             'compras_feccompra' => $compras_feccompra,
    //             'compras_fecmodif' => $compras_fecmodif,
    //             'compras_comentrarios' => $compras_comentrarios,
    //             'precLista' => $precLista?->precio ?? 0,
    //             'difPorcentual' => $difPorcentual,
    //             'colorCelda' => $colorCelda,
    //             't1_cantidad' => $t1_cantidad,
    //         ];
    //     }

    //     if ($columOrden == 1){
    //         $this->ordenarComo2 = 'sin';
    //         $this->ordenarComo3 = 'sin';
    //         $this->ordenarComo4 = 'sin';
    //         $this->ordenarComo5 = 'sin';
    //         $this->ordenarComo6 = 'sin';

    //         // Determinar el tipo de orden (asc o desc)
    //         $orden = ($this->ordenarComo1 == 'desc') ? SORT_DESC : SORT_ASC;
    //         $columnaColor = array_column($listadoFinal, 'codColor');
    //         array_multisort($columnaColor, $orden, $listadoFinal);            

    //         // $orden = ($this->ordenarComo1 == 'desc') ? 'desc' : 'asc';
    //         // usort($listadoFinal, function($a, $b) use ($orden) {
    //         //     $estA = $a['codColor'];
    //         //     $estB = $b['codColor'];
    //         //     if ($orden === 'asc') {
    //         //         return $estA <=> $estB; // Ascendente
    //         //     } else {
    //         //         return $estB <=> $estA; // Descendente
    //         //     }
    //         // });

    //     } else if ($columOrden == 2){
    //         $this->ordenarComo1 = 'sin';
    //         $this->ordenarComo3 = 'sin';
    //         $this->ordenarComo4 = 'sin';
    //         $this->ordenarComo5 = 'sin';
    //         $this->ordenarComo6 = 'sin';
    //         // Determinar el tipo de orden (asc o desc)
    //         $orden = ($this->ordenarComo2 == 'desc') ? SORT_DESC : SORT_ASC;
    //         $columnaColor = array_column($listadoFinal, 'faltante');
    //         array_multisort($columnaColor, $orden, $listadoFinal);            

    //         //$orden = ($this->ordenarComo2 == 'desc') ? 'desc' : 'asc';
    //         //usort($listadoFinal, function($a, $b) use ($orden) {
    //         //    $estA = $a['faltante'];
    //         //    $estB = $b['faltante'];
    //         //    if ($orden === 'asc') {
    //         //        return $estA <=> $estB; // Ascendente
    //         //    } else {
    //         //        return $estB <=> $estA; // Descendente
    //         //    }
    //         //});
    //     } else if ($columOrden == 3){
    //         $this->ordenarComo1 = 'sin';
    //         $this->ordenarComo2 = 'sin';
    //         $this->ordenarComo4 = 'sin';
    //         $this->ordenarComo5 = 'sin';
    //         $this->ordenarComo6 = 'sin';
    //         $orden = ($this->ordenarComo3 == 'desc') ? 'desc' : 'asc';
    //         usort($listadoFinal, function($a, $b) use ($orden) {
    //             $estA = $a['t1_cantidad'];
    //             $estB = $b['t1_cantidad'];
    //             if ($orden === 'asc') {
    //                 return $estA <=> $estB; // Ascendente
    //             } else {
    //                 return $estB <=> $estA; // Descendente
    //             }
    //         });            
    //     } else if ($columOrden == 4){
    //         $this->ordenarComo1 = 'sin';
    //         $this->ordenarComo2 = 'sin';
    //         $this->ordenarComo3 = 'sin';
    //         $this->ordenarComo5 = 'sin';
    //         $this->ordenarComo6 = 'sin';
    //         $orden = ($this->ordenarComo4 == 'desc') ? 'desc' : 'asc';
    //         usort($listadoFinal, function($a, $b) use ($orden) {
    //             $estA = $a['codEstado'];
    //             $estB = $b['codEstado'];
    //             if ($orden === 'asc') {
    //                 return $estA <=> $estB; // Ascendente
    //             } else {
    //                 return $estB <=> $estA; // Descendente
    //             }
    //         });
    //     } else if ($columOrden == 5){
    //         $this->ordenarComo1 = 'sin';
    //         $this->ordenarComo2 = 'sin';
    //         $this->ordenarComo3 = 'sin';
    //         $this->ordenarComo4 = 'sin';
    //         $this->ordenarComo6 = 'sin';
    //         $orden = ($this->ordenarComo5 == 'desc') ? 'desc' : 'asc';
    //         usort($listadoFinal, function($a, $b) use ($orden) {
    //             $fechaA = Carbon::createFromFormat('d/m/Y H:i:s', $a['plan_entrega']);
    //             $fechaB = Carbon::createFromFormat('d/m/Y H:i:s', $b['plan_entrega']);
    //             if ($orden === 'asc') {
    //                 return $fechaA <=> $fechaB; // Ascendente
    //             } else {
    //                 return $fechaB <=> $fechaA; // Descendente
    //             }
    //         });
    //     } else if ($columOrden == 6){
    //         $this->ordenarComo1 = 'sin';
    //         $this->ordenarComo2 = 'sin';
    //         $this->ordenarComo3 = 'sin';
    //         $this->ordenarComo4 = 'sin';
    //         $this->ordenarComo5 = 'sin';
    //         $orden = ($this->ordenarComo5 == 'desc') ? 'desc' : 'asc';
    //         usort($listadoFinal, function($a, $b) use ($orden) {
    //             $estA = $a['difDiasPlanEntrega'];
    //             $estB = $b['difDiasPlanEntrega'];
    //             if ($orden === 'asc') {
    //                 return $estA <=> $estB; // Ascendente
    //             } else {
    //                 return $estB <=> $estA; // Descendente
    //             }
    //         });            
    //     }

    //     $this->listaRevVentas = $listadoFinal;
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

        $this->selectDatos(1);
    }

    public function render()
    {
        return view('livewire.aplic.listrevventas');
    }
}
