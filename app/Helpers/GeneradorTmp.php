<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class GeneradorTmp
{
    static function parseFecha($valor)
    {
        if (empty($valor)) {
            return null;
        }

        // Intentar varios formatos posibles
        $formatos = ['d/m/Y H:i:s', 'd/m/Y'];

        foreach ($formatos as $formato) {
            try {
                return Carbon::createFromFormat($formato, $valor)->format('Y-m-d');
            } catch (\Exception $e) {
                // sigue probando
            }
        }

        return null; // si ninguno coincide
    }    

    public static function TmpCompras($guid){
        $listadoFinal = [];

        //PROCESAMIENTO ARCHIVO COMPRAS
        //$list_compras = DB::table('bertec_01_compras_pend')->get();
        $list_compras = DB::table('bertec_01_compras_pend as c')
            ->joinSub(
                DB::table('bertec_01_compras_pend')
                    ->selectRaw("
                        nro_compra,
                        cod_artic,
                        MIN(STR_TO_DATE(fec_emision, '%d/%m/%Y %H:%i:%s')) as fec_min
                    ")
                    ->groupBy('nro_compra', 'cod_artic'),
                'x',
                function ($join) {
                    $join->on('c.nro_compra', '=', 'x.nro_compra')
                        ->on('c.cod_artic', '=', 'x.cod_artic')
                        ->whereRaw("STR_TO_DATE(c.fec_emision, '%d/%m/%Y %H:%i:%s') = x.fec_min");
                }
            )
            ->orderBy('c.id')
            ->get()
            ->unique(fn($c) => $c->nro_compra.'|'.$c->cod_artic)
        ->values();
            
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
            $fecCompra2='';
            $fecModif='';
            $comentarios1='';
            $comentarios2='';
            $unidades1=0;
            $unidades2=0;
            $entregaParc='';
            $user='';

            $faltante = max(0, $stocks->total_cant_comp_stock - $stocks->total_saldo_ctrl_stock - $compra->cant_pendiente);

            if ($faltante<0)
                $faltante=0;

            if ($dtosAudit){
                $fecCompra1 = $dtosAudit->fecCompra1;
                $fecCompra2 = $dtosAudit->fecCompra2;
                $fecModif = $dtosAudit->fecModif;
                $comentarios1 = $dtosAudit->comentarios1;
                $comentarios2 = $dtosAudit->comentarios2;
                $unidades1 = $dtosAudit->unidades1;
                $unidades2 = $dtosAudit->unidades2;
                $entregaParc = $dtosAudit->entregaParc;
                $user = $dtosAudit->user;
            }

            $listadoFinal[] = [
                // Campos de compras
                'nro_compra'     => $compra->nro_compra,
                'cod_artic'      => $compra->cod_artic,
                'descrip'        => $compra->descrip,
                'raz_social'     => $compra->raz_social,
                'cant_pedida'    => $compra->cant_pedida,
                'cant_recibida'  => $compra->cant_recibida,
                'cant_pendiente' => $compra->cant_pendiente,
                'moneda' => $compra->moneda,
                'cotiz' => $compra->cotiz,
                'fec_emision' => $compra->fec_emision ?: null,
                'fec_entrega' => $compra->fec_entrega ?: null,
                'faltante' => $faltante,
                
                // dtos de auditoria
                'fecCompra1' => $fecCompra1 ?: null,
                'fecCompra2' => $fecCompra2 ?: null,
                'fecModif' => $fecModif ?: null,
                'comentarios1' => $comentarios1,
                'comentarios2' => $comentarios2,
                'unidades1' => $unidades1,
                'unidades2' => $unidades2,
                'entregaParc' => $entregaParc,
                'user' => $user,

                // Campos de stock
                'saldo_ctrl_stock'  => $stocks->total_saldo_ctrl_stock,
                'cant_comp_stock'   => $stocks->total_cant_comp_stock
            ];
        }

        // 🔹 Eliminar registros anteriores del mismo usrGuid
        DB::table('bertec_01_tmp_compras')->where('usrGuid', $guid)->delete();

        // Insertar todos los registros del vector en la tabla temporal
        foreach ($listadoFinal as $item) {
            DB::table('bertec_01_tmp_compras')->insert([
                'usrGuid'           => $guid,
                'nro_compra'        => $item['nro_compra'],
                'cod_artic'         => $item['cod_artic'],
                'descrip'           => $item['descrip'],
                'raz_social'        => $item['raz_social'],
                'cant_pedida'       => $item['cant_pedida'] ?? 0,
                'cant_recibida'     => $item['cant_recibida'] ?? 0,
                'cant_pendiente'    => $item['cant_pendiente'] ?? 0,
                'moneda'            => $item['moneda'],
                'cotiz'             => $item['cotiz'] ?? 0,
                'fec_emision'       =>  Carbon::createFromFormat('d/m/Y H:i:s', $item['fec_emision'])->format('Y-m-d'),
                'fec_entrega'       =>  Carbon::createFromFormat('d/m/Y H:i:s', $item['fec_entrega'])->format('Y-m-d'),
                'faltante'          => $item['faltante'] ?? 0,
                'fecCompra1'        => $item['fecCompra1'] ?? null,
                'fecCompra2'        => $item['fecCompra2'] ?? null,
                'fecModif'          => $item['fecModif'] ?? null,
                'comentarios1'      => $item['comentarios1'] ?? '',
                'comentarios2'      => $item['comentarios2'] ?? '',
                'unidades1'         => $item['unidades1'] ?? 0,
                'unidades2'         => $item['unidades2'] ?? 0,
                'entregaParc'       => $item['entregaParc'] === '' ? 0 : $item['entregaParc'], // 🔹 clave
                'user'              => $item['user'] ?? '',
                'saldo_ctrl_stock'  => $item['saldo_ctrl_stock'] ?? 0,
                'cant_comp_stock'   => $item['cant_comp_stock'] ?? 0,
            ]);
        }        
    }

    public static function TmpVentas($guid){
        // 🔹 Recuperar la fecha desde la sesión
        $fecIngresoStock = session('fechaIngresoStock');
        // revisión stock dia anterior
        $fecIngresoStock = \Carbon\Carbon::parse($fecIngresoStock)->format('d/m/Y') . ' 0:00:00';

        $listadoFinal = [];
        //todo: 05/02/2026 modificar esta consulta para agregar pje_desc
        $list_ventas = DB::table('vta_bertec_01_pend_entrega')->get();

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
                
                // Aplicar descuento si existe
                if (!empty($ventas->pje_desc) && $ventas->pje_desc > 0 && $impoDolariz > 0) {
                    $impoDolariz -= $impoDolariz * ($ventas->pje_desc / 100);
                }
            } else {
                $impoDolariz = 0;
            }
        
            // Buscar datos de auditoría en bertec_01_control_ventas
            $dtosAudit = DB::table('bertec_01_control_ventas')
                ->select('codEstado','comentarios','fecModifEstado','user', 'codColor', 'codCtrl')
                ->where('nroComprobante', $ventas->nro_pedido)
                ->where('codArticulo', $ventas->cod_artic)
                ->first();

            $codEstado=0;
            $comentarios='';
            $fecModifEstado='';
            $user='';
            $codColor=2;
            $codCtrl=0;
            $difDiasPlanEntrega=0;
           
            $rawFecha = trim($ventas->plan_entrega);
            $fechaPlan = null;
            // Evitar error si viene vacío
            if (!empty($rawFecha)) {

                // Intentar con varios formatos posibles
                $formatos = [
                    'd/m/Y H:i:s',
                    'd/m/Y H:i',
                    'd/m/Y', // por si viene sin hora
                ];

                foreach ($formatos as $formato) {
                    try {
                        $fechaPlan = Carbon::createFromFormat($formato, $rawFecha);
                        break; // si funcionó, salir del loop
                    } catch (\Exception $e) {
                        // continuar probando
                    }
                }

                // Si no coincidió ningún formato → setear null para evitar el error
                if (!$fechaPlan) {
                    $fechaPlan = null;
                }
            }

            $hoy = Carbon::now(); // Fecha actual
            // Calcula la diferencia en días (puede ser positiva o negativa)
            $difDiasPlanEntrega = $fechaPlan
                ? (int) round($fechaPlan->diffInDays($hoy, false))
                : 0;
            
            if ($dtosAudit){
                $codEstado = $dtosAudit->codEstado;
                $comentarios = $dtosAudit->comentarios;
                $fecModifEstado = $dtosAudit->fecModifEstado;
                $user = $dtosAudit->user;
                $codColor = $dtosAudit->codColor;
                $codCtrl = $dtosAudit->codCtrl;
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
            $colorCelda = 0;
            if ($difPorcentual>=0.1 and $difPorcentual<=20){
                $colorCelda = 1;
            } else if ($difPorcentual > 20 and $difPorcentual<=25){
                $colorCelda = 2;
            } else if ($difPorcentual>25){
                $colorCelda = 3;
            }

            if ($precLista && $impoDolariz == $precLista->precio && $impoDolariz != 0) {
                $colorCelda = 1;
            }

            if (
                $precLista !== null &&
                $impoDolariz > 0 &&
                $precLista->precio > 0 &&
                $impoDolariz > $precLista->precio
            ) {
                $colorCelda = 1;
            }
            
            $t1_cantidad = 0;
            $t1_cantidad = DB::table('bertec_01_stock_anterior')
                ->where('t1_cod_articu', $ventas->cod_artic)
                ->where('t1_fecha_ingreso', $fecIngresoStock)
                ->sum('t1_cantidad');

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
                'codCtrl' => $codCtrl,

                // dtos de compras
                'compras_feccompra' => $compras_feccompra,
                'compras_fecmodif' => $compras_fecmodif,
                'compras_comentrarios' => $compras_comentrarios,
                'precLista' => $precLista?->precio ?? 0,
                'difPorcentual' => $difPorcentual,
                'colorCelda' => $colorCelda,
                't1_cantidad' => $t1_cantidad,
            ];
        }

        // 🔹 Eliminar registros anteriores del mismo usrGuid
        DB::table('bertec_01_tmp_ventas')->where('usrGuid', $guid)->delete();

        foreach ($listadoFinal as $item) {
            DB::table('bertec_01_tmp_ventas')->insert([
                'usrGuid'           => $guid,
                'nro_pedido' => $item['nro_pedido'],
                'cod_artic' => $item['cod_artic'],
                'descrip' => $item['descrip'],
                'renglon' => $item['renglon'] ?? 0,
                'cant_pedida' => $item['cant_pedida'] ?? 0,
                'pend_desc' => $item['pend_desc'] ?? 0,
                'saldo_ctrl_stock' => $item['saldo_ctrl_stock'] ?? 0,
                'cant_comp_stock' => $item['cant_comp_stock'] ?? 0,
                'aRecibir' => $item['aRecibir'] ?? 0,
                'fec_pedido' => self::parseFecha($item['fec_pedido']),
                'plan_entrega' => self::parseFecha($item['plan_entrega']),
                'difDiasPlanEntrega' => $item['difDiasPlanEntrega'] ?? 0,
                'cod_vend' => $item['cod_vend'],
                'raz_social' => $item['raz_social'],
                'nro_o_compra' => $item['nro_o_compra'],
                'impoDolariz' => $item['impoDolariz'] ?? 0,
                'faltante' => $item['faltante'] ?? 0,
                'codEstado' => $item['codEstado'] ?? 0,
                'comentarios' => $item['comentarios'],
                'fecModifEstado' => $item['fecModifEstado'] ?: null,
                'user' => $item['user'],
                'codColor' => $item['codColor'] ?? 0,
                'compras_feccompra' => $item['compras_feccompra'] ?: null,
                'compras_fecmodif' => $item['compras_fecmodif'] ?: null,
                'compras_comentrarios' => $item['compras_comentrarios'],
                'precLista' => $item['precLista'] ?? 0,
                'difPorcentual' => $item['difPorcentual'] ?? 0,
                'colorCelda' => $item['colorCelda'] ?? 0,
                'codCtrl' => $item['codCtrl'] ?? 0,                
                't1_cantidad' => $item['t1_cantidad'] ?? 0
            ]);
        }        
    }

}
