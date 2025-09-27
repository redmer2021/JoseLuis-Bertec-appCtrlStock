@extends('layouts.pltgeneral')

@section('contenidosPrincipales')
<div class="flex flex-col h-screen">
    <!-- Menu superior -->
    @livewire('aplic.menu1')

    <!-- Menu lateral (apilado debajo) -->
    <div class="bg-amber-700 pl-2 py-2">
        @livewire('aplic.menulat')
    </div>

    <!-- Contenido principal que ocupa lo que sobra -->
    <div class="p-4 bg-blue-300 flex-1 overflow-auto">
        @livewire('aplic.contenidos')
    </div>
</div>
@endsection
