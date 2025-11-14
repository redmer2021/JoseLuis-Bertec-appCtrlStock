<?php

namespace App\Livewire\Aplic;

use App\Helpers\GeneradorTmp;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class Listrevcompras extends Component
{
    use WithPagination;

    public $ordenarComo1 = 'sin';
    public $ordenarComo2 = 'sin';
    public $verForm = false;
    public $listRevCompras = [];
    public $txtBuscaNroCompras = '';
    public $txtBuscaDescArtic = '';
    public $txtBuscaRazSocial = '';

    public $asignardtos_a = 0;
    public $varComprobante = '';
    public $varCodArticulo = '';
    public $varDescArticulo = '';
    public $fecCompra1 = '';
    public $comentarios1 = '';
    public $fecCompra2 = '';
    public $comentarios2 = '';
    public $unidades1 = 0;
    public $unidades2 = 0;
    public $fecModif = '';
    public $tipoDeGrabacion;
    public $entregaParc;
    public $cant_pendientes=0;

    public function ExportExcel(){
        // Guardar los valores actuales en la sesión
        session()->put('txtBuscaNroCompras', $this->txtBuscaNroCompras);
        session()->put('txtBuscaDescArtic', $this->txtBuscaDescArtic);
        session()->put('txtBuscaRazSocial', $this->txtBuscaRazSocial);

        // Redirigir al controlador que generará la descarga
        return redirect()->route('exportar.revcompras');
    }

    public function CancelarEdic(){
        $this->LimpiarCampos();
        $this->verForm = false;
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
                break;
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

        if ($this->asignardtos_a == 2) {
            $totalUnidades = (int)($this->unidades1 ?? 0) + (int)($this->unidades2 ?? 0);
            
            if ($totalUnidades > $this->cant_pendientes) {
                $this->addError('unidades', 'La suma de unidades ingresadas no puede superar la cantidad pendiente (' . number_format($this->cant_pendientes,0) . ').');
                return;
            }
        }        
        if ($this->tipoDeGrabacion == 2) {
            // === INSERCIÓN ===
            if ($this->asignardtos_a == 1) {
                $articulos = DB::table('bertec_01_compras_pend')
                    ->where('nro_compra', $this->varComprobante)
                    ->pluck('cod_artic');
        
                foreach ($articulos as $articulo) {
                   $fecha1 = null;
                    if (!empty($this->fecCompra1)) {
                        $fecha1 = Carbon::createFromFormat('Y-m-d', $this->fecCompra1)->format('Y-m-d');
                    }
                   $fecha2 = null;
                    if (!empty($this->fecCompra2)) {
                        $fecha2 = Carbon::createFromFormat('Y-m-d', $this->fecCompra2)->format('Y-m-d');
                    }
                    
                    DB::table('bertec_01_control_compras')->insert([
                        'nroComprobante' => $this->varComprobante,
                        'codArticulo'    => $articulo,
                        'fecCompra1'      => $fecha1,
                        'fecCompra2'      => $fecha2,
                        'fecModif'       => $this->fecCompra1 ? now() : null,
                        'comentarios1'    => $this->comentarios1 ?? null,
                        'comentarios2'    => $this->comentarios2 ?? null,
                        'entregaParc' => $this->entregaParc ? 1 : 2,
                        'user' => auth()->user()->name,
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ]);
                }
            } else {
                $fecha1 = null;
                if (!empty($this->fecCompra1)) {
                    $fecha1 = Carbon::createFromFormat('Y-m-d', $this->fecCompra1)->format('Y-m-d');
                }
                $fecha2 = null;
                if (!empty($this->fecCompra2)) {
                    $fecha2 = Carbon::createFromFormat('Y-m-d', $this->fecCompra2)->format('Y-m-d');
                }

                DB::table('bertec_01_control_compras')->insert([
                'nroComprobante' => $this->varComprobante,
                'codArticulo'    => $this->varCodArticulo,
                'fecCompra1'      => $fecha1,
                'fecCompra2'      => $fecha2,
                'fecModif'       => $this->fecCompra1 ? now() : null,
                'comentarios1'    => $this->comentarios1 ?? null,
                'comentarios2'    => $this->comentarios2 ?? null,
                'unidades1' => (int)$this->unidades1 ?? 0,
                'unidades2' => (int)$this->unidades2 ?? 0,
                'entregaParc' => $this->entregaParc ? 1 : 2,
                'user' => auth()->user()->name,
                'created_at'     => now(),
                'updated_at'     => now(),
                ]);
            }
        } else {
            // === ACTUALIZACIÓN ===
            if ($this->asignardtos_a == 1) {
                $items = DB::table('bertec_01_control_compras')
                    ->where('nroComprobante', $this->varComprobante)
                    ->get();
        
                foreach ($items as $item) {
                    $updateData = [
                        'comentarios1' => $this->comentarios1 ?? null,
                        'comentarios2' => $this->comentarios2 ?? null,
                        'entregaParc' => $this->entregaParc ? 1 : 2,
                        'user' => auth()->user()->name,
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
                    }
                    $updateData['fecCompra2'] = $fecha2;
                            
                    DB::table('bertec_01_control_compras')
                        ->where('id', $item->id)
                        ->update($updateData);
                }
            } else {
                $item = DB::table('bertec_01_control_compras')
                ->where('nroComprobante', $this->varComprobante)
                ->where('codArticulo', $this->varCodArticulo)
                    ->first();
                    
                $updateData = [
                    'comentarios1' => $this->comentarios1 ?? null,
                    'comentarios2' => $this->comentarios2 ?? null,
                    'unidades1' => (int)$this->unidades1 ?? 0,
                    'unidades2' => (int)$this->unidades2 ?? 0,
                    'entregaParc' => $this->entregaParc ? 1 : 2,
                    'user' => auth()->user()->name,
                    'updated_at'  => now(),
                ];
                
                if ($item && $item->fecCompra1 !== $this->fecCompra1) {
                    $fecha1 = null;
                    if (!empty($this->fecCompra1)) {
                        // Mantiene formato compatible con MySQL (YYYY-MM-DD)
                        $fecha1 = Carbon::createFromFormat('Y-m-d', $this->fecCompra1)->format('Y-m-d');
                    }

                    $updateData['fecCompra1'] = $fecha1;
                    $updateData['fecModif']  = now()->toDateString();
                }
                
                $fecha2 = null;
                if (!empty($this->fecCompra2)) {
                    // Mantiene formato compatible con MySQL (YYYY-MM-DD)
                    $fecha2 = Carbon::createFromFormat('Y-m-d', $this->fecCompra2)->format('Y-m-d');
                }
                $updateData['fecCompra2'] = $fecha2;

                DB::table('bertec_01_control_compras')
                    ->where('nroComprobante', $this->varComprobante)
                    ->where('codArticulo', $this->varCodArticulo)
                    ->update($updateData);
            }
        }

        $this->verForm = false;
        $this->LimpiarCampos();
        GeneradorTmp::TmpCompras(auth()->user()->usrGuid);
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
            'comentarios2',
            'unidades1',
            'unidades2',
            'entregaParc'
        ]);
    }

    public function Editar($param1, $param2, $param3, $param4){
        $this->resetErrorBag();
        $this->varComprobante = $param1;
        $this->varCodArticulo = $param2;
        $this->cant_pendientes = $param3;
        $this->varDescArticulo = $param4;

        // Buscar datos de auditoría en bertec_01_control_compras
        $dtosAudit = DB::table('bertec_01_control_compras')
            ->select('fecCompra1','fecCompra2','fecModif','comentarios1', 'comentarios2', 'unidades1', 'unidades2', 'entregaParc')
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
            $this->unidades1 = $dtosAudit->unidades1;
            $this->unidades2 = $dtosAudit->unidades2;
            $this->entregaParc = $dtosAudit->entregaParc == 1 ? true : false;

            $this->tipoDeGrabacion = 1;
        }

        $this->verForm = true;
    }

    public function Buscar(){
        $this->selectDatos();
    }

    protected function selectDatos(){
        // Normalizar textos (eliminar espacios a ambos lados)
        $this->txtBuscaNroCompras   = trim($this->txtBuscaNroCompras);
        $this->txtBuscaDescArtic  = trim($this->txtBuscaDescArtic);
        $this->txtBuscaRazSocial    = trim($this->txtBuscaRazSocial);

        // Construir query base
        $query = DB::table('bertec_01_tmp_compras')
            ->where('usrGuid', Auth::user()->usrGuid)
            ->when($this->txtBuscaNroCompras != '', function ($q) {
                $q->where('nro_compra', 'like', '%' . $this->txtBuscaNroCompras . '%');
            })
            ->when($this->txtBuscaDescArtic != '', function ($q) {
                $q->where('descrip', 'like', '%' . $this->txtBuscaDescArtic . '%');
            })
            ->when($this->txtBuscaRazSocial != '', function ($q) {
                $q->where('raz_social', 'like', '%' . $this->txtBuscaRazSocial . '%');
            });

            // Orden dinámico según variables
            if (isset($this->ordenarComo1) && $this->ordenarComo1 !== 'sin') {
                $query->orderBy('faltante', $this->ordenarComo1); // asc o desc
            }

            if (isset($this->ordenarComo2) && $this->ordenarComo2 !== 'sin') {
                $query->orderBy('fecCompra1', $this->ordenarComo2); // asc o desc
            }

            // Ejecutar consulta final
            $this->listRevCompras = $query->get();

        }

    // protected function selectDatos_old($columOrden = 0){
    //     $listadoFinal = [];
        
    //     // Normalizar textos (eliminar espacios a ambos lados)
    //     $this->txtBuscaNroCompras   = trim($this->txtBuscaNroCompras);
    //     $this->txtBuscaDescArtic  = trim($this->txtBuscaDescArtic);
    //     $this->txtBuscaRazSocial    = trim($this->txtBuscaRazSocial);

    //     // Construir query dinámica
    //     $list_compras = DB::table('bertec_01_compras_pend')
    //         ->when($this->txtBuscaNroCompras != '', function ($query) {
    //             $query->where('nro_compra', 'like', '%' . $this->txtBuscaNroCompras . '%');
    //         })
    //         ->when($this->txtBuscaDescArtic != '', function ($query) {
    //             $query->where('descrip', 'like', '%' . $this->txtBuscaDescArtic . '%');
    //         })
    //         ->when($this->txtBuscaRazSocial != '', function ($query) {
    //             $query->where('raz_social', 'like', '%' . $this->txtBuscaRazSocial . '%');
    //         })
    //         ->get();

    //     foreach ($list_compras as $compra) {
    //         // Buscar stock del artículo
    //         $stocks = DB::table('bertec_01_stock_depositos')
    //             ->selectRaw('SUM(saldo_ctrl_stock) as total_saldo_ctrl_stock, SUM(cant_comp_stock) as total_cant_comp_stock')
    //             ->where('cod_artic', $compra->cod_artic)
    //             ->first();
            
    //         // Buscar datos de auditoría en bertec_01_control_compras
    //         $dtosAudit = DB::table('bertec_01_control_compras')
    //             ->select('fecCompra1','fecCompra2','fecModif','comentarios1', 'comentarios2', 'unidades1', 'unidades2', 'entregaParc', 'user')
    //             ->where('nroComprobante', $compra->nro_compra)
    //             ->where('codArticulo', $compra->cod_artic)
    //             ->first();
            
    //         $fecCompra1='';
    //         $fecCompra2='';
    //         $fecModif='';
    //         $comentarios1='';
    //         $comentarios2='';
    //         $unidades1=0;
    //         $unidades2=0;
    //         $entregaParc='';
    //         $user='';

    //         $faltante = max(0, $stocks->total_cant_comp_stock - $stocks->total_saldo_ctrl_stock - $compra->cant_pendiente);

    //         if ($faltante<0)
    //             $faltante=0;

    //         if ($dtosAudit){
    //             $fecCompra1 = $dtosAudit->fecCompra1;
    //             $fecCompra2 = $dtosAudit->fecCompra2;
    //             $fecModif = $dtosAudit->fecModif;
    //             $comentarios1 = $dtosAudit->comentarios1;
    //             $comentarios2 = $dtosAudit->comentarios2;
    //             $unidades1 = $dtosAudit->unidades1;
    //             $unidades2 = $dtosAudit->unidades2;
    //             $entregaParc = $dtosAudit->entregaParc;
    //             $user = $dtosAudit->user;
    //         }

    //         $listadoFinal[] = [
    //             // Campos de compras
    //             'nro_compra'     => $compra->nro_compra,
    //             'cod_artic'      => $compra->cod_artic,
    //             'descripcion'    => $compra->descrip,
    //             'raz_social'     => $compra->raz_social,
    //             'cant_pedida'    => $compra->cant_pedida,
    //             'cant_recibida'  => $compra->cant_recibida,
    //             'cant_pendiente' => $compra->cant_pendiente,
    //             'moneda' => $compra->moneda,
    //             'cotiz' => $compra->cotiz,
    //             'fec_emision' => $compra->fec_emision,
    //             'fec_entrega' => $compra->fec_entrega,
    //             'faltante' => $faltante,
                
    //             // dtos de auditoria
    //             'fecCompra1' => $fecCompra1,
    //             'fecCompra2' => $fecCompra2,
    //             'fecModif' => $fecModif,
    //             'comentarios1' => $comentarios1,
    //             'comentarios2' => $comentarios2,
    //             'unidades1' => $unidades1,
    //             'unidades2' => $unidades2,
    //             'entregaParc' => $entregaParc,
    //             'user' => $user,

    //             // Campos de stock
    //             'saldo_ctrl_stock'  => $stocks->total_saldo_ctrl_stock,
    //             'cant_comp_stock'   => $stocks->total_cant_comp_stock
    //         ];
    //     }


    //     // Generar un GUID único para identificar este lote
    //     $usrGuid = (string) Str::uuid();

    //     // Insertar todos los registros del vector en la tabla temporal
    //     foreach ($listadoFinal as $item) {
    //         DB::table('bertec_01_compras_tmp')->insert([
    //             'usrGuid'           => $usrGuid,
    //             'nro_compra'        => $item['nro_compra'],
    //             'cod_artic'         => $item['cod_artic'],
    //             'descripcion'       => $item['descripcion'],
    //             'raz_social'        => $item['raz_social'],
    //             'cant_pedida'       => $item['cant_pedida'] ?? 0,
    //             'cant_recibida'     => $item['cant_recibida'] ?? 0,
    //             'cant_pendiente'    => $item['cant_pendiente'] ?? 0,
    //             'moneda'            => $item['moneda'],
    //             'cotiz'             => $item['cotiz'] ?? 0,
    //             'fec_emision'       => $this->parseFecha($item['fec_emision']),
    //             'fec_entrega'       => $this->parseFecha($item['fec_entrega']),
    //             'faltante'          => $item['faltante'] ?? 0,
    //             'fecCompra1'        => $this->parseFecha($item['fecCompra1']),
    //             'fecCompra2'        => $this->parseFecha($item['fecCompra2']),
    //             'fecModif'          => $this->parseFecha($item['fecModif']),
    //             'comentarios1'      => $item['comentarios1'] ?? '',
    //             'comentarios2'      => $item['comentarios2'] ?? '',
    //             'unidades1'         => $item['unidades1'] ?? 0,
    //             'unidades2'         => $item['unidades2'] ?? 0,
    //             'entregaParc'       => $item['entregaParc'] === '' ? 0 : $item['entregaParc'], // 🔹 clave
    //             'user'              => $item['user'] ?? '',
    //             'saldo_ctrl_stock'  => $item['saldo_ctrl_stock'] ?? 0,
    //             'cant_comp_stock'   => $item['cant_comp_stock'] ?? 0,
    //         ]);
    //     }


    //     if ($columOrden == 1){
    //         $this->ordenarComo2 = 'sin';
    //         $orden = ($this->ordenarComo1 == 'desc') ? 'desc' : 'asc';
    //         usort($listadoFinal, function($a, $b) use ($orden) {
    //             $fechaA = strtotime($a['fecCompra1']);
    //             $fechaB = strtotime($b['fecCompra1']);
    
    //             if ($orden === 'asc') {
    //                 return $fechaA <=> $fechaB; // Ascendente
    //             } else {
    //                 return $fechaB <=> $fechaA; // Descendente
    //             }
    //         });
    //     } else if ($columOrden == 2){
    //         $this->ordenarComo1 = 'sin';
    //         $orden = ($this->ordenarComo2 == 'desc') ? 'desc' : 'asc';
    //         usort($listadoFinal, function($a, $b) use ($orden) {
    //             $estA = $a['faltante'];
    //             $estB = $b['faltante'];
    //             if ($orden === 'asc') {
    //                 return $estA <=> $estB; // Ascendente
    //             } else {
    //                 return $estB <=> $estA; // Descendente
    //             }
    //         });
    //     }

    //     $this->listRevCompras = $listadoFinal;
    // }

    // protected function parseFecha($valor)
    // {
    //     if (empty($valor)) {
    //         return null;
    //     }

    //     // Intentar varios formatos posibles
    //     $formatos = ['d/m/Y H:i:s', 'd/m/Y'];

    //     foreach ($formatos as $formato) {
    //         try {
    //             return Carbon::createFromFormat($formato, $valor)->format('Y-m-d');
    //         } catch (\Exception $e) {
    //             // sigue probando
    //         }
    //     }

    //     return null; // si ninguno coincide
    // }    

    public function mount()
    {
        $this->selectDatos();
    }

    public function render()
    {
        return view('livewire.aplic.listrevcompras', ['listRevCompras' => $this->listRevCompras]);
    }
}
