<div>
    <span class="font-bold text-[1.5rem]">STOCK INGRESOS</span>
    <div class="w-[100rem] overflow-x-auto">
        <div class="grid gap-1 grid-cols-[110px_200px_110px_110px_150px_150px_250px_110px_300px_100px_110px_110px_110px_110px_110px_110px_110px_110px]">

            <div class="grillas-celdas-1">fec_emis</div>
            <div class="grillas-celdas-1">usuario</div>
            <div class="grillas-celdas-1">fec_comprob</div>
            <div class="grillas-celdas-1">tip_comprob</div>
            <div class="grillas-celdas-1">nro_comprob</div>
            <div class="grillas-celdas-1">cod_artic</div>
            <div class="grillas-celdas-1">desc_artic</div>
            <div class="grillas-celdas-1">cod_clie_prov</div>
            <div class="grillas-celdas-1">clie_prov</div>
            <div class="grillas-celdas-1">un_med</div>
            <div class="grillas-celdas-1">cantidad</div>
            <div class="grillas-celdas-1">precio</div>
            <div class="grillas-celdas-1">tot_renglon</div>
            <div class="grillas-celdas-1">tip_int_stock</div>
            <div class="grillas-celdas-1">nro_int_stock</div>
            <div class="grillas-celdas-1">tip_interno</div>
            <div class="grillas-celdas-1">nro_interno</div>
            <div class="grillas-celdas-1">created_at</div>
            
            @foreach ($lista as $it)
                <div class="grillas-celdas-2 justify-center">
                    {{ $it->fec_emis 
                        ? \Carbon\Carbon::createFromFormat('d/m/Y H:i:s', $it->fec_emis)->format('d/m/Y') 
                        : '' 
                    }}
                </div>
                <div class="grillas-celdas-2 ">{{ $it->usuario }}</div>
                <div class="grillas-celdas-2 justify-center">
                    {{ $it->fec_comprob 
                        ? \Carbon\Carbon::createFromFormat('d/m/Y H:i:s', $it->fec_comprob)->format('d/m/Y') 
                        : '' 
                    }}
                </div>
                <div class="grillas-celdas-2 justify-center">{{ $it->tip_comprob }}</div>
                <div class="grillas-celdas-2 ">{{ $it->nro_comprob }}</div>
                <div class="grillas-celdas-2 ">{{ $it->cod_artic }}</div>
                <div class="grillas-celdas-2">{{ $it->desc_artic }}</div>
                <div class="grillas-celdas-2">{{ $it->cod_clie_prov }}</div>
                <div class="grillas-celdas-2">{{ $it->clie_prov }}</div>
                <div class="grillas-celdas-2 justify-center">{{ $it->un_med }}</div>
                <div class="grillas-celdas-2 justify-end">
                    {{ number_format($it->cantidad, 2) }}
                </div>
                <div class="grillas-celdas-2 justify-end">
                    {{ number_format($it->precio, 2) }}
                </div>                
                <div class="grillas-celdas-2 justify-end">
                    {{ number_format($it->tot_renglon, 2) }}
                </div>
                <div class="grillas-celdas-2 justify-center">{{ $it->tip_int_stock }}</div>
                <div class="grillas-celdas-2">{{ $it->nro_int_stock }}</div>
                <div class="grillas-celdas-2 justify-center">{{ $it->tip_interno }}</div>
                <div class="grillas-celdas-2">{{ $it->nro_interno }}</div>
                
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
