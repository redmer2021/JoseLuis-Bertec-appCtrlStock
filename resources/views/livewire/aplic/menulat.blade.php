<div>
    <div class="flex space-x-2">
        
        @if(in_array(auth()->user()->name, ['CYP','SEB']))
            <div wire:click="actualizarContenido('list-rev-compras')" 
                 class="hover:cursor-pointer border-2 border-transparent hover:border-green-400 bg-[#bdbdbd] rounded-md flex items-center justify-center">
                <span class="font-bold py-2 md:px-4 text-xs text-center md:text-lg">REVISIÓN COMPRAS</span>
            </div>
        @endif
    
        @if(in_array(auth()->user()->name, ['CYP','VTAS','SEB']))
            <div wire:click="actualizarContenido('list-rev-ventas')" 
                 class="hover:cursor-pointer border-2 border-transparent hover:border-green-400 bg-[#bdbdbd] rounded-md flex items-center justify-center">
                <span class="font-bold py-2 md:px-4 text-xs text-center md:text-lg">REVISIÓN VENTAS</span>
            </div>
        @endif
    
        @if(in_array(auth()->user()->name, ['CYP','VTAS','DEP','SEB']))
            <div wire:click="actualizarContenido('list-rev-depositos')" 
                 class="hover:cursor-pointer border-2 border-transparent hover:border-green-400 bg-[#bdbdbd] rounded-md flex items-center justify-center">
                <span class="font-bold py-2 md:px-4 text-xs text-center md:text-lg">REVISIÓN DEPÓSITOS</span>
            </div>
        @endif
    
        @if(auth()->user()->name === 'SEB')
            <div wire:click="actualizarContenido('listados')" 
                 class="hover:cursor-pointer border-2 border-transparent hover:border-green-400 bg-[#bdbdbd] rounded-md flex items-center justify-center">
                <span class="font-bold py-2 md:px-4 text-xs text-center md:text-lg">LISTADOS</span>
            </div>
        @endif
    </div>    
</div>