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
    public $fecCompra1 = '';
    public $comentarios1 = '';
    public $fecCompra2 = '';
    public $comentarios2 = '';
    public $fecModif = '';
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
                   $fecha1 = null;
                    if (!empty($this->fecCompra1)) {
                        // Mantiene formato compatible con MySQL (YYYY-MM-DD)
                        $fecha1 = Carbon::createFromFormat('Y-m-d', $this->fecCompra1)->format('Y-m-d');
                    }
                   $fecha2 = null;
                    if (!empty($this->fecCompra2)) {
                        // Mantiene formato compatible con MySQL (YYYY-MM-DD)
                        $fecha2 = Carbon::createFromFormat('Y-m-d', $this->fecCompra2)->format('Y-m-d');
                    }
                    
                    DB::table('bertec_01_control_movim')->insert([
                        'nroComprobante' => $this->varComprobante,
                        'codArticulo'    => $articulo,
                        'fecCompra1'      => $fecha1,
                        'fecCompra2'      => $fecha2,
                        'fecModif'       => $this->fecCompra1 ? now() : null,
                        'comentarios1'    => $this->comentarios1 ?? null,
                        'comentarios2'    => $this->comentarios2 ?? null,
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ]);
                }
            } else {
                    $fecha1 = null;
                    if (!empty($this->fecCompra1)) {
                        // Mantiene formato compatible con MySQL (YYYY-MM-DD)
                        $fecha1 = Carbon::createFromFormat('Y-m-d', $this->fecCompra1)->format('Y-m-d');
                    }
                    $fecha2 = null;
                    if (!empty($this->fecCompra2)) {
                        // Mantiene formato compatible con MySQL (YYYY-MM-DD)
                        $fecha2 = Carbon::createFromFormat('Y-m-d', $this->fecCompra2)->format('Y-m-d');
                    }
                    DB::table('bertec_01_control_movim')->insert([
                    'nroComprobante' => $this->varComprobante,
                    'codArticulo'    => $this->varCodArticulo,
                    'fecCompra1'      => $fecha1,
                    'fecCompra2'      => $fecha2,
                    'fecModif'       => $this->fecCompra ? now() : null,
                    'comentarios1'    => $this->comentarios1 ?? null,
                    'comentarios2'    => $this->comentarios2 ?? null,
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
                        'comentarios1' => $this->comentarios1 ?? null,
                        'comentarios2' => $this->comentarios2 ?? null,
                        'updated_at'  => now(),
                    ];
        
                    if ($item->fecCompra1 !== $this->fecCompra1) {
                        $fecha1 = null;
                        if (!empty($this->fecCompra1)) {
                            // Mantiene formato compatible con MySQL (YYYY-MM-DD)
                            $fecha1 = Carbon::createFromFormat('Y-m-d', $this->fecCompra1)->format('Y-m-d');
                        }

                        $updateData['fecCompra1'] = $fecha1;
                        $updateData['fecModif']  = now();
                    }

                    $fecha2 = null;
                    if (!empty($this->fecCompra2)) {
                        // Mantiene formato compatible con MySQL (YYYY-MM-DD)
                        $fecha2 = Carbon::createFromFormat('Y-m-d', $this->fecCompra2)->format('Y-m-d');
                        $updateData['fecCompra2'] = $fecha2;
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
                    'comentarios1' => $this->comentarios1 ?? null,
                    'comentarios2' => $this->comentarios2 ?? null,
                    'updated_at'  => now(),
                ];
        
                if ($item && $item->fecCompra1 !== $this->fecCompra1) {
                    $fecha1 = null;
                    if (!empty($this->fecCompra1)) {
                        // Mantiene formato compatible con MySQL (YYYY-MM-DD)
                        $fecha1 = Carbon::createFromFormat('Y-m-d', $this->fecCompra1)->format('Y-m-d');
                    }

                    $updateData['fecCompra1'] = $fecha1;
                    $updateData['fecModif']  = now();
                }
                
                $fecha2 = null;
                if (!empty($this->fecCompra2)) {
                    // Mantiene formato compatible con MySQL (YYYY-MM-DD)
                    $fecha2 = Carbon::createFromFormat('Y-m-d', $this->fecCompra2)->format('Y-m-d');
                    $updateData['fecCompra2'] = $fecha2;
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
            'fecCompra1',
            'fecCompra2',
            'fecModif',
            'comentarios1',
            'comentarios2'
        ]);
    }

    public function Editar($param1, $param2){
        $this->varComprobante = $param1;
        $this->varCodArticulo = $param2;

        // Buscar datos de auditoría en bertec_01_control_movim
        $dtosAudit = DB::table('bertec_01_control_movim')
            ->select('fecCompra1','fecCompra2','fecModif','comentarios1', 'comentarios2')
            ->where('nroComprobante', $param1)
            ->where('codArticulo', $param2)
            ->first();

        //por default se pone tipodegrabacion en 2, si no se encuentran datos de auditoria
        //se grabara como un nuevo elemento, si encuentra datos, tipodegrabacion tomará 1
        //y se actualizarán los datos.
        $this->tipoDeGrabacion = 2;
        if ($dtosAudit){
            $this->fecCompra1 = $dtosAudit->fecCompra1;
            $this->fecCompra2 = $dtosAudit->fecCompra2;
            $this->fecModif = $dtosAudit->fecModif;
            $this->comentarios1 = $dtosAudit->comentarios1;
            $this->comentarios2 = $dtosAudit->comentarios2;
            $this->tipoDeGrabacion = 1;
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
                ->select('fecCompra1','fecCompra2','fecModif','comentarios1', 'comentarios2')
                ->where('nroComprobante', $compra->nro_compra)
                ->where('codArticulo', $compra->cod_artic)
                ->first();
            
            $fecCompra1='';
            $fecCompra2='';
            $fecModif='';
            $comentarios1='';
            $comentarios2='';
            
            $faltante = $stocks->total_cant_comp_stock - $stocks->total_saldo_ctrl_stock - $compra->cant_pendiente;

            if ($faltante<0)
                $faltante=0;

            if ($dtosAudit){
                //dd($dtosAudit->fecCompra);
                $fecCompra1 = $dtosAudit->fecCompra1;
                $fecCompra2 = $dtosAudit->fecCompra2;
                $fecModif = $dtosAudit->fecModif;
                $comentarios1 = $dtosAudit->comentarios1;
                $comentarios2 = $dtosAudit->comentarios2;
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
                'fecCompra1' => $fecCompra1,
                'fecCompra2' => $fecCompra2,
                'fecModif' => $fecModif,
                'comentarios1' => $comentarios1,
                'comentarios2' => $comentarios2,

                // Campos de stock
                'saldo_ctrl_stock'  => $stocks->total_saldo_ctrl_stock,
                'cant_comp_stock'   => $stocks->total_cant_comp_stock
            ];
        }

        $orden = ($this->ordenarComo == 'desc') ? 'desc' : 'asc';

        usort($listadoFinal, function($a, $b) use ($orden) {
            $fechaA = strtotime($a['fecCompra1']);
            $fechaB = strtotime($b['fecCompra1']);

            if ($orden === 'asc') {
                return $fechaA <=> $fechaB; // Ascendente
            } else {
                return $fechaB <=> $fechaA; // Descendente
            }
        });

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
