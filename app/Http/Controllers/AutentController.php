<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\DB;
use App\Helpers\GeneradorTmp;

class AutentController extends Controller
{
    public function login(Request $request){
       
        $credenciles = $request->validate(
        [
            'email' => ['required', 'email'],
            'password' => ['required']
        ],
        [
            'email.required'=>'Debe ingresar Email',
            'email.email'=>'Email no válido',
            'password.required'=>'Debe ingresar Contraseña',
        ]);

        if (Auth::attempt($credenciles)){
            $request->session()->regenerate();
            // Obtener el usrGuid del usuario logueado
            $usrGuid = Auth::user()->usrGuid ?? null;

            if ($usrGuid) {
                // Obtener la fecha desde el formulario
                $fechaIngreso = $request->input('txtFecIngresoStock');

                // 🔹 Guardar la fecha en la sesión
                session(['fechaIngresoStock' => $fechaIngreso]);
                
                // Ejecutar el método pasando el usrGuid
                GeneradorTmp::TmpCompras($usrGuid);
                GeneradorTmp::TmpVentas($usrGuid);
                
            }

            return  redirect('UsrAutoriz');
        } else {
                throw ValidationException::withMessages(['credNoValidas'=>'Las credenciales ingresadas son incorrectas. Acceso Denegado']);
                //return  redirect('PantallaLogin');
        }
    }

    // public function TmpCompras($guid){
    //     $listadoFinal = [];

    //     //PROCESAMIENTO ARCHIVO COMPRAS
    //     $list_compras = DB::table('bertec_01_compras_pend')->get();

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
    //             'descrip'        => $compra->descrip,
    //             'raz_social'     => $compra->raz_social,
    //             'cant_pedida'    => $compra->cant_pedida,
    //             'cant_recibida'  => $compra->cant_recibida,
    //             'cant_pendiente' => $compra->cant_pendiente,
    //             'moneda' => $compra->moneda,
    //             'cotiz' => $compra->cotiz,
    //             'fec_emision' => $compra->fec_emision ?: null,
    //             'fec_entrega' => $compra->fec_entrega ?: null,
    //             'faltante' => $faltante,
                
    //             // dtos de auditoria
    //             'fecCompra1' => $fecCompra1 ?: null,
    //             'fecCompra2' => $fecCompra2 ?: null,
    //             'fecModif' => $fecModif ?: null,
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

    //     // 🔹 Eliminar registros anteriores del mismo usrGuid
    //     DB::table('bertec_01_tmp_compras')->where('usrGuid', $guid)->delete();

    //     // Insertar todos los registros del vector en la tabla temporal
    //     foreach ($listadoFinal as $item) {
    //         DB::table('bertec_01_tmp_compras')->insert([
    //             'usrGuid'           => $guid,
    //             'nro_compra'        => $item['nro_compra'],
    //             'cod_artic'         => $item['cod_artic'],
    //             'descrip'           => $item['descrip'],
    //             'raz_social'        => $item['raz_social'],
    //             'cant_pedida'       => $item['cant_pedida'] ?? 0,
    //             'cant_recibida'     => $item['cant_recibida'] ?? 0,
    //             'cant_pendiente'    => $item['cant_pendiente'] ?? 0,
    //             'moneda'            => $item['moneda'],
    //             'cotiz'             => $item['cotiz'] ?? 0,
    //             'fec_emision'       =>  Carbon::createFromFormat('d/m/Y H:i:s', $item['fec_emision'])->format('Y-m-d'),
    //             'fec_entrega'       =>  Carbon::createFromFormat('d/m/Y H:i:s', $item['fec_entrega'])->format('Y-m-d'),
    //             'faltante'          => $item['faltante'] ?? 0,
    //             'fecCompra1'        => $item['fecCompra1'] ?? null,
    //             'fecCompra2'        => $item['fecCompra2'] ?? null,
    //             'fecModif'          => $item['fecModif'] ?? null,
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
    // }

    // public function TmpVentas($guid, $fechaIngreso){
    //     $fecIngresoStock = $fechaIngreso;
    //     $listadoFinal = [];
    //     $list_ventas = DB::table('vta_bertec_01_pend_entrega')->get();

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
    //             ->select('codEstado','comentarios','fecModifEstado','user', 'codColor', 'codCtrl')
    //             ->where('nroComprobante', $ventas->nro_pedido)
    //             ->where('codArticulo', $ventas->cod_artic)
    //             ->first();

    //         $codEstado=0;
    //         $comentarios='';
    //         $fecModifEstado='';
    //         $user='';
    //         $codColor=2;
    //         $codCtrl=0;
    //         $difDiasPlanEntrega=0;

    //         $fechaPlan = Carbon::createFromFormat('d/m/Y H:i:s', $ventas->plan_entrega);
    //         $hoy = Carbon::now(); // Fecha actual
            
    //         // Calcula la diferencia en días (puede ser positiva o negativa)
    //         $difDiasPlanEntrega = (int) round($fechaPlan->diffInDays($hoy, false));
            
    //         if ($dtosAudit){
    //             $codEstado = $dtosAudit->codEstado;
    //             $comentarios = $dtosAudit->comentarios;
    //             $fecModifEstado = $dtosAudit->fecModifEstado;
    //             $user = $dtosAudit->user;
    //             $codColor = $dtosAudit->codColor;
    //             $codCtrl = $dtosAudit->codCtrl;
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
    //             ->where('t1_fecha_ingreso', $fecIngresoStock)
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
    //             'codCtrl' => $codCtrl,

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

    //     // 🔹 Eliminar registros anteriores del mismo usrGuid
    //     DB::table('bertec_01_tmp_ventas')->where('usrGuid', $guid)->delete();

    //     foreach ($listadoFinal as $item) {
    //         DB::table('bertec_01_tmp_ventas')->insert([
    //             'usrGuid'           => $guid,
    //             'nro_pedido' => $item['nro_pedido'],
    //             'cod_artic' => $item['cod_artic'],
    //             'descrip' => $item['descrip'],
    //             'renglon' => $item['renglon'] ?? 0,
    //             'cant_pedida' => $item['cant_pedida'] ?? 0,
    //             'pend_desc' => $item['pend_desc'] ?? 0,
    //             'saldo_ctrl_stock' => $item['saldo_ctrl_stock'] ?? 0,
    //             'cant_comp_stock' => $item['cant_comp_stock'] ?? 0,
    //             'aRecibir' => $item['aRecibir'] ?? 0,
    //             'fec_pedido' => $this->parseFecha($item['fec_pedido']),
    //             'plan_entrega' => $this->parseFecha($item['plan_entrega']),
    //             'difDiasPlanEntrega' => $item['difDiasPlanEntrega'] ?? 0,
    //             'cod_vend' => $item['cod_vend'],
    //             'raz_social' => $item['raz_social'],
    //             'nro_o_compra' => $item['nro_o_compra'],
    //             'impoDolariz' => $item['impoDolariz'] ?? 0,
    //             'faltante' => $item['faltante'] ?? 0,
    //             'codEstado' => $item['codEstado'] ?? 0,
    //             'comentarios' => $item['comentarios'],
    //             'fecModifEstado' => $this->parseFecha($item['fecModifEstado']),
    //             'user' => $item['user'],
    //             'codColor' => $item['codColor'] ?? 0,
    //             'compras_feccompra' => $this->parseFecha($item['compras_feccompra']),
    //             'compras_fecmodif' => $this->parseFecha($item['compras_fecmodif']),
    //             'compras_comentrarios' => $item['compras_comentrarios'],
    //             'precLista' => $item['precLista'] ?? 0,
    //             'difPorcentual' => $item['difPorcentual'] ?? 0,
    //             'colorCelda' => $item['colorCelda'] ?? 0,
    //             'codCtrl' => $item['codCtrl'] ?? 0,                
    //             't1_cantidad' => $item['t1_cantidad'] ?? 0
    //         ]);
    //     }        
    // }


    // protected function GenerarTemporales($guid, $fechaIngreso){

    //     $fecIngresoStock = $fechaIngreso;

    //     $listadoFinal = [];

    //     //PROCESAMIENTO ARCHIVO COMPRAS
    //     $list_compras = DB::table('bertec_01_compras_pend')->get();

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
    //             'descrip'        => $compra->descrip,
    //             'raz_social'     => $compra->raz_social,
    //             'cant_pedida'    => $compra->cant_pedida,
    //             'cant_recibida'  => $compra->cant_recibida,
    //             'cant_pendiente' => $compra->cant_pendiente,
    //             'moneda' => $compra->moneda,
    //             'cotiz' => $compra->cotiz,
    //             'fec_emision' => $compra->fec_emision ?: null,
    //             'fec_entrega' => $compra->fec_entrega ?: null,
    //             'faltante' => $faltante,
                
    //             // dtos de auditoria
    //             'fecCompra1' => $fecCompra1 ?: null,
    //             'fecCompra2' => $fecCompra2 ?: null,
    //             'fecModif' => $fecModif ?: null,
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

    //     // 🔹 Eliminar registros anteriores del mismo usrGuid
    //     DB::table('bertec_01_tmp_compras')->where('usrGuid', $guid)->delete();

    //     // Insertar todos los registros del vector en la tabla temporal
    //     foreach ($listadoFinal as $item) {
    //         DB::table('bertec_01_tmp_compras')->insert([
    //             'usrGuid'           => $guid,
    //             'nro_compra'        => $item['nro_compra'],
    //             'cod_artic'         => $item['cod_artic'],
    //             'descrip'           => $item['descrip'],
    //             'raz_social'        => $item['raz_social'],
    //             'cant_pedida'       => $item['cant_pedida'] ?? 0,
    //             'cant_recibida'     => $item['cant_recibida'] ?? 0,
    //             'cant_pendiente'    => $item['cant_pendiente'] ?? 0,
    //             'moneda'            => $item['moneda'],
    //             'cotiz'             => $item['cotiz'] ?? 0,
    //             'fec_emision'       => $this->parseFecha($item['fec_emision']),
    //             'fec_entrega'       => $this->parseFecha($item['fec_entrega']),
    //             'faltante'          => $item['faltante'] ?? 0,
    //             'fecCompra1'        => $item['fecCompra1'],
    //             'fecCompra2'        => $item['fecCompra2'],
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




    //     //PROCESAMIENTO ARCHIVO VENTAS
    //     $listadoFinal = [];
    //     $list_ventas = DB::table('vta_bertec_01_pend_entrega')->get();

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
    //             ->select('codEstado','comentarios','fecModifEstado','user', 'codColor', 'codCtrl')
    //             ->where('nroComprobante', $ventas->nro_pedido)
    //             ->where('codArticulo', $ventas->cod_artic)
    //             ->first();

    //         $codEstado=0;
    //         $comentarios='';
    //         $fecModifEstado='';
    //         $user='';
    //         $codColor=2;
    //         $codCtrl=0;
    //         $difDiasPlanEntrega=0;

    //         $fechaPlan = Carbon::createFromFormat('d/m/Y H:i:s', $ventas->plan_entrega);
    //         $hoy = Carbon::now(); // Fecha actual
            
    //         // Calcula la diferencia en días (puede ser positiva o negativa)
    //         $difDiasPlanEntrega = (int) round($fechaPlan->diffInDays($hoy, false));
            
    //         if ($dtosAudit){
    //             $codEstado = $dtosAudit->codEstado;
    //             $comentarios = $dtosAudit->comentarios;
    //             $fecModifEstado = $dtosAudit->fecModifEstado;
    //             $user = $dtosAudit->user;
    //             $codColor = $dtosAudit->codColor;
    //             $codCtrl = $dtosAudit->codCtrl;
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
    //             ->where('t1_fecha_ingreso', $fecIngresoStock)
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
    //             'codCtrl' => $codCtrl,

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

    //     // 🔹 Eliminar registros anteriores del mismo usrGuid
    //     DB::table('bertec_01_tmp_ventas')->where('usrGuid', $guid)->delete();

    //     foreach ($listadoFinal as $item) {
    //         DB::table('bertec_01_tmp_ventas')->insert([
    //             'usrGuid'           => $guid,
    //             'nro_pedido' => $item['nro_pedido'],
    //             'cod_artic' => $item['cod_artic'],
    //             'descrip' => $item['descrip'],
    //             'renglon' => $item['renglon'] ?? 0,
    //             'cant_pedida' => $item['cant_pedida'] ?? 0,
    //             'pend_desc' => $item['pend_desc'] ?? 0,
    //             'saldo_ctrl_stock' => $item['saldo_ctrl_stock'] ?? 0,
    //             'cant_comp_stock' => $item['cant_comp_stock'] ?? 0,
    //             'aRecibir' => $item['aRecibir'] ?? 0,
    //             'fec_pedido' => $this->parseFecha($item['fec_pedido']),
    //             'plan_entrega' => $this->parseFecha($item['plan_entrega']),
    //             'difDiasPlanEntrega' => $item['difDiasPlanEntrega'] ?? 0,
    //             'cod_vend' => $item['cod_vend'],
    //             'raz_social' => $item['raz_social'],
    //             'nro_o_compra' => $item['nro_o_compra'],
    //             'impoDolariz' => $item['impoDolariz'] ?? 0,
    //             'faltante' => $item['faltante'] ?? 0,
    //             'codEstado' => $item['codEstado'] ?? 0,
    //             'comentarios' => $item['comentarios'],
    //             'fecModifEstado' => $this->parseFecha($item['fecModifEstado']),
    //             'user' => $item['user'],
    //             'codColor' => $item['codColor'] ?? 0,
    //             'compras_feccompra' => $this->parseFecha($item['compras_feccompra']),
    //             'compras_fecmodif' => $this->parseFecha($item['compras_fecmodif']),
    //             'compras_comentrarios' => $item['compras_comentrarios'],
    //             'precLista' => $item['precLista'] ?? 0,
    //             'difPorcentual' => $item['difPorcentual'] ?? 0,
    //             'colorCelda' => $item['colorCelda'] ?? 0,
    //             'codCtrl' => $item['codCtrl'] ?? 0,                
    //             't1_cantidad' => $item['t1_cantidad'] ?? 0
    //         ]);
    //     }
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

    public function UsrAutoriz(){
        return view('panelctrl');
    }

    public function logout(Request $request, Redirector $redirect){

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return $redirect->to('/');
    }


}
