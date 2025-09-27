<div>
    <span class="font-bold text-[1.5rem]">STOCK DEPÓSITOS</span>
    <div class="w-[100rem] overflow-x-auto">
        <div class="grid gap-1 grid-cols-[160px_355px_150px_150px_150px_150px_150px_150px_110px]">

            <div class="grillas-celdas-1">cod_artic</div>
            <div class="grillas-celdas-1">descripcion</div>
            <div class="grillas-celdas-1">cod_deposito</div>
            <div class="grillas-celdas-1">un_med</div>
            <div class="grillas-celdas-1">saldo_ctrl_stock</div>
            <div class="grillas-celdas-1">cant_comp_stock</div>
            <div class="grillas-celdas-1">cant_recib_stock</div>
            <div class="grillas-celdas-1">punto_ped</div>
            <div class="grillas-celdas-1">created_at</div>
            
            @foreach ($lista as $it)
                <div class="grillas-celdas-2 ">{{ $it->cod_artic }}</div>
                <div class="grillas-celdas-2">{{ $it->descripcion }}</div>
                <div class="grillas-celdas-2">{{ $it->cod_deposito }}</div>
                <div class="grillas-celdas-2">{{ $it->un_med }}</div>
                <div class="grillas-celdas-2 justify-end">
                    {{ number_format($it->saldo_ctrl_stock, 2) }}
                </div>
                <div class="grillas-celdas-2 justify-end">
                    {{ number_format($it->cant_comp_stock, 2) }}
                </div>
                <div class="grillas-celdas-2 justify-end">
                    {{ number_format($it->cant_recib_stock, 2) }}
                </div>
                <div class="grillas-celdas-2 justify-end">
                    {{ number_format($it->punto_ped, 2) }}
                </div>

                <div class="grillas-celdas-2 justify-center">
                    {{ $it->created_at ? \Carbon\Carbon::parse($it->created_at)->format('d/m/Y') : '' }}
                </div>                
            @endforeach        
        </div>
        
    </div>

    <div class="mx-[2rem] mt-2 mb-8">
        {{ $lista->links() }}
    </div>

</div>
