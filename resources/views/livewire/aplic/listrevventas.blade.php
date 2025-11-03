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

    <span class="font-bold md:text-[1.5rem]">REVISIÓN DE VENTAS</span>

    <div class="w-full my-[1rem]">
        <div class="grid grid-cols-1 md:grid-cols-[1fr_1fr_2fr_2fr_1fr] gap-3 w-[70%]">
            <div class="flex bg-gray-300 rounded-md">
                <input wire:model="txtBuscaOrdenComp" type="text" class="p-2 w-full focus:outline-none focus:ring-0" placeholder="Nro. Orden de compra">
            </div>
            <div class="flex bg-gray-300 rounded-md">
                <input wire:model="txtBuscaNroVentas" type="text" class="p-2 w-full focus:outline-none focus:ring-0" placeholder="Nro. Pedido">
            </div>
            <div class="flex bg-gray-300 rounded-md">
                <input wire:model="txtBuscaDescArtic" type="text" class="p-2 w-full focus:outline-none focus:ring-0" placeholder="Descripción artículo">
            </div>

            <div class="flex bg-gray-300 rounded-md">
                <input wire:model="txtBuscaRazSocial" type="text" class="p-2 w-full focus:outline-none focus:ring-0" placeholder="Cliente">
            </div>

            <button class="p-2 cursor-pointer" wire:click="Buscar()">
                <img src="{{ asset('imgs/lupa.png') }}" alt="lupa" class="h-8 w-8">
            </button>

        </div>
    </div>

    <div>
        <div class="grid gap-1 grid-cols-[50px_80px_120px_150px_250px_60px_60px_60px_60px_60px_60px_80px_80px_110px_110px_80px_110px_110px_110px_250px_110px_110px_250px_110px_110px_100px_80px_250px_130px]">
            <div class="grillas-celdas-1">edit</div>
            <div wire:click="Reordenar3()" class="cursor-pointer grillas-celdas-1 !justify-between">
                <span  class="">
                    APROB
                </span>
                @if ($ordenarComo3 == 'desc')
                    <img src="{{ asset('imgs/orden-ascendente.png') }}" alt="Orden" class="h-5 w-5">
                @else
                    <img src="{{ asset('imgs/orden-descendiente.png') }}" alt="Orden" class="h-5 w-5">
                @endif
            </div>
            <div class="grillas-celdas-1">NRO.PEDIDO</div>
            <div class="grillas-celdas-1">ARTICULO</div>
            <div class="grillas-celdas-1">DESCRIPCION</div>
            <div class="grillas-celdas-1">ITEM</div>
            <div class="grillas-celdas-1">PEDIDA</div>
            <div class="grillas-celdas-1">PEND</div>
            <div class="grillas-celdas-1">STOCK</div>
            <div class="grillas-celdas-1">COMPR.</div>
            <div class="grillas-celdas-1">A RECIB</div>
            <div wire:click="Reordenar4()" class="cursor-pointer grillas-celdas-1 !justify-between">
                <span  class="">
                    FALT.
                </span>
                @if ($ordenarComo4 == 'desc')
                    <img src="{{ asset('imgs/orden-ascendente.png') }}" alt="Orden" class="h-5 w-5">
                @else
                    <img src="{{ asset('imgs/orden-descendiente.png') }}" alt="Orden" class="h-5 w-5">
                @endif
            </div>
            <div class="grillas-celdas-1">INGR.</div>
            <div class="grillas-celdas-1">PRECIO</div>
            <div class="grillas-celdas-1">PREC-LSTA</div>
            <div class="grillas-celdas-1">DIF-%</div>

            <div wire:click="Reordenar1()" class="cursor-pointer grillas-celdas-1 !justify-between">
                <span  class="">
                    EST
                </span>
                @if ($ordenarComo1 == 'desc')
                    <img src="{{ asset('imgs/orden-ascendente.png') }}" alt="Orden" class="h-5 w-5">
                @else
                    <img src="{{ asset('imgs/orden-descendiente.png') }}" alt="Orden" class="h-5 w-5">
                @endif
            </div>
            <div class="grillas-celdas-1">F. MOD</div>
            <div class="grillas-celdas-1">USER</div>
            <div class="grillas-celdas-1">COMENTARIOS</div>
            <div class="grillas-celdas-1">F. ENTR MOD</div>
            <div class="grillas-celdas-1">F. MODIFIC</div>
            <div class="grillas-celdas-1">COMENTARIOS COMPRAS</div>
            <div class="grillas-celdas-1">F.PEDIDO</div>

            <div wire:click="Reordenar2()" class="cursor-pointer grillas-celdas-1 !justify-between">
                <span>
                    PL ENT.
                </span>
                @if ($ordenarComo2 == 'desc')
                    <img src="{{ asset('imgs/orden-ascendente.png') }}" alt="Orden" class="h-5 w-5">
                @else
                    <img src="{{ asset('imgs/orden-descendiente.png') }}" alt="Orden" class="h-5 w-5">
                @endif
            </div>

            <div wire:click="Reordenar5()" class="cursor-pointer grillas-celdas-1 !justify-between">
                <span>
                    DIAS V.
                </span>
                @if ($ordenarComo5 == 'desc')
                    <img src="{{ asset('imgs/orden-ascendente.png') }}" alt="Orden" class="h-5 w-5">
                @else
                    <img src="{{ asset('imgs/orden-descendiente.png') }}" alt="Orden" class="h-5 w-5">
                @endif
            </div>

            <div class="grillas-celdas-1">VEND</div>
            <div class="grillas-celdas-1">RAZóN SOCIAL</div>
            <div class="grillas-celdas-1">NRO O/COMPRA</div>

            @foreach ($listaRevVentas as $it)
                <div class="grillas-celdas-2 flex justify-center items-center">
                    @if (in_array(auth()->user()->name, ['CYP', 'VTAS']))
                        <img wire:click="Editar('{{ $it['nro_pedido'] }}', '{{ $it['cod_artic'] }}', '{{ $it['descrip'] }}')" src="{{ asset('imgs/editar.png') }}" alt="Ventas" class="cursor-pointer hover:scale-105 w-[1rem]"/>
                    @else
                        <img src="{{ asset('imgs/editar.png') }}" alt="Ventas" class="w-[1rem]"/>
                    @endif
                </div>
                <div class="grillas-celdas-2" 
                    style="background-color: <?= ($it['codColor'] == 1) ? 'green' : 'gray'; ?>;">                   
                </div>
                <div class="grillas-celdas-2">{{ $it['nro_pedido'] }}</div>
                <div class="grillas-celdas-2">{{ $it['cod_artic'] }}</div>
                <div class="grillas-celdas-2">{{ $it['descrip'] }}</div>
                <div class="grillas-celdas-2 justify-end ">{{ $it['renglon'] }}</div>
                <div class="grillas-celdas-2 justify-end ">{{ number_format($it['cant_pedida'], 0) }}</div>
                <div class="grillas-celdas-2 justify-end ">{{ number_format($it['pend_desc'], 0) }}</div>
                <div class="grillas-celdas-2 justify-end ">{{ number_format($it['saldo_ctrl_stock'], 0) }}</div>
                <div class="grillas-celdas-2 justify-end ">{{ number_format($it['cant_comp_stock'], 0) }}</div>
                <div class="grillas-celdas-2 justify-end ">{{ number_format($it['aRecibir'], 0) }}</div>
                <div class="grillas-celdas-2 justify-end ">{{ number_format($it['faltante'], 0) }}</div>
                <div class="grillas-celdas-2 justify-end ">INGR</div>
                <div class="grillas-celdas-2 justify-end">{{ number_format($it['impoDolariz'], 2) }}</div>
                <div class="grillas-celdas-2 justify-end pr-2">{{ number_format($it['precLista'], 2) }}</div>                
                @switch($it['colorCelda'])
                    @case(1)
                        <div class="grillas-celdas-3-1 justify-center pr-2">{{ number_format($it['difPorcentual'], 2) }}%</div>
                        @break
                    @case(2)
                        <div class="grillas-celdas-3-2 justify-center pr-2">{{ number_format($it['difPorcentual'], 2) }}%</div>                
                        @break            
                    @case(3)
                        <div class="grillas-celdas-3-3 justify-center pr-2">{{ number_format($it['difPorcentual'], 2) }}%</div>
                        @break
                    @default
                        <div class="grillas-celdas-2 justify-center pr-2">{{ number_format($it['difPorcentual'], 2) }}%</div>
                @endswitch
                <div class="grillas-celdas-2 justify-center">{{ $it['codEstado'] }}</div>
                <div class="grillas-celdas-2 justify-center"> {{ $it['fecModifEstado'] ? \Carbon\Carbon::parse($it['fecModifEstado'])->format('d/m/Y') : '' }}</div>
                <div class="grillas-celdas-2 justify-center">{{ $it['user'] }}</div>
                <div class="grillas-celdas-2">{{ $it['comentarios'] }}</div>                                
                <div class="grillas-celdas-2 justify-center">{{ $it['compras_feccompra'] ? \Carbon\Carbon::parse($it['compras_feccompra'])->format('d/m/Y') : ''    }}</div>
                <div class="grillas-celdas-2 justify-center">{{ $it['compras_fecmodif'] ? \Carbon\Carbon::parse($it['compras_fecmodif'])->format('d/m/Y') : ''    }}</div>
                <div class="grillas-celdas-2">{{ $it['compras_comentrarios'] }}</div>
                <div class="grillas-celdas-2 justify-center">{{ $it['fec_pedido'] ? \Carbon\Carbon::createFromFormat('d/m/Y H:i:s', $it['fec_pedido'])->format('d/m/Y') : '' }} </div>
                <div class="grillas-celdas-2 justify-center">{{ $it['plan_entrega'] ? \Carbon\Carbon::createFromFormat('d/m/Y H:i:s', $it['plan_entrega'])->format('d/m/Y') : '' }} </div>
                <div class="grillas-celdas-2 justify-center">{{ $it['difDiasPlanEntrega'] }}</div>
                <div class="grillas-celdas-2 justify-center ">{{ $it['cod_vend'] }}</div>
                <div class="grillas-celdas-2">{{ $it['raz_social'] }}</div>
                <div class="grillas-celdas-2 justify-end">{{ $it['nro_o_compra'] }}</div>
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
                        <option value="3">NAP</option>
                    </select>
                </div>
                <div>
                    <span class="text-xs">Notas:</span>
                    <input maxlength="50" wire:model="comentarios" class="bg-gray-300 p-2 rounded-md w-full" type="text">
                </div>
            </div>
            <div class="flex space-x-2 justify-center items-center bg-gray-300 rounded p-3 mb-6">
                <label class="cursor-pointer text-sm" for="ok">Aprobado</label>
                <input id="ok" wire:model="codColor" type="checkbox" class="w-4 h-4 text-blue-600" :disabled="$wire.asignardtos_a != 1">
            </div>

            <div class="flex justify-end">
                <button wire:click="CancelarEdic()" class="w-[10rem] mr-2 cursor-pointer bg-red-400 hover:bg-red-600 hover:text-white transition-colors duration-200 font-bold px-5 py-3 rounded-md text-black">Cancelar</button>
                <button wire:click="GrabarDtos()" class="w-[10rem] cursor-pointer bg-blue-400 hover:bg-blue-600 hover:text-white transition-colors duration-200 font-bold px-5 py-3 rounded-md text-black">Grabar</button>
            </div>

        </div>
    </section>

</div>

