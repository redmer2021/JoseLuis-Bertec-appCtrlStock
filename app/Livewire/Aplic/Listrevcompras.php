<?php

namespace App\Livewire\Aplic;

use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class Listrevcompras extends Component
{
    use WithPagination;

    public $verForm = false;
    public $listRevCompras = [];
    public $txtBuscaNroCompras = '';
    public $txtBuscaNroArticulo = '';
    public $txtBuscaRazSocial = '';

    public $asignardtos_a = 0;
    public $varComprobante = '';
    public $varCodArticulo = '';
    public $fecCompra = '';
    public $fecModif = '';
    public $notas = '';
    public $idEstado;
    public $tipoDeGrabacion;

    public function CancelarEdic(){
        $this->LimpiarCampos();
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
                $articulos = DB::table('bertec_01_compras_pend')
                    ->where('nro_compra', $this->varComprobante)
                    ->pluck('cod_artic');
        
                foreach ($articulos as $articulo) {
                    DB::table('bertec_01_control_movim')->insert([
                        'nroComprobante' => $this->varComprobante,
                        'codArticulo'    => $articulo,
                        'fecCompra'      => $this->fecCompra ?: null,
                        'fecModif'       => $this->fecCompra ? now() : null,
                        'estado'         => $this->idEstado ?? null,
                        'fecEstado'      => $this->idEstado ? now() : null,                        
                        'comentarios'    => $this->notas ?? null,
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ]);
                }
            } else {
                DB::table('bertec_01_control_movim')->insert([
                    'nroComprobante' => $this->varComprobante,
                    'codArticulo'    => $this->varCodArticulo,
                    'fecCompra'      => $this->fecCompra ?: null,
                    'fecModif'       => $this->fecCompra ? now() : null,
                    'estado'         => $this->idEstado ?? null,
                    'fecEstado'      => $this->idEstado ? now() : null,
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
                        'estado'      => $this->idEstado ?? null,
                        'comentarios' => $this->notas ?? null,
                        'updated_at'  => now(),
                    ];
        
                    // comparar como string
                    if ($item->fecCompra !== $this->fecCompra) {
                        $updateData['fecCompra'] = $this->fecCompra ?: null;
                        $updateData['fecModif']  = now();
                    }

                    // comparar idEstado
                    if ($item->estado !== $this->idEstado) {
                        $updateData['estado']    = $this->idEstado ?? null;
                        $updateData['fecEstado'] = now();
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
                    'estado'      => $this->idEstado ?? null,
                    'comentarios' => $this->notas ?? null,
                    'updated_at'  => now(),
                ];
        
                if ($item && $item->fecCompra !== $this->fecCompra) {
                    $updateData['fecCompra'] = $this->fecCompra ?: null;
                    $updateData['fecModif']  = now();
                }

                if ($item && $item->estado !== $this->idEstado) {
                    $updateData['estado']    = $this->idEstado ?? null;
                    $updateData['fecEstado'] = now();
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
            'idEstado',
            'notas'
        ]);
    }

    public function Editar($param1, $param2){
        $this->varComprobante = $param1;
        $this->varCodArticulo = $param2;

        // Buscar datos de auditoría en bertec_01_control_movim
        $dtosAudit = DB::table('bertec_01_control_movim')
            ->select('fecCompra','fecModif','estado','comentarios')
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
            $this->idEstado = $dtosAudit->estado;
            $this->notas = $dtosAudit->comentarios;
            $this->tipoDeGrabacion = 1;
        }

        $this->verForm = true;
    }

    public function Buscar1(){
        $this->txtBuscaNroArticulo = '';
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
        $this->txtBuscaNroArticulo = '';
        $this->selectDatos();
    }

    protected function selectDatos(){
        $listadoFinal = [];
        
        // Normalizar textos (eliminar espacios a ambos lados)
        $this->txtBuscaNroCompras   = trim($this->txtBuscaNroCompras);
        $this->txtBuscaNroArticulo  = trim($this->txtBuscaNroArticulo);
        $this->txtBuscaRazSocial    = trim($this->txtBuscaRazSocial);

        // Traer compras 
        if ($this->txtBuscaNroCompras!=''){
            $list_compras = DB::table('bertec_01_compras_pend')
            ->where('nro_compra', 'like', '%' . $this->txtBuscaNroCompras . '%')
            ->orderBy('nro_compra')
            ->orderBy('cod_artic')
            ->get();
        } else if ($this->txtBuscaNroArticulo!=''){
            $list_compras = DB::table('bertec_01_compras_pend')
            ->where('cod_artic', 'like', '%' . $this->txtBuscaNroArticulo . '%')
            ->orderBy('nro_compra')
            ->orderBy('cod_artic')
            ->get();
        } else if ($this->txtBuscaRazSocial!=''){
            $list_compras = DB::table('bertec_01_compras_pend')
            ->where('raz_social', 'like', '%' . $this->txtBuscaRazSocial . '%')
            ->orderBy('nro_compra')
            ->orderBy('cod_artic')
            ->get();
        } else {
            $list_compras = DB::table('bertec_01_compras_pend')
            ->orderBy('nro_compra')
            ->orderBy('cod_artic')
            ->limit(50)
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
                ->select('fecCompra','fecModif','fecEstado','estado','comentarios')
                ->where('nroComprobante', $compra->nro_compra)
                ->where('codArticulo', $compra->cod_artic)
                ->first();
            
            $fecCompra='';
            $fecModif='';
            $estado='';
            $fecEstado='';
            $comentarios='';
            
            $pendientes = $stocks->total_cant_comp_stock - $stocks->total_saldo_ctrl_stock;
            if ($pendientes<0)
                $pendientes=0;

            if ($dtosAudit){
                $fecCompra = $dtosAudit->fecCompra;
                $fecModif = $dtosAudit->fecModif;
                // Mapear estado numérico a texto
                switch ($dtosAudit->estado) {
                    case 1:
                        $estado = "PAD";
                        break;
                    case 2:
                        $estado = "PAC";
                        break;
                    default:
                        $estado = ""; 
                }
                $fecEstado = $dtosAudit->fecEstado;
                $comentarios = $dtosAudit->comentarios;
            }

            $listadoFinal[] = [
                // Campos de compras
                'nro_compra'     => $compra->nro_compra,
                'cod_artic'      => $compra->cod_artic,
                'descripcion'    => $compra->desc_adicional,
                'raz_social'     => $compra->raz_social,
                'cant_pedida'    => $compra->cant_pedida,
                'cant_recibida'  => $compra->cant_recibida,
                'cant_pendiente' => $compra->cant_pendiente,
                'moneda' => $compra->moneda,
                'cotiz' => $compra->cotiz,
                'fec_emision' => $compra->fec_emision,
                'fec_entrega' => $compra->fec_entrega,
                'pendiente' => $pendientes,
                
                // dtos de auditoria
                'fecCompra' => $fecCompra,
                'fecModif' => $fecModif,
                'estado' => $estado,
                'fecEstado' => $fecEstado,
                'comentarios' => $comentarios,

                // Campos de stock
                'saldo_ctrl_stock'  => $stocks->total_saldo_ctrl_stock,
                'cant_comp_stock'   => $stocks->total_cant_comp_stock
            ];
        }

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
