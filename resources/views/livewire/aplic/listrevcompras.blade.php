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
    <!--                                           1    2     3     4     5    6    7    8    9    10    11    12    13    14    15    16   17           -->
    <div class="sticky top-0 grid gap-1 grid-cols-[50px_130px_135px_300px_60px_60px_60px_60px_80px_100px_100px_140px_60px_100px_300px_100px_250px]">
        <div class="grillas-celdas-1-1">edit</div>              <!--1-->
        <div class="grillas-celdas-1-1">Nro.PED.COMPRA</div>    <!--2-->
        <div class="grillas-celdas-1-1">ARTICULO</div>          <!--3-->
        <div class="grillas-celdas-1-1">DESCRIPCION</div>       <!--4-->
        <div class="grillas-celdas-1-1">PEDIDA</div>            <!--5-->
        <div class="grillas-celdas-1-1">PEND</div>              <!--6-->
        <div class="grillas-celdas-1-1">STOCK</div>             <!--7-->
        <div class="grillas-celdas-1-1">COMPR.</div>            <!--8-->
        <div class="grillas-celdas-1-1">FALT.</div>             <!--9-->
        <div class="grillas-celdas-1-1">FE EM OC</div>          <!--10-->
        <div class="grillas-celdas-1-1">F.ENTR OC</div>         <!--11-->
        <div class="grillas-celdas-1-1">F.ENTR MOD</div>        <!--12-->
        <div class="grillas-celdas-1-1">EN 1</div>              <!--13-->
        <div class="grillas-celdas-1-1">F.MOD</div>             <!--14-->
        <div class="grillas-celdas-1-1">COMENTARIOS</div>       <!--15-->
        <div class="grillas-celdas-1-1">USER</div>              <!--16-->
        <div class="grillas-celdas-1-1">PROVEEDOR</div>         <!--17-->
    </div>        

    <span class="font-bold md:text-[1.5rem] block mt-[1rem] ml-[1rem]">COMPRAS</span>
    

    <div class="w-full my-[1rem] flex justify-between">
        <div class="grid grid-cols-1 md:grid-cols-[1fr_1fr_2fr_1fr] gap-3 w-[50%] ml-[1rem]">
            <div class="flex bg-gray-300 rounded-md">
                <input wire:model="txtBuscaNroCompras" type="text" class="p-2 w-full focus:outline-none focus:ring-0" placeholder="Nro. Ped. Compra">
            </div>
            <div class="flex bg-gray-300 rounded-md">
                <input wire:model="txtBuscaDescArtic" type="text" class="p-2 w-full focus:outline-none focus:ring-0" placeholder="Descripción artículo">
            </div>

            <div class="flex bg-gray-300 rounded-md">
                <input wire:model="txtBuscaRazSocial" type="text" class="p-2 w-full focus:outline-none focus:ring-0" placeholder="Proveedor">
            </div>
            <div>
                <button class="p-2" >
                    <img wire:click="Buscar()" src="{{ asset('imgs/lupa.png') }}" alt="lupa" class="h-8 w-8 cursor-pointer">
                </button>
            </div>
        </div>

        <button 
        class="font-bold 
                py-1 
                md:px-4 
                text-xs 
                text-center 
                md:text-lg 
                hover:cursor-pointer 
                border-2 
                border-transparent 
                hover:border-[#162556]
                bg-gray-300
                rounded-md 
                flex 
                items-center 
                mr-[1rem]
                justify-center"
        wire:click="ExportExcel()">
        Exportar a Excel
    </button>

    </div>

    <div>
        <!--                              1    2     3     4     5    6    7    8    9    10    11    12    13    14    15    16   17-->
        <div class="grid gap-1 grid-cols-[50px_130px_135px_300px_60px_60px_60px_60px_80px_100px_100px_140px_60px_100px_300px_100px_250px]">
            <!-- 1 -->
            <div class="grillas-celdas-1">edit</div>
            <!-- 2 -->
            <div class="grillas-celdas-1">Nro.PED.COMPRA</div>
            <!-- 3 -->
            <div class="grillas-celdas-1">ARTICULO</div>
            <!-- 4 -->
            <div class="grillas-celdas-1">DESCRIPCION</div>
            <!-- 5 -->
            <div class="grillas-celdas-1">PEDIDA</div>
            <!-- 6 -->
            <div class="grillas-celdas-1">PEND</div>
            <!-- 7 -->
            <div class="grillas-celdas-1">STOCK</div>
            <!-- 8 -->
            <div class="grillas-celdas-1">COMPR.</div>
            <!-- 9 -->
            <div wire:click="Reordenar(1)" class="cursor-pointer grillas-celdas-1 !justify-between">
                <span>
                    FALT.
                </span>
                @if ($ordenarComo1 == 'sin')
                    <img src="{{ asset('imgs/sin-ordenar.png') }}" alt="Orden" class="h-5 w-5">
                @elseif ($ordenarComo1 == 'asc')
                    <img src="{{ asset('imgs/orden-descendiente.png') }}" alt="Orden" class="h-5 w-5">
                @else
                    <img src="{{ asset('imgs/orden-ascendente.png') }}" alt="Orden" class="h-5 w-5">
                @endif
            </div>
            <!-- 10 -->
            <div class="grillas-celdas-1">FE EM OC</div>
            <!-- 11 -->
            <div class="grillas-celdas-1">F.ENTR OC</div>
            <!-- 12 -->
            <div wire:click="Reordenar(2)" class="grillas-celdas-1 !justify-between cursor-pointer">
                <span>
                    F.ENTR MOD
                </span>
                @if ($ordenarComo2 == 'sin')
                    <img src="{{ asset('imgs/sin-ordenar.png') }}" alt="Orden" class="h-5 w-5">
                @elseif ($ordenarComo2 == 'asc')
                    <img src="{{ asset('imgs/orden-descendiente.png') }}" alt="Orden" class="h-5 w-5">
                @else
                    <img src="{{ asset('imgs/orden-ascendente.png') }}" alt="Orden" class="h-5 w-5">
                @endif
            </div>
            <!-- 13 -->
            <div class="grillas-celdas-1">EN 1</div>
            <!-- 14 -->
            <div class="grillas-celdas-1">F.MOD</div>
            <!-- 15 -->
            <div class="grillas-celdas-1">COMENTARIOS</div>
            <!-- 16 -->
            <div class="grillas-celdas-1">USER</div>
            <!-- 17 -->
            <div class="grillas-celdas-1">PROVEEDOR</div>
        
            @foreach ($listRevCompras as $it)
                <!-- 1 -->
                @if (in_array(auth()->user()->name, ['CYP', 'VTAS']))
                    <div wire:click="Editar('{{ $it->nro_compra }}', '{{ $it->cod_artic }}', '{{ $it->cant_pendiente }}', '{{ $it->descrip }}', {{ $it->cant_pedida }}, {{ $it->cant_pendiente }}, '{{ \Carbon\Carbon::createFromFormat('Y-m-d', $it->fec_entrega)->format('d/m/Y') }}' )" class="cursor-pointer grillas-celdas-2 flex justify-center items-center">
                        <img src="{{ asset('imgs/editar.png') }}" alt="Compras pendientes" class="w-[1rem]" />
                    </div>
                @else
                    <div class="grillas-celdas-2 flex justify-center items-center">
                        <img src="{{ asset('imgs/editar.png') }}" alt="Compras pendientes" class="w-[1rem]" />
                    </div>
                @endif
    
                <!-- 2 -->
                <div class="grillas-celdas-2">{{ $it->nro_compra }}</div>
                <!-- 3 -->
                <div class="grillas-celdas-2">{{ $it->cod_artic }}</div>
                <!-- 4 -->
                <div class="grillas-celdas-2">{{ $it->descrip }}</div>
                <!-- 5 -->
                <div class="grillas-celdas-2 justify-end">{{ number_format($it->cant_pedida, 0)  }}</div>
                <!-- 6 -->
                <div class="grillas-celdas-2 justify-end">{{ number_format($it->cant_pendiente, 0)  }}</div>
                <!-- 7 -->
                <div class="grillas-celdas-2 justify-end">{{ number_format($it->saldo_ctrl_stock, 0)  }}</div>
                <!-- 8 -->
                <div class="grillas-celdas-2 justify-end">{{ number_format($it->cant_comp_stock, 0)  }}</div>
                <!-- 9 -->
                <div class="grillas-celdas-2 justify-end">{{ number_format($it->faltante, 0)  }}</div>
                <!-- 10 -->
                <div class="grillas-celdas-2 justify-center">{{ \Carbon\Carbon::createFromFormat('Y-m-d', $it->fec_emision)->format('d/m/Y') }}</div>
                <!-- 11 -->
                <div class="grillas-celdas-2 justify-center">{{ \Carbon\Carbon::createFromFormat('Y-m-d', $it->fec_entrega)->format('d/m/Y') }}</div>
                <!-- 12 -->
                <div class="grillas-celdas-2 flex justify-between !px-6">
                    {{ $it->fecCompra1 
                        ? \Carbon\Carbon::parse($it->fecCompra1)->format('d/m/Y') 
                        : '' }}
                        @if ($it->entregaParc == 1)
                            <img src="{{ asset('imgs/entregas-multiple.png') }}" alt="Multiple Entrega" class="w-[1rem]" />
                        @endif
                </div>
                <!-- 13 -->
                <div class="grillas-celdas-2 justify-center">
                    {{ $it->unidades1 }}
                </div>

                <!-- 14 -->
                <div class="grillas-celdas-2 justify-center">
                    {{ $it->fecModif
                        ? \Carbon\Carbon::parse($it->fecModif)->format('d/m/Y') 
                        : '' }}                                        
                </div>

                <!-- 15 -->
                <div class="grillas-celdas-2">{{ $it->comentarios1 }}</div>
                <!-- 16 -->
                <div class="grillas-celdas-2">{{ $it->user }}</div>
                <!-- 17 -->
                <div class="grillas-celdas-2">{{ $it->raz_social }}</div>
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
            <div class="grid grid-cols-[1fr_1fr_2fr] mb-[2rem] bg-gray-300 rounded-md pr-3">

                <div class="pl-2 row-span-2 flex items-center">
                    <span>Asignar datos a:</span>
                </div>
                <div class="pt-2 text-xs flex justify-end">
                    <span>Comprobante</span>
                </div>
                <div class="pt-2 text-xs flex justify-end">
                    <span>Cód-Artículo</span>
                </div>

                <div class="py-2 flex justify-end">
                    <label class="cursor-pointer mr-2" for="op1">{{ $varComprobante }}</label>
                    <input class="cursor-pointer" wire:model="asignardtos_a" value="1" id="op1" type="radio" name="asignardtos_a">
                </div>

                <div class="py-2 flex justify-end">
                    <label class="cursor-pointer mr-2" for="op2">{{ $varDescArticulo }}</label>
                    <input class="cursor-pointer" wire:model="asignardtos_a" value="2" id="op2" type="radio" name="asignardtos_a">
                </div>
                <div class="col-span-3 flex justify-between px-2 py-3">
                    <span>CANT. PED: {{ $cantPedida }}</span>
                    <span>CANT. PEND: {{ $cantPendiente }}</span>
                    <span>FEC. ENTR. OC: {{ $f_entrega_oc }}</span>
                </div>
                
                @error('asignardtos_a')
                    <div class="col-span-3 flex justify-center mb-2">
                        <span class="block text-red-600 mt-1">{{$message}}</span>
                    </div>
                @enderror
            </div>
            
            <div class="grid md:grid-cols-[25%_12%_auto] mb-[2rem] gap-3">
                <div>
                    <span class="text-xs">Fecha Entrega 1</span>
                    <input wire:model="fecCompra1" maxlength="50" class="bg-gray-300 p-2 rounded-md w-full" type="date">
                </div>
                <div>
                    <span class="text-xs">Unidades 1</span>
                    <input :disabled="$wire.asignardtos_a != 2" wire:model="unidades1" maxlength="5" class="bg-gray-300 p-2 rounded-md w-full text-right" type="text">
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
                    <span class="text-xs">Unidades 2</span>
                    <input :disabled="$wire.asignardtos_a != 2" wire:model="unidades2" maxlength="5" class="bg-gray-300 p-2 rounded-md w-full text-right" type="text">
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

            @error('unidades')
                <div class="col-span-3 flex justify-center mb-2">
                    <span class="block text-red-600 mt-1">{{$message}}</span>
                </div>
            @enderror

            <div class="flex justify-center md:justify-end">
                <button wire:click="CancelarEdic()" class="w-[10rem] mr-2 cursor-pointer bg-red-400 hover:bg-red-600 hover:text-white transition-colors duration-200 font-bold px-5 py-3 rounded-md text-black">Cancelar</button>
                <button wire:click="GrabarDtos()" class="w-[10rem] cursor-pointer bg-blue-400 hover:bg-blue-600 hover:text-white transition-colors duration-200 font-bold px-5 py-3 rounded-md text-black">Grabar</button>
            </div>
        </div>
    </section>

</div>

