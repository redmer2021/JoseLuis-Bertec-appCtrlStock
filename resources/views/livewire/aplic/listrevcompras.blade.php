<div>
    <!-- Spinner global -->
    <div wire:loading.flex 
         class="absolute inset-0 z-40 items-center justify-center">
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

    <span class="font-bold text-[1.5rem]">REVISIÓN COMPRAS PENDIENTES</span>
    
    <div class="w-full my-[1rem]">
        <div class="grid grid-cols-3 gap-3">
            <div class="flex bg-gray-300 rounded-md">
                <input wire:model="txtBuscaNroCompras" type="text" class="p-2 w-full focus:outline-none focus:ring-0" placeholder="Nro compras...">
                <button class="p-2 cursor-pointer" wire:click="Buscar1()">
                    <img src="{{ asset('imgs/lupa.png') }}" alt="lupa" class="h-8 w-8">
                </button>
            </div>
            <div class="flex bg-gray-300 rounded-md">
                <input wire:model="txtBuscaNroArticulo" type="text" class="p-2 w-full focus:outline-none focus:ring-0" placeholder="Nro de artículo...">
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

    <div class="">
        <div class="grid gap-1 grid-cols-[50px_125px_125px_250px_100px_100px_100px_100px_100px_100px_100px_100px_100px_300px_250px_100px]">
            <div class="grillas-celdas-1">edit</div>
            <div class="grillas-celdas-1">Nro.PED.COMPRA</div>
            <div class="grillas-celdas-1">COD.ARTICULO</div>
            <div class="grillas-celdas-1">DESCRIPCION</div>
            <div class="grillas-celdas-1">PEDIDA</div>
            <div class="grillas-celdas-1">PENDIENTE</div>
            <div class="grillas-celdas-1">STOCK</div>
            <div class="grillas-celdas-1">COMPROMETIDO</div>
            <div class="grillas-celdas-1">FALTANTE</div>
            <div class="grillas-celdas-1">FECHA ENTREGA</div>
            <div class="grillas-celdas-1">FECHA MODIF</div>
            <div class="grillas-celdas-1">ESTADO</div>
            <div class="grillas-celdas-1">FECHA</div>
            <div class="grillas-celdas-1">COMENTARIOS</div>
            <div class="grillas-celdas-1">PROVEEDOR</div>
            <div class="grillas-celdas-1">F.ENTREGA</div>

            @foreach ($listRevCompras as $it)
                <div class="grillas-celdas-2 flex justify-center items-center">
                    <img wire:click="Editar('{{ $it['nro_compra'] }}', '{{ $it['cod_artic'] }}')" src="{{ asset('imgs/editar.png') }}" alt="Compras pendientes" class="cursor-pointer hover:scale-105 w-[1rem]" />
                </div>
                <div class="grillas-celdas-2">{{ $it['nro_compra'] }}</div>
                <div class="grillas-celdas-2">{{ $it['cod_artic'] }}</div>
                <div class="grillas-celdas-2">{{ $it['descripcion'] }}</div>
                <div class="grillas-celdas-2 justify-end">{{ number_format($it['cant_pedida'], 0)  }}</div>
                <div class="grillas-celdas-2 justify-end">{{ number_format($it['cant_pendiente'], 0)  }}</div>
                <div class="grillas-celdas-2 justify-end">{{ number_format($it['saldo_ctrl_stock'], 0)  }}</div>
                <div class="grillas-celdas-2 justify-end">{{ number_format($it['cant_comp_stock'], 0)  }}</div>
                <div class="grillas-celdas-2 justify-end">{{ number_format($it['pendiente'], 0)  }}</div>
                <div class="grillas-celdas-2 justify-center">{{ $it['fecCompra'] }}</div>
                <div class="grillas-celdas-2">
                    {{ $it['fecModif'] 
                        ? \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $it['fecModif'])->format('d/m/Y') 
                        : '' }}
                </div>
                <div class="grillas-celdas-2">{{ $it['estado'] }}</div>
                <div class="grillas-celdas-2">
                    {{ $it['fecEstado'] 
                        ? \Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $it['fecEstado'])->format('d/m/Y') 
                        : '' }}
                </div>
                <div class="grillas-celdas-2">{{ $it['comentarios'] }}</div>
                <div class="grillas-celdas-2">{{ $it['raz_social'] }}</div>
                <div class="grillas-celdas-2">{{ \Carbon\Carbon::createFromFormat('d/m/Y H:i:s', $it['fec_entrega'])->format('d/m/Y') }}</div>
            @endforeach        
        </div>        
    </div>

    <section 
        class="ventanaModal" 
        x-cloak 
        x-show="verForm = $wire.verForm" 
        x-transition.duration.0ms
        x-effect="document.body.classList.toggle('overflow-hidden', $wire.verForm)"
    >
        <div class="ventanaInterna_1 p-[8rem]">
            <div class="grid grid-cols-12 mb-[2rem] bg-gray-300 rounded-md">
                <div class="pl-2 flex items-center row-span-2 col-span-6">
                    <span>Asignar datos a:</span>
                </div>
                <div class="pt-2 text-xs flex justify-center col-span-3">
                    <span>Comprobante</span>
                </div>
                <div class="pt-2 text-xs flex justify-center col-span-3">
                    <span>Cód-Artículo</span>
                </div>

                <div class="py-2 col-span-3 flex justify-center">
                    <label class="cursor-pointer mr-2" for="op1">{{ $varComprobante }}</label>
                    <input class="cursor-pointer" wire:model="asignardtos_a" value="1" id="op1" type="radio" name="asignardtos_a">
                </div>

                <div class="py-2 col-span-3 flex justify-center">
                    <label class="cursor-pointer mr-2" for="op2">{{ $varCodArticulo }}</label>
                    <input class="cursor-pointer" wire:model="asignardtos_a" value="2" id="op2" type="radio" name="asignardtos_a">
                </div>
                @error('asignardtos_a')
                    <div class="col-span-12 flex justify-center mb-2">
                        <span class="block text-red-600 mt-1">{{$message}}</span>
                    </div>
                @enderror
            </div>
            
            <div class="grid grid-cols-2 mb-[2rem] gap-3">
                <div class="flex justify-center ">
                    <span class="text-xs">Fecha Entrega</span>
                </div>
                <div class="flex justify-center ">
                    <span class="text-xs">Seleccionar estado:</span>
                </div>
                <div>
                    <input wire:model="fecCompra" maxlength="50" class="bg-gray-300 p-2 rounded-md w-full" type="text">
                </div>

                <div>
                    <select wire:model="idEstado" class="w-full p-2 bg-gray-300 rounded-md">
                        <option value="0">Seleccionar estado...</option>
                        <option value="1">PAD</option>
                        <option value="2">PAC</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-[1fr_5fr] mb-[2rem] gap-3">
                <div class="flex items-center">
                    <span class="text-xs">Notas:</span>
                </div>
                <div>
                    <input maxlength="50" wire:model="notas" class="bg-gray-300 p-2 rounded-md w-full" type="text">
                </div>
            </div>
            <div class="flex justify-end">
                <button wire:click="CancelarEdic()" class="w-[10rem] mr-2 cursor-pointer bg-red-400 hover:bg-red-600 hover:text-white transition-colors duration-200 font-bold px-5 py-3 rounded-md text-black">Cancelar</button>
                <button wire:click="GrabarDtos()" class="w-[10rem] cursor-pointer bg-blue-400 hover:bg-blue-600 hover:text-white transition-colors duration-200 font-bold px-5 py-3 rounded-md text-black">Grabar</button>
            </div>
        </div>
    </section>

</div>
