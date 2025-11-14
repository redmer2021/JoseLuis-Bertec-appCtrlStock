<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class DescargasController extends Controller
{
    public function exportarRevCompras()
    {
        $listRevCompras = $this->selectDatos(1);

        // Nombre del archivo
        $fileName = 'revisiones_compras_' . date('Ymd_His') . '.csv';

        // Crear contenido CSV en memoria
        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, array_keys($listRevCompras[0]), ';'); // encabezados

        foreach ($listRevCompras as $row) {
            fputcsv($handle, $row, ';');
        }

        rewind($handle);
        $csvContent = stream_get_contents($handle);
        fclose($handle);

        // Devolver la descarga
        return Response::make($csvContent, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }    

    public function exportarRevVentas(){
        $listRevVentas = $this->selectDatos(2);

        // Nombre del archivo
        $fileName = 'revisiones_ventas_' . date('Ymd_His') . '.csv';

        // Crear contenido CSV en memoria
        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, array_keys($listRevVentas[0]), ';'); // encabezados

        foreach ($listRevVentas as $row) {
            fputcsv($handle, $row, ';');
        }

        rewind($handle);
        $csvContent = stream_get_contents($handle);
        fclose($handle);

        // Devolver la descarga
        return Response::make($csvContent, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    public function exportarRevDepositos(){
        $listRevDepositos = $this->selectDatos(3);

        // Nombre del archivo
        $fileName = 'revisiones_depositos_' . date('Ymd_His') . '.csv';

        // Crear contenido CSV en memoria
        $handle = fopen('php://temp', 'r+');
        fputcsv($handle, array_keys($listRevDepositos[0]), ';'); // encabezados

        foreach ($listRevDepositos as $row) {
            fputcsv($handle, $row, ';');
        }

        rewind($handle);
        $csvContent = stream_get_contents($handle);
        fclose($handle);

        // Devolver la descarga
        return Response::make($csvContent, 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ]);
    }

    protected function selectDatos($queListado = 0){
        $listadoFinal = [];
        switch($queListado){
            case 1:
                // Recuperar los valores desde la sesión
                $nroCompras = session('txtBuscaNroCompras');
                $descArtic = session('txtBuscaDescArtic');
                $razSocial = session('txtBuscaRazSocial');
                // Construir query dinámica
                $list_compras = DB::table('bertec_01_compras_pend')
                    ->when($nroCompras != '', function ($query) use ($nroCompras) {
                        $query->where('nro_compra', 'like', '%' . $nroCompras . '%');
                    })
                    ->when($descArtic != '', function ($query) use ($descArtic) {
                        $query->where('descrip', 'like', '%' . $descArtic . '%');
                    })
                    ->when($razSocial != '', function ($query) use ($razSocial) {
                        $query->where('raz_social', 'like', '%' . $razSocial . '%');
                    })
                    ->get();
        
                foreach ($list_compras as $compra) {
                    // Buscar stock del artículo
                    $stocks = DB::table('bertec_01_stock_depositos')
                        ->selectRaw('SUM(saldo_ctrl_stock) as total_saldo_ctrl_stock, SUM(cant_comp_stock) as total_cant_comp_stock')
                        ->where('cod_artic', $compra->cod_artic)
                        ->first();
                    
                    // Buscar datos de auditoría en bertec_01_control_compras
                    $dtosAudit = DB::table('bertec_01_control_compras')
                        ->select('fecCompra1','fecCompra2','fecModif','comentarios1', 'comentarios2', 'unidades1', 'unidades2', 'entregaParc', 'user')
                        ->where('nroComprobante', $compra->nro_compra)
                        ->where('codArticulo', $compra->cod_artic)
                        ->first();
                    
                    $fecCompra1='';
                    $fecModif='';
                    $comentarios1='';
                    $user='';
        
                    $faltante = max(0, $stocks->total_cant_comp_stock - $stocks->total_saldo_ctrl_stock - $compra->cant_pendiente);
        
                    if ($faltante<0)
                        $faltante=0;
        
                    if ($dtosAudit){
                        $fecCompra1 = $dtosAudit->fecCompra1;
                        $fecModif = $dtosAudit->fecModif;
                        $comentarios1 = $dtosAudit->comentarios1;
                        $user = $dtosAudit->user;
                    }
        
                    $listadoFinal[] = [
                        'nro_compra'     => $compra->nro_compra,
                        'articulo'      => $compra->cod_artic,
                        'descripcion'    => $compra->descrip,
                        'pedida'    => (int)$compra->cant_pedida,
                        'pend' => (int)$compra->cant_pendiente,
                        'stock'  => (int)$stocks->total_saldo_ctrl_stock,
                        'compr'   => (int)$stocks->total_cant_comp_stock,
                        'falt' => (int)$faltante,
                        'feemoc' => $compra->fec_emision,
                        'fentroc' => $compra->fec_entrega,
                        'fecCompra1' => $fecCompra1,
                        'fecModif' => $fecModif,
                        'comentarios1' => $comentarios1,
                        'user' => $user,
                        'raz_social'     => $compra->raz_social
                    ];
                }
                
                break;
            case 2:
                // Recuperar los valores desde la sesión
                $ordenComp = session('txtBuscaOrdenComp');
                $nroVentas = session('txtBuscaNroVentas');
                $descArtic = session('txtBuscaDescArtic');
                $razSocial = session('txtBuscaRazSocial');
                $fecIngresoStock = session('txtFecIngresoStock');

                // Construir query dinámica
                $list_ventas = DB::table('vta_bertec_01_pend_entrega')
                    ->when($ordenComp != '', function ($query) use ($ordenComp) {
                        $query->where('nro_o_compra', 'like', '%' . $ordenComp . '%');
                    })
                    ->when($nroVentas != '', function ($query) use($nroVentas) {
                        $query->where('nro_pedido', 'like', '%' . $nroVentas . '%');
                    })
                    ->when($descArtic != '', function ($query) use($descArtic) {
                        $query->where('descrip', 'like', '%' . $descArtic . '%');
                    })
                    ->when($razSocial != '', function ($query) use ($razSocial) {
                        $query->where('raz_social', 'like', '%' . $razSocial . '%');
                    })
                    ->get();

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

                    if (!empty($ventas->cotiza) && $ventas->cotiza != 0 && !empty($ventas->pend_factu) && $ventas->pend_factu != 0) {        
                        $impoDolariz = ($ventas->importe / $ventas->pend_factu) / $ventas->cotiza;
                    } else {
                        $impoDolariz = 0;
                    }
                
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
                    }

                    $precLista = DB::table('bertec_articulos')
                        ->select('precio')
                        ->where('cod_articulo', $ventas->cod_artic)
                        ->first();

                    //Calcular diferencia porcentual
                    $difPorcentual = 0;
                    if ($impoDolariz > 0 && $impoDolariz < ($precLista->precio ?? 0)) {
                        $diferencia = $precLista->precio - $impoDolariz;
                        $difPorcentual = ($diferencia / $precLista->precio) * 100;
                    }

                    // revisión stock dia anterior
                    $dtosStockDiaAnt = DB::table('bertec_01_stock_anterior')
                        ->select('t1_fecha_ingreso','t1_cod_articu','t1_cantidad')
                        ->where('t1_cod_articu', $ventas->cod_artic)
                        ->where('t1_fecha_ingreso', $fecIngresoStock)
                        ->first();
                    $t1_cantidad = 0;
                    if ($dtosStockDiaAnt){
                        $t1_cantidad = $dtosStockDiaAnt->t1_cantidad;
                    }

                    $listadoFinal[] = [
                        'nro_pedido' => $ventas->nro_pedido,
                        'articulo' => $ventas->cod_artic,
                        'descripcion' => $ventas->descrip,
                        'item' => (int)$ventas->renglon,
                        'pedida' => (int)$ventas->cant_pedida,
                        'pend' => (int)$ventas->pend_desc,
                        'stock'  => (int)$stocks->total_saldo_ctrl_stock,
                        'compr'   => (int)$stocks->total_cant_comp_stock,
                        'Arecibir' => (int)$aRecibir,
                        'falt' => (int)$faltante,
                        'ingr' => (int)$t1_cantidad,
                        'impoDolariz' => round($impoDolariz,2),
                        'precLista' => round($precLista?->precio ?? 0, 2),
                        'difPorcentual' => round($difPorcentual,2),
                        'codEstado' => $codEstado,
                        'fecModifEstado' => $fecModifEstado,
                        'user' => $user,
                        'comentarios' => $comentarios,
                        'compras_feccompra' => $compras_feccompra,
                        'compras_fecmodif' => $compras_fecmodif,
                        'compras_comentrarios' => $compras_comentrarios,
                        'f_pedido' => $ventas->fec_pedido,
                        'pl_ent' => $ventas->plan_entrega,
                        'diasv' => (int)$difDiasPlanEntrega,
                        'vend' => $ventas->cod_vend,
                        'razon_social' => $ventas->raz_social,
                        'nro_o_compra' => $ventas->nro_o_compra
                    ];
                }
                break;
            case 3:
                // Recuperar los valores desde la sesión
                $nroVentas = session('txtBuscaNroVentas');
                $descArtic = session('txtBuscaDescArtic');
                $razSocial = session('txtBuscaRazSocial');
                $nroOrdenCompra = session('txtBuscaNroOrdenCompra');
                $fecIngresoStock = session('txtFecIngresoStock');

                $list_ventas = DB::table('bertec_01_pend_entrega')
                ->when($nroVentas != '', function ($query) use($nroVentas) {
                    $query->where('nro_pedido', 'like', '%' . $nroVentas . '%');
                })
                ->when($descArtic != '', function ($query) use($descArtic) {
                    $query->where('descrip', 'like', '%' . $descArtic . '%');
                })
                ->when($razSocial != '', function ($query) use($razSocial) {
                    $query->where('raz_social', 'like', '%' . $razSocial . '%');
                })
                ->when($nroOrdenCompra != '', function ($query) use($nroOrdenCompra) {
                    $query->where('nro_o_compra', 'like', '%' . $nroOrdenCompra . '%');
                })
                ->get();        
                
                foreach ($list_ventas as $ventas) {
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

                    if ($dtosAudit){
                        $codEstado = $dtosAudit->codEstado == 1 ? 'PAD' : 'PAC';
                        $comentarios = $dtosAudit->comentarios;
                        $fecModifEstado = $dtosAudit->fecModifEstado;
                        $user = $dtosAudit->user;
                        $codColor = $dtosAudit->codColor;
                    }

                    if ($codColor == 1){
                        // revisión stock dia anterior
                        $dtosStockDiaAnt = DB::table('bertec_01_stock_anterior')
                            ->select('t1_fecha_ingreso','t1_cod_articu','t1_cantidad')
                            ->where('t1_cod_articu', $ventas->cod_artic)
                            ->where('t1_fecha_ingreso', $fecIngresoStock)                    
                            ->first();
                        $t1_cantidad = 0;
                        if ($dtosStockDiaAnt){
                            $t1_cantidad = $dtosStockDiaAnt->t1_cantidad;
                        }

                        // Buscar stock del artículo
                        $stocks = DB::table('bertec_01_stock_depositos')
                            ->selectRaw('SUM(saldo_ctrl_stock) as total_saldo_ctrl_stock')
                            ->where('cod_artic', $ventas->cod_artic)
                            ->first();

                        $listadoFinal[] = [
                            'nro_ped_vta' => $ventas->nro_pedido,
                            'articulo' => $ventas->cod_artic,
                            'descripcion' => $ventas->descrip,
                            'item' => (int)$ventas->renglon,
                            'pedida' => (int)$ventas->cant_pedida,
                            'pend' => (int)$ventas->pend_desc,
                            'stock'  => (int)$stocks->total_saldo_ctrl_stock,
                            'ingr' => (int)$t1_cantidad,
                            'estado' => $codEstado,
                            'comentarios' => $comentarios,
                            'fecModifEstado' => $fecModifEstado,
                            'user' => $user,
                            'f_pedido' => $ventas->fec_pedido,
                            'planentr' => $ventas->plan_entrega,
                            'vend' => $ventas->cod_vend,
                            'razon_social' => $ventas->raz_social,
                            'nro_o_compra' => $ventas->nro_o_compra
                        ];
                    }
                }

                break;
        }

        return $listadoFinal;
    }

}
