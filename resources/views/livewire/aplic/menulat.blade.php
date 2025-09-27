<div>
    {{-- <div wire:click="actualizarContenido('compras')" class="hover:cursor-pointer border-2 border-transparent hover:border-green-400 mb-4 flex flex-col bg-[#bdbdbd] w-[15rem] h-[10rem] rounded-md p-4 items-center justify-between">
        <h5 class="font-bold">COMPRAS PENDIENTES</h5>
        <img src="{{ asset('imgs/compras-pendientes.png') }}" alt="Compras pendientes" class="w-20 h-20 object-contain" />        
    </div>

    <div wire:click="actualizarContenido('entregas')" class="hover:cursor-pointer border-2 border-transparent hover:border-green-400 mb-4 flex flex-col bg-[#bdbdbd] w-[15rem] h-[10rem] rounded-md p-4 items-center justify-between">
        <h5 class="font-bold">ENTREGAS PENDIENTES</h5>
        <img src="{{ asset('imgs/entregas-pendientes.png') }}" alt="Compras pendientes" class="w-20 h-20 object-contain" />
    </div>

    <div wire:click="actualizarContenido('stock-depositos')" class="hover:cursor-pointer border-2 border-transparent hover:border-green-400 mb-4 flex flex-col bg-[#bdbdbd] w-[15rem] h-[10rem] rounded-md p-4 items-center justify-between">
        <h5 class="font-bold">STOCK DEPÓSITOS</h5>
        <img src="{{ asset('imgs/stock-dispo.png') }}" alt="Compras pendientes" class="w-20 h-20 object-contain" />        
    </div>

    <div wire:click="actualizarContenido('stock-ingresos')" class="hover:cursor-pointer border-2 border-transparent hover:border-green-400 mb-4 flex flex-col bg-[#bdbdbd] w-[15rem] h-[10rem] rounded-md p-4 items-center justify-between">
        <h5 class="font-bold">STOCK INGRESOS</h5>
        <img src="{{ asset('imgs/stock-movim.png') }}" alt="Compras pendientes" class="w-20 h-20 object-contain" />
    </div> --}}

    <div wire:click="actualizarContenido('list-rev-compras')" class="hover:cursor-pointer border-2 border-transparent hover:border-green-400 bg-[#bdbdbd] w-[12rem] rounded-md flex items-center justify-center">
        <span class="font-bold py-2">REVISIÓN COMPRAS </span>
        {{-- <img src="{{ asset('imgs/compras-pendientes.png') }}" alt="Revisión Compras pendientes" class="w-20 h-20 object-contain" /> --}}
    </div>

</div>