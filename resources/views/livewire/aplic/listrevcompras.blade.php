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

    <span class="font-bold md:text-[1.5rem]">REVISIÓN COMPRAS</span>
    
    <div class="w-full my-[1rem]">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div class="flex bg-gray-300 rounded-md">
                <input wire:model="txtBuscaNroCompras" type="text" class="p-2 w-full focus:outline-none focus:ring-0" placeholder="Nro. Ped. Compra">
                <button class="p-2 cursor-pointer" wire:click="Buscar1()">
                    <img src="{{ asset('imgs/lupa.png') }}" alt="lupa" class="h-8 w-8">
                </button>
            </div>
            <div class="flex bg-gray-300 rounded-md">
                <input wire:model="txtBuscaDescArtic" type="text" class="p-2 w-full focus:outline-none focus:ring-0" placeholder="Descripción artículo">
                <button class="p-2 cursor-pointer" wire:click="Buscar2()">
                    <img src="{{ asset('imgs/lupa.png') }}" alt="lupa" class="h-8 w-8">
                </button>
            </div>

            <div class="flex bg-gray-300 rounded-md">
                <input wire:model="txtBuscaRazSocial" type="text" class="p-2 w-full focus:outline-none focus:ring-0" placeholder="Proveedor">
                <button class="p-2 cursor-pointer" wire:click="Buscar3()">
                    <img src="{{ asset('imgs/lupa.png') }}" alt="lupa" class="h-8 w-8">
                </button>
            </div>
        </div>
    </div>

    <div>
        <div class="grid gap-1 grid-cols-[50px_130px_135px_300px_60px_60px_60px_60px_60px_100px_100px_140px_100px_300px_250px]">
            <div class="grillas-celdas-1">edit</div>
            <div class="grillas-celdas-1">Nro.PED.COMPRA</div>
            <div class="grillas-celdas-1">ARTICULO</div>
            <div class="grillas-celdas-1">DESCRIPCION</div>
            <div class="grillas-celdas-1">PEDIDA</div>
            <div class="grillas-celdas-1">PEND</div>
            <div class="grillas-celdas-1">STOCK</div>
            <div class="grillas-celdas-1">COMPR.</div>
            <div class="grillas-celdas-1">FALT.</div>
            <div class="grillas-celdas-1">FE EM OC</div>
            <div class="grillas-celdas-1">F.ENTR OC</div>
            <div class="grillas-celdas-1 !justify-between">
                <span wire:click="Reordenar()" class="cursor-pointer">
                    F. ENTR MOD
                </span>
                @if ($ordenarComo == 'desc')
                    <img wire:click="Reordenar()" src="{{ asset('imgs/orden-descendiente.png') }}" alt="Orden" class="h-5 w-5 cursor-pointer">
                @else
                    <img wire:click="Reordenar()" src="{{ asset('imgs/orden-ascendente.png') }}" alt="Orden" class="h-5 w-5 cursor-pointer">
                @endif
            </div>
            <div class="grillas-celdas-1">F.MOD</div>
            <div class="grillas-celdas-1">COMENTARIOS</div>
            <div class="grillas-celdas-1">PROVEEDOR</div>
        
            @foreach ($listRevCompras as $it)
                <div class="grillas-celdas-2 flex justify-center items-center">
                    @if (in_array(auth()->user()->name, ['CYP']))
                        <img wire:click="Editar('{{ $it['nro_compra'] }}', '{{ $it['cod_artic'] }}')" src="{{ asset('imgs/editar.png') }}" alt="Compras pendientes" class="cursor-pointer hover:scale-105 w-[1rem]" />
                    @else
                        <img src="{{ asset('imgs/editar.png') }}" alt="Compras pendientes" class="w-[1rem]" />
                    @endif
                </div>
                <div class="grillas-celdas-2">{{ $it['nro_compra'] }}</div>
                <div class="grillas-celdas-2">{{ $it['cod_artic'] }}</div>
                <div class="grillas-celdas-2">{{ $it['descripcion'] }}</div>
                <div class="grillas-celdas-2 justify-end">{{ number_format($it['cant_pedida'], 0)  }}</div>
                <div class="grillas-celdas-2 justify-end">{{ number_format($it['cant_pendiente'], 0)  }}</div>
                <div class="grillas-celdas-2 justify-end">{{ number_format($it['saldo_ctrl_stock'], 0)  }}</div>
                <div class="grillas-celdas-2 justify-end">{{ number_format($it['cant_comp_stock'], 0)  }}</div>
                <div class="grillas-celdas-2 justify-end">{{ number_format($it['faltante'], 0)  }}</div>
                <div class="grillas-celdas-2 justify-center">{{ \Carbon\Carbon::createFromFormat('d/m/Y H:i:s', $it['fec_emision'])->format('d/m/Y') }}</div>
                <div class="grillas-celdas-2 justify-center">{{ \Carbon\Carbon::createFromFormat('d/m/Y H:i:s', $it['fec_entrega'])->format('d/m/Y') }}</div>

                <div class="grillas-celdas-2 flex justify-between !px-6">
                    {{ $it['fecCompra1'] 
                        ? \Carbon\Carbon::parse($it['fecCompra1'])->format('d/m/Y') 
                        : '' }}
                        @if ($it['entregaParc'] == 1)
                            <img src="{{ asset('imgs/entregas-multiple.png') }}" alt="Multiple Entrega" class="w-[1rem]" />
                        @endif
                </div>

                <div class="grillas-celdas-2 justify-center">
                    {{ $it['fecModif'] 
                        ? \Carbon\Carbon::parse($it['fecModif'])->format('d/m/Y') 
                        : '' }}                                        
                </div>

                <div class="grillas-celdas-2">{{ $it['comentarios1'] }}</div>
                <div class="grillas-celdas-2">{{ $it['raz_social'] }}</div>
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
            <div class="grid grid-cols-12 mb-[2rem] bg-gray-300 rounded-md pr-3">

                <div class="pl-2 flex items-center row-span-12 md:row-span-2 col-span-4">
                    <span>Asignar datos a:</span>
                </div>
                <div class="pt-2 text-xs flex justify-end row-span-12 md:col-span-4">
                    <span>Comprobante</span>
                </div>
                <div class="pt-2 text-xs flex justify-end row-span-12 md:col-span-4">
                    <span>Cód-Artículo</span>
                </div>

                <div class="py-2 row-span-12 md:col-span-4 flex justify-end">
                    <label class="cursor-pointer mr-2" for="op1">{{ $varComprobante }}</label>
                    <input class="cursor-pointer" wire:model="asignardtos_a" value="1" id="op1" type="radio" name="asignardtos_a">
                </div>

                <div class="py-2 row-span-12 md:col-span-4 flex justify-end">
                    <label class="cursor-pointer mr-2" for="op2">{{ $varCodArticulo }}</label>
                    <input class="cursor-pointer" wire:model="asignardtos_a" value="2" id="op2" type="radio" name="asignardtos_a">
                </div>
                @error('asignardtos_a')
                    <div class="col-span-12 flex justify-center mb-2">
                        <span class="block text-red-600 mt-1">{{$message}}</span>
                    </div>
                @enderror
            </div>
            
            <div class="grid md:grid-cols-[25%_auto] mb-[2rem] gap-3">
                <div>
                    <span class="text-xs">Fecha Entrega</span>
                    <input wire:model="fecCompra1" maxlength="50" class="bg-gray-300 p-2 rounded-md w-full" type="date">
                </div>
                <div>
                    <span class="text-xs">Notas:</span>
                    <input maxlength="50" wire:model="comentarios1" class="bg-gray-300 p-2 rounded-md w-full" type="text">
                </div>
                <div>
                    <span class="text-xs">Fecha Entrega 2</span>
                    <input wire:model="fecCompra2" maxlength="50" class="bg-gray-300 p-2 rounded-md w-full" type="date">
                </div>
                <div>
                    <span class="text-xs">Notas:</span>
                    <input maxlength="50" wire:model="comentarios2" class="bg-gray-300 p-2 rounded-md w-full" type="text">
                </div>
            </div>

            <div class="flex space-x-2 justify-center items-center bg-gray-300 rounded p-3 mb-3 w-auto">
                <label class="cursor-pointer text-sm" for="entregParc">Entregas Parciales</label>
                <input id="entregParc" wire:model="entregaParc" type="checkbox" class="w-4 h-4 text-blue-600">
            </div>

            <div class="flex justify-center md:justify-end">
                <button wire:click="CancelarEdic()" class="w-[10rem] mr-2 cursor-pointer bg-red-400 hover:bg-red-600 hover:text-white transition-colors duration-200 font-bold px-5 py-3 rounded-md text-black">Cancelar</button>
                <button wire:click="GrabarDtos()" class="w-[10rem] cursor-pointer bg-blue-400 hover:bg-blue-600 hover:text-white transition-colors duration-200 font-bold px-5 py-3 rounded-md text-black">Grabar</button>
            </div>
        </div>
    </section>

</div>
