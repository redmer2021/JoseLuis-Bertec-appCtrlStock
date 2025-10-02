<div>
    <!-- Spinner global -->
    <div wire:loading.flex 
         class="fixed top-[15rem] left-0 right-0 z-40 flex justify-center">
        <div class="bg-indigo-400 px-6 py-4 text-green-700 text-lg rounded-lg font-semibold flex items-center space-x-3 animate-pulse">
            <svg class="w-6 h-6 text-gray-600 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10"
                        stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor"
                      d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
            </svg>
            <span>Recuperando datos...</span>
        </div>
    </div>

    <span class="font-bold text-[1.5rem]">REVISIÓN DE VENTAS</span>
    
    <div class="w-full my-[1rem]">
        <div class="grid grid-cols-3 gap-3">
            <div class="flex bg-gray-300 rounded-md">
                <input wire:model="txtBuscaNroVentas" type="text" class="p-2 w-full focus:outline-none focus:ring-0" placeholder="Nro venta...">
                <button class="p-2 cursor-pointer" wire:click="Buscar1()">
                    <img src="{{ asset('imgs/lupa.png') }}" alt="lupa" class="h-8 w-8">
                </button>
            </div>
            <div class="flex bg-gray-300 rounded-md">
                <input wire:model="txtBuscaDescArtic" type="text" class="p-2 w-full focus:outline-none focus:ring-0" placeholder="Descripción artículo...">
                <button class="p-2 cursor-pointer" wire:click="Buscar2()">
                    <img src="{{ asset('imgs/lupa.png') }}" alt="lupa" class="h-8 w-8">
                </button>
            </div>

            <div class="flex bg-gray-300 rounded-md">
                <input wire:model="txtBuscaRazSocial" type="text" class="p-2 w-full focus:outline-none focus:ring-0" placeholder="Razón social...">
                <button class="p-2 cursor-pointer" wire:click="Buscar3()">
                    <img src="{{ asset('imgs/lupa.png') }}" alt="lupa" class="h-8 w-8">
                </button>
            </div>
        </div>
    </div>

    <div>
        <div class="grid gap-1 grid-cols-[50px_50px_120px_150px_300px_100px_110px_110px_110px_110px_110px_110px_300px_110px_110px_110px_110px_110px_110px_110px_300px_110px_110px_110px_300px_150px_110px_110px_110px]">
            <div class="grillas-celdas-1">edit</div>
            <div class="grillas-celdas-1">CTRL</div>
            <div class="grillas-celdas-1">NRO.PEDIDO</div>
            <div class="grillas-celdas-1">COD.ARTICULO</div>
            <div class="grillas-celdas-1">DESCRIPCION</div>
            <div class="grillas-celdas-1">RENGLON</div>
            <div class="grillas-celdas-1">PEDIDA</div>
            <div class="grillas-celdas-1">PEND.</div>
            <div class="grillas-celdas-1">STOCK</div>
            <div class="grillas-celdas-1">COMPROMET</div>
            <div class="grillas-celdas-1">FALTANTE</div>
            <div class="grillas-celdas-1">ESTADO</div>
            <div class="grillas-celdas-1">COMENTARIOS</div>
            <div class="grillas-celdas-1">DEPOSITO</div>
            <div class="grillas-celdas-1">MODIF ESTADO</div>
            <div class="grillas-celdas-1">USER</div>
            <div class="grillas-celdas-1">F. EM OC</div>
            <div class="grillas-celdas-1">F. ENTR OC</div>
            <div class="grillas-celdas-1">F. ENTR MOD</div>
            <div class="grillas-celdas-1">F. MODIFIC</div>
            <div class="grillas-celdas-1">COMENTARIOS</div>
            <div class="grillas-celdas-1">FECHA PEDIDO</div>
            <div class="grillas-celdas-1">PLAN ENTREGA</div>
            <div class="grillas-celdas-1">COD VEN</div>
            <div class="grillas-celdas-1">RAZóN SOCIAL</div>
            <div class="grillas-celdas-1">NRO O/COMPRA</div>
            <div class="grillas-celdas-1">IMPORTE</div>
            <div class="grillas-celdas-1">PREC-LSTA</div>
            <div class="grillas-celdas-1">PJE</div>

            @foreach ($listaRevVentas as $it)
                <div class="grillas-celdas-2 flex justify-center items-center">
                    <img wire:click="Editar()" src="{{ asset('imgs/editar.png') }}" alt="Ventas" class="cursor-pointer hover:scale-105 w-[1rem]"/>
                </div>
                <div class="grillas-celdas-2">CTRL</div>
                <div class="grillas-celdas-2">{{ $it['nro_pedido'] }}</div>
                <div class="grillas-celdas-2">{{ $it['cod_artic'] }}</div>
                <div class="grillas-celdas-2">{{ $it['descrip'] }}</div>
                <div class="grillas-celdas-2 justify-end ">{{ $it['renglon'] }}</div>
                <div class="grillas-celdas-2 justify-end ">{{ number_format($it['cant_pedida'], 0) }}</div>
                <div class="grillas-celdas-2 justify-end ">{{ number_format($it['pend_desc'], 0) }}</div>
                <div class="grillas-celdas-2 justify-end ">{{ number_format($it['saldo_ctrl_stock'], 0) }}</div>
                <div class="grillas-celdas-2 justify-end ">{{ number_format($it['cant_comp_stock'], 0) }}</div>
                <div class="grillas-celdas-2 justify-end ">{{ number_format($it['faltante'], 0) }}</div>
                <div class="grillas-celdas-2">ESTADO</div>
                <div class="grillas-celdas-2">COMENTARIOS</div>
                <div class="grillas-celdas-2">DEPOSITO</div>
                <div class="grillas-celdas-2">MODIF ESTADO</div>
                <div class="grillas-celdas-2">USER</div>
                
                <div class="grillas-celdas-2 justify-center">{{ $it['femoc'] ? \Carbon\Carbon::createFromFormat('d/m/Y H:i:s', $it['femoc'])->format('d/m/Y') : '' }} </div>

                <div class="grillas-celdas-2">F. ENTR OC</div>
                <div class="grillas-celdas-2">F. ENTR MOD</div>
                <div class="grillas-celdas-2">F. MODIFIC</div>
                <div class="grillas-celdas-2">COMENTARIOS</div>
                <div class="grillas-celdas-2 justify-center">{{ $it['fec_pedido'] ? \Carbon\Carbon::createFromFormat('d/m/Y H:i:s', $it['fec_pedido'])->format('d/m/Y') : '' }} </div>
                <div class="grillas-celdas-2 justify-center">{{ $it['plan_entrega'] ? \Carbon\Carbon::createFromFormat('d/m/Y H:i:s', $it['plan_entrega'])->format('d/m/Y') : '' }} </div>
                <div class="grillas-celdas-2 justify-center ">{{ $it['cod_vend'] }}</div>
                <div class="grillas-celdas-2">{{ $it['raz_social'] }}</div>
                <div class="grillas-celdas-2">{{ $it['nro_o_compra'] }}</div>                
                <div class="grillas-celdas-2 justify-end">{{ number_format($it['impoDolariz'], 2) }}</div>
                <div class="grillas-celdas-2">PREC-LSTA</div>
                <div class="grillas-celdas-2">PJE</div>    
            @endforeach        

        </div>
    </div>

</div>

