<?php

namespace App\Livewire\Aplic;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Listrevcompras extends Component
{
    use WithPagination;

    public $ordenarComo = 'asc';
    public $verForm = false;
    public $listRevCompras = [];
    public $txtBuscaNroCompras = '';
    public $txtBuscaDescArtic = '';
    public $txtBuscaRazSocial = '';

    public $asignardtos_a = 0;
    public $varComprobante = '';
    public $varCodArticulo = '';
    public $fecCompra = '';
    public $fecModif = '';
    public $notas = '';
    public $tipoDeGrabacion;

    public function CancelarEdic(){
        $this->LimpiarCampos();
        $this->verForm = false;
    }

    public function Reordenar(){
        if ($this->ordenarComo == 'desc'){
            $this->ordenarComo = 'asc';
        } else {
            $this->ordenarComo = 'desc';
        }
        $this->selectDatos();
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
                $articulos = DB::table('bertec_01_compras_pend')
                    ->where('nro_compra', $this->varComprobante)
                    ->pluck('cod_artic');
        
                foreach ($articulos as $articulo) {
                   $fecha = null;

                    if (!empty($this->fecCompra)) {
                        // Mantiene formato compatible con MySQL (YYYY-MM-DD)
                        $fecha = Carbon::createFromFormat('Y-m-d', $this->fecCompra)->format('Y-m-d');
                    }
                    
                    DB::table('bertec_01_control_movim')->insert([
                        'nroComprobante' => $this->varComprobante,
                        'codArticulo'    => $articulo,
                        'fecCompra'      => $fecha,
                        'fecModif'       => $this->fecCompra ? now() : null,
                        'comentarios'    => $this->notas ?? null,
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ]);
                }
            } else {
                    $fecha = null;
                    if (!empty($this->fecCompra)) {
                        // Mantiene formato compatible con MySQL (YYYY-MM-DD)
                        $fecha = Carbon::createFromFormat('Y-m-d', $this->fecCompra)->format('Y-m-d');
                    }
                    DB::table('bertec_01_control_movim')->insert([
                    'nroComprobante' => $this->varComprobante,
                    'codArticulo'    => $this->varCodArticulo,
                    'fecCompra'      => $fecha,
                    'fecModif'       => $this->fecCompra ? now() : null,
                    'comentarios'    => $this->notas ?? null,
                    'created_at'     => now(),
                    'updated_at'     => now(),
                ]);
            }
        } else {
            // === ACTUALIZACIÓN ===
            if ($this->asignardtos_a == 1) {
                $items = DB::table('bertec_01_control_movim')
                    ->where('nroComprobante', $this->varComprobante)
                    ->get();
        
                foreach ($items as $item) {
                    $updateData = [
                        'comentarios' => $this->notas ?? null,
                        'updated_at'  => now(),
                    ];
        
                    if ($item->fecCompra !== $this->fecCompra) {
                        $fecha = null;
                        if (!empty($this->fecCompra)) {
                            // Mantiene formato compatible con MySQL (YYYY-MM-DD)
                            $fecha = Carbon::createFromFormat('Y-m-d', $this->fecCompra)->format('Y-m-d');
                        }

                        $updateData['fecCompra'] = $fecha;
                        $updateData['fecModif']  = now();
                    }
                            
                    DB::table('bertec_01_control_movim')
                        ->where('id', $item->id)
                        ->update($updateData);
                }
            } else {
                $item = DB::table('bertec_01_control_movim')
                    ->where('nroComprobante', $this->varComprobante)
                    ->where('codArticulo', $this->varCodArticulo)
                    ->first();
        
                $updateData = [
                    'comentarios' => $this->notas ?? null,
                    'updated_at'  => now(),
                ];
        
                if ($item && $item->fecCompra !== $this->fecCompra) {
                    $fecha = null;
                    if (!empty($this->fecCompra)) {
                        // Mantiene formato compatible con MySQL (YYYY-MM-DD)
                        $fecha = Carbon::createFromFormat('Y-m-d', $this->fecCompra)->format('Y-m-d');
                    }

                    $updateData['fecCompra'] = $fecha;
                    $updateData['fecModif']  = now();
                }
       
                DB::table('bertec_01_control_movim')
                    ->where('nroComprobante', $this->varComprobante)
                    ->where('codArticulo', $this->varCodArticulo)
                    ->update($updateData);
            }
        }

        $this->verForm = false;
        $this->LimpiarCampos();
        $this->selectDatos();
    }

    protected function LimpiarCampos(){
        $this->reset([
            'asignardtos_a',
            'varComprobante',
            'varCodArticulo',
            'fecCompra',
            'fecModif',
            'notas'
        ]);
    }

    public function Editar($param1, $param2){
        $this->varComprobante = $param1;
        $this->varCodArticulo = $param2;

        // Buscar datos de auditoría en bertec_01_control_movim
        $dtosAudit = DB::table('bertec_01_control_movim')
            ->select('fecCompra','fecModif','comentarios')
            ->where('nroComprobante', $param1)
            ->where('codArticulo', $param2)
            ->first();

        //por default se pone tipodegrabacion en 2, si no se encuentran datos de auditoria
        //se grabara como un nuevo elemento, si encuentra datos, tipodegrabacion tomará 1
        //y se actualizarán los datos.
        $this->tipoDeGrabacion = 2;
        if ($dtosAudit){
            $this->fecCompra = $dtosAudit->fecCompra;
            $this->fecModif = $dtosAudit->fecModif;
            $this->notas = $dtosAudit->comentarios;
            $this->tipoDeGrabacion = 1;
            //dd('paso');
        }

        $this->verForm = true;
    }

    public function Buscar1(){
        $this->txtBuscaDescArtic = '';
        $this->txtBuscaRazSocial = '';
        $this->selectDatos();
    }

    public function Buscar2(){
        $this->txtBuscaNroCompras = '';
        $this->txtBuscaRazSocial = '';
        $this->selectDatos();
    }

    public function Buscar3(){
        $this->txtBuscaNroCompras = '';
        $this->txtBuscaDescArtic = '';
        $this->selectDatos();
    }

    protected function selectDatos(){
        $listadoFinal = [];
        
        // Normalizar textos (eliminar espacios a ambos lados)
        $this->txtBuscaNroCompras   = trim($this->txtBuscaNroCompras);
        $this->txtBuscaDescArtic  = trim($this->txtBuscaDescArtic);
        $this->txtBuscaRazSocial    = trim($this->txtBuscaRazSocial);

        // Traer compras 
        if ($this->txtBuscaNroCompras!=''){
            $list_compras = DB::table('bertec_01_compras_pend')
            ->where('nro_compra', 'like', '%' . $this->txtBuscaNroCompras . '%')
            ->orderBy('nro_compra')
            ->get();
        } else if ($this->txtBuscaDescArtic!=''){
            $list_compras = DB::table('bertec_01_compras_pend')
            ->where('descrip', 'like', '%' . $this->txtBuscaDescArtic . '%')
            ->orderBy('nro_compra')
            ->get();
        } else if ($this->txtBuscaRazSocial!=''){
            $list_compras = DB::table('bertec_01_compras_pend')
            ->where('raz_social', 'like', '%' . $this->txtBuscaRazSocial . '%')
            ->orderBy('nro_compra')
            ->get();
        } else {
            $list_compras = DB::table('bertec_01_compras_pend')
            ->orderBy('nro_compra')
            ->limit(100)
            ->get();
        }

        
        foreach ($list_compras as $compra) {
            // Buscar stock del artículo
            $stocks = DB::table('bertec_01_stock_depositos')
                ->selectRaw('SUM(saldo_ctrl_stock) as total_saldo_ctrl_stock, SUM(cant_comp_stock) as total_cant_comp_stock')
                ->where('cod_artic', $compra->cod_artic)
                ->first();
            
            // Buscar datos de auditoría en bertec_01_control_movim
            $dtosAudit = DB::table('bertec_01_control_movim')
                ->select('fecCompra','fecModif','comentarios')
                ->where('nroComprobante', $compra->nro_compra)
                ->where('codArticulo', $compra->cod_artic)
                ->first();
            
            $fecCompra='';
            $fecModif='';
            $comentarios='';
            
            $faltante = $stocks->total_cant_comp_stock - $stocks->total_saldo_ctrl_stock - $compra->cant_pendiente;

            if ($faltante<0)
                $faltante=0;

            if ($dtosAudit){
                //dd($dtosAudit->fecCompra);
                $fecCompra = $dtosAudit->fecCompra;
                $fecModif = $dtosAudit->fecModif;
                $comentarios = $dtosAudit->comentarios;
            }

            $listadoFinal[] = [
                // Campos de compras
                'nro_compra'     => $compra->nro_compra,
                'cod_artic'      => $compra->cod_artic,
                'descripcion'    => $compra->descrip,
                'raz_social'     => $compra->raz_social,
                'cant_pedida'    => $compra->cant_pedida,
                'cant_recibida'  => $compra->cant_recibida,
                'cant_pendiente' => $compra->cant_pendiente,
                'moneda' => $compra->moneda,
                'cotiz' => $compra->cotiz,
                'fec_emision' => $compra->fec_emision,
                'fec_entrega' => $compra->fec_entrega,
                'faltante' => $faltante,
                
                // dtos de auditoria
                'fecCompra' => $fecCompra,
                'fecModif' => $fecModif,
                'comentarios' => $comentarios,

                // Campos de stock
                'saldo_ctrl_stock'  => $stocks->total_saldo_ctrl_stock,
                'cant_comp_stock'   => $stocks->total_cant_comp_stock
            ];
        }

        $orden = ($this->ordenarComo == 'desc') ? 'desc' : 'asc';

        usort($listadoFinal, function($a, $b) use ($orden) {
            $fechaA = strtotime($a['fecCompra']);
            $fechaB = strtotime($b['fecCompra']);

            if ($orden === 'asc') {
                return $fechaA <=> $fechaB; // Ascendente
            } else {
                return $fechaB <=> $fechaA; // Descendente
            }
        });

        //dd($listadoFinal);

        $this->listRevCompras = $listadoFinal;
    }

    public function mount()
    {
        $this->selectDatos();
    }

    public function render()
    {
        return view('livewire.aplic.listrevcompras', ['listRevCompras' => $this->listRevCompras]);
    }
}
