<div class="relative min-h-[10rem]"> {{-- le damos altura mínima para que el spinner se vea centrado --}}
    
    <!-- Spinner global -->
    <div wire:loading.flex 
         class="absolute inset-0 z-40 items-center justify-center">
        <div class="px-6 py-4 text-green-700 text-lg font-semibold flex items-center space-x-3 animate-pulse">
            <svg class="w-6 h-6 text-gray-600 animate-spin" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10"
                        stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor"
                      d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
            </svg>
            <span>Recuperando datos...</span>
        </div>
    </div>

    <!-- Contenido dinámico -->
    <div wire:loading.remove>
        @if($seleccion === 'compras')
            @livewire('aplic.listcompras')
        @elseif($seleccion === 'entregas')
            @livewire('aplic.listentregas')
        @elseif($seleccion === 'stock-depositos')
            @livewire('aplic.listdepositos')
        @elseif($seleccion === 'stock-ingresos')
            @livewire('aplic.listingresos')
        @elseif($seleccion === 'list-rev-compras')
            @livewire('aplic.listrevcompras')
        @else
            <h5>Seleccione opción...</h5>
        @endif
    </div>

</div>

