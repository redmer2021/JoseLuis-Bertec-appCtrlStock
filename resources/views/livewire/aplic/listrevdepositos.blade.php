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
    <!--                                           1    2    3     4     5     6    7    8    9    10   11    12    13    14    15    16    17    18    19    20-->
    <div class="sticky top-0 grid gap-1 grid-cols-[50px_50px_120px_150px_300px_60px_60px_60px_60px_80px_100px_300px_100px_110px_110px_100px_120px_80px_300px_120px]">
        <div class="grillas-celdas-1-1">EDIT</div>          <!-- 1 -->
        <div class="grillas-celdas-1-1">CTRL</div>          <!-- 2 -->
        <div class="grillas-celdas-1-1">NRO.PED VTA</div>   <!-- 3 -->
        <div class="grillas-celdas-1-1">ARTICULO</div>      <!-- 4 -->
        <div class="grillas-celdas-1-1">DESCRIPCION</div>   <!-- 5 -->
        <div class="grillas-celdas-1-1">ITEM</div>          <!-- 6 -->
        <div class="grillas-celdas-1-1">PEDIDA</div>        <!-- 7 -->
        <div class="grillas-celdas-1-1">PEND</div>          <!-- 8 -->
        <div class="grillas-celdas-1-1">STOCK</div>         <!-- 9 -->
        <div class="grillas-celdas-1-1">INGR</div>          <!-- 10 -->
        <div class="grillas-celdas-1-1">ESTADO</div>        <!-- 11 -->
        <div class="grillas-celdas-1-1">COMENTARIOS</div>   <!-- 12 -->
        <div class="grillas-celdas-1-1">F.MOD</div>         <!-- 13 -->
        <div class="grillas-celdas-1-1">USER</div>          <!-- 14 -->
        <div class="grillas-celdas-1-1">F.PEDIDO</div>      <!-- 15 -->
        <div class="grillas-celdas-1-1">DIAS V.</div>       <!-- 16 -->
        <div class="grillas-celdas-1-1">PL ENT.</div>       <!-- 17 -->
        <div class="grillas-celdas-1-1">VEND</div>          <!-- 18 -->
        <div class="grillas-celdas-1-1">RAZóN SOCIAL</div>  <!-- 19 -->
        <div class="grillas-celdas-1-1">NRO O/COMPRA</div>  <!-- 20 -->
    </div>

    <span class="font-bold md:text-[1.5rem] block mt-[1rem] ml-[1rem]">DEPÓSITOS</span>
   
    <div class="w-full my-[1rem] mr-4 flex justify-between">
        <div class="grid grid-cols-[1fr_1fr_1fr_1fr_1fr] gap-3 items-center ml-[1rem]">
            <!-- Nro. Orden de Compra -->
            <div class="flex bg-gray-300 rounded-md">
                <input wire:model="txtBuscaNroOrdenCompra"
                    type="text"
                    class="p-2 w-full focus:outline-none focus:ring-0"
                    placeholder="Nro. Orden de Compra">
            </div>

            <!-- Nro. Pedido -->
            <div class="flex bg-gray-300 rounded-md">
                <input wire:model="txtBuscaNroVentas"
                    type="text"
                    class="p-2 w-full focus:outline-none focus:ring-0"
                    placeholder="Nro. Pedido">
            </div>

            <!-- Descripción artículo -->
            <div class="flex bg-gray-300 rounded-md">
                <input wire:model="txtBuscaDescArtic"
                    type="text"
                    class="p-2 w-full focus:outline-none focus:ring-0"
                    placeholder="Descripción artículo">
            </div>

            <!-- Cliente -->
            <div class="flex bg-gray-300 rounded-md">
                <input wire:model="txtBuscaRazSocial"
                    type="text"
                    class="p-2 w-full focus:outline-none focus:ring-0"
                    placeholder="Cliente">
            </div>

            <!-- Botón lupa (alineado a la izquierda del bloque STOCK F.IN) -->
            <button type="button" class="p-2 flex justify-start rounded-md">
                <img wire:click="Buscar()" src="{{ asset('imgs/lupa.png') }}" alt="lupa" class="h-7 w-7 cursor-pointer">
            </button>

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
        <!--                              1    2    3     4     5     6    7    8    9    10   11    12    13    14    15    16    17    18    19    20-->
        <div class="grid gap-1 grid-cols-[50px_50px_120px_150px_300px_60px_60px_60px_60px_80px_100px_300px_100px_110px_110px_100px_120px_80px_300px_120px]">
            <!-- 1 -->
            <div class="grillas-celdas-1">EDIT</div>
            <!-- 2 -->
            <div class="grillas-celdas-1">CTRL</div>
            <!-- 3 -->
            <div class="grillas-celdas-1">NRO.PED VTA</div>
            <!-- 4 -->
            <div class="grillas-celdas-1">ARTICULO</div>
            <!-- 5 -->
            <div class="grillas-celdas-1">DESCRIPCION</div>
            <!-- 6 -->
            <div class="grillas-celdas-1">ITEM</div>
            <!-- 7 -->
            <div class="grillas-celdas-1">PEDIDA</div>
            <!-- 8 -->
            <div class="grillas-celdas-1">PEND</div>
            <!-- 9 -->
            <div class="grillas-celdas-1">STOCK</div>
            <!-- 10 -->
            <div wire:click="Reordenar(1)" class="cursor-pointer grillas-celdas-1 !justify-between">
                <span>
                    INGR
                </span>
                @if ($ordenarComo1 == 'sin')
                    <img src="{{ asset('imgs/sin-ordenar.png') }}" alt="Orden" class="h-5 w-5">
                @elseif ($ordenarComo1 == 'asc')
                    <img src="{{ asset('imgs/orden-descendiente.png') }}" alt="Orden" class="h-5 w-5">
                @else
                    <img src="{{ asset('imgs/orden-ascendente.png') }}" alt="Orden" class="h-5 w-5">
                @endif
            </div>
            <!-- 11 -->
            {{-- <div class="grillas-celdas-1">ESTADO</div> --}}
            <div wire:click="Reordenar(4)" class="cursor-pointer grillas-celdas-1 !justify-between">
                <span>
                    ESTADO
                </span>
                @if ($ordenarComo4 == 'sin')
                    <img src="{{ asset('imgs/sin-ordenar.png') }}" alt="Orden" class="h-5 w-5">
                @elseif ($ordenarComo4 == 'asc')
                    <img src="{{ asset('imgs/orden-descendiente.png') }}" alt="Orden" class="h-5 w-5">
                @else
                    <img src="{{ asset('imgs/orden-ascendente.png') }}" alt="Orden" class="h-5 w-5">
                @endif
            </div>
            <!-- 12 -->
            <div class="grillas-celdas-1">COMENTARIOS</div>
            <!-- 13 -->
            <div class="grillas-celdas-1">F.MOD</div>
            <!-- 14 -->
            <div class="grillas-celdas-1">USER</div>
            <!-- 15 -->
            <div class="grillas-celdas-1">F.PEDIDO</div>
            <!-- 16 -->
            <div wire:click="Reordenar(3)" class="cursor-pointer grillas-celdas-1 !justify-between">
                <span>
                    DIAS V.
                </span>
                @if ($ordenarComo3 == 'sin')
                    <img src="{{ asset('imgs/sin-ordenar.png') }}" alt="Orden" class="h-5 w-5">
                @elseif ($ordenarComo3 == 'asc')
                    <img src="{{ asset('imgs/orden-descendiente.png') }}" alt="Orden" class="h-5 w-5">
                @else
                    <img src="{{ asset('imgs/orden-ascendente.png') }}" alt="Orden" class="h-5 w-5">
                @endif
            </div>
            <!-- 17 -->
            <div wire:click="Reordenar(2)" class="grillas-celdas-1 !justify-between cursor-pointer">
                <span>
                    PL ENT.
                </span>
                @if ($ordenarComo2 == 'sin')
                    <img src="{{ asset('imgs/sin-ordenar.png') }}" alt="Orden" class="h-5 w-5">
                @elseif ($ordenarComo2 == 'asc')
                    <img src="{{ asset('imgs/orden-descendiente.png') }}" alt="Orden" class="h-5 w-5">
                @else
                    <img src="{{ asset('imgs/orden-ascendente.png') }}" alt="Orden" class="h-5 w-5">
                @endif
            </div>
            <!-- 18 -->
            <div class="grillas-celdas-1">VEND</div>
            <!-- 19 -->
            <div class="grillas-celdas-1">RAZóN SOCIAL</div>
            <!-- 20 -->
            <div class="grillas-celdas-1">NRO O/COMPRA</div>

            @foreach ($listaRevDepositos as $it)
                <!-- 1 -->
                <div class="grillas-celdas-2 flex justify-center items-center">
                    @if (in_array(auth()->user()->name, ['CYP', 'DEP']))
                        <img wire:click="Editar('{{ $it->nro_pedido }}', '{{ $it->cod_artic }}', '{{ $it->descrip }}')" src="{{ asset('imgs/editar.png') }}" alt="Ventas" class="cursor-pointer hover:scale-105 w-[1rem]"/>
                    @else
                        <img src="{{ asset('imgs/editar.png') }}" alt="Ventas" class="w-[1rem]"/>
                    @endif
                </div>
                <!-- 2 -->
                <div class="grillas-celdas-2" 
                    style="background-color: <?= ($it->codCtrl == 1) ? 'green' : 'gray'; ?>;">
                </div>
                <!-- 3 -->
                <div class="grillas-celdas-2">{{ $it->nro_pedido }}</div>
                <!-- 4 -->
                <div class="grillas-celdas-2">{{ $it->cod_artic }}</div>
                <!-- 5 -->
                <div class="grillas-celdas-2">{{ $it->descrip }}</div>
                <!-- 6 -->
                <div class="grillas-celdas-2 justify-end ">{{ $it->renglon }}</div>
                <!-- 7 -->
                <div class="grillas-celdas-2 justify-end ">{{ number_format($it->cant_pedida, 0) }}</div>
                <!-- 8 -->
                <div class="grillas-celdas-2 justify-end ">{{ number_format($it->pend_desc, 0) }}</div>
                <!-- 9 -->
                <div class="grillas-celdas-2 justify-end ">{{ number_format($it->saldo_ctrl_stock, 0) }}</div>
                <!-- 10 -->
                <div class="grillas-celdas-2 justify-end">{{ number_format($it->t1_cantidad, 0) }}</div>    

                <!-- 11 -->
                @switch($it->codEstado)
                    @case(1)
                        <div class="grillas-celdas-2 justify-center">PAD</div>
                        @break
                    @case(2)
                        <div class="grillas-celdas-2 justify-center !bg-red-400 !text-white">PAC</div>
                        @break
                @endswitch
                
                <!-- 12 -->
                <div class="grillas-celdas-2">{{ $it->comentarios }}</div>
                <!-- 13 -->
                <div class="grillas-celdas-2 justify-center"> {{ $it->fecModifEstado ? \Carbon\Carbon::parse($it->fecModifEstado)->format('d/m/Y') : '' }}</div>
                <!-- 14 -->
                <div class="grillas-celdas-2 justify-center">{{ $it->user }}</div>
                <!-- 15 -->
                <div class="grillas-celdas-2 justify-center">{{ $it->fec_pedido ? \Carbon\Carbon::createFromFormat('Y-m-d', $it->fec_pedido)->format('d/m/Y') : '' }} </div>
                <!-- 16 -->
                <div class="grillas-celdas-2 justify-center">{{ number_format($it->difDiasPlanEntrega, 0) }}</div>                
                <!-- 17 -->
                <div class="grillas-celdas-2 justify-center">{{ $it->plan_entrega ? \Carbon\Carbon::createFromFormat('Y-m-d', $it->plan_entrega)->format('d/m/Y') : '' }} </div>
                <!-- 18 -->
                <div class="grillas-celdas-2 justify-center ">{{ $it->cod_vend }}</div>
                <!-- 19 -->
                <div class="grillas-celdas-2">{{ $it->raz_social }}</div>
                <!-- 20 -->
                <div class="grillas-celdas-2 justify-end">{{ $it->nro_o_compra }}</div>
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
                @error('asignardtos_a')
                    <div class="col-span-3 flex justify-center mb-2">
                        <span class="block text-red-600 mt-1">{{$message}}</span>
                    </div>
                @enderror
            </div>

            <div class="grid grid-cols-[1fr_2fr] mb-[2rem] gap-3">
                <div>
                    <span class="text-xs">Estado:</span>
                    <select wire:model="codEstado" class="bg-gray-300 p-2 rounded-md w-full">
                        <option value="0">Seleccionar...</option>
                        <option value="1">PAD</option>
                        <option value="2">PAC</option>
                    </select>
                </div>

                <div>
                    <span class="text-xs">Notas:</span>
                    <input maxlength="50" wire:model="comentarios" class="bg-gray-300 p-2 rounded-md w-full" type="text">
                </div>
            </div>
            @if(in_array(auth()->user()->name, ['DEP']))
                <div class="flex space-x-2 justify-center items-center bg-gray-300 rounded p-3 mb-6">
                    <label class="cursor-pointer text-sm" for="ok">Control</label>
                    <input id="ok" wire:model="codCtrl" type="checkbox" class="w-4 h-4 text-blue-600">
                </div>
            @endif

            <div class="flex justify-end">
                <button wire:click="CancelarEdic()" class="w-[10rem] mr-2 cursor-pointer bg-red-400 hover:bg-red-600 hover:text-white transition-colors duration-200 font-bold px-5 py-3 rounded-md text-black">Cancelar</button>
                <button wire:click="GrabarDtos()" class="w-[10rem] cursor-pointer bg-blue-400 hover:bg-blue-600 hover:text-white transition-colors duration-200 font-bold px-5 py-3 rounded-md text-black">Grabar</button>
            </div>

        </div>
    </section>


</div>

