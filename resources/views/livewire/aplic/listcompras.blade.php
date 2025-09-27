<div>
    <span class="font-bold text-[1.5rem]">COMPRAS PENDIENTES</span>
    <div class="w-[100rem] overflow-x-auto">
        <div class="grid gap-1 grid-cols-[125px_110px_180px_250px_250px_100px_100px_300px_110px_110px_110px_110px_110px_110px_110px_110px_110px]">
            <div class="grillas-celdas-1">nro-compra</div>
            <div class="grillas-celdas-1">fec-emision</div>
            <div class="grillas-celdas-1">cod-artic</div>
            <div class="grillas-celdas-1">descrip</div>
            <div class="grillas-celdas-1">desc-adicional</div>
            <div class="grillas-celdas-1">deposito</div>
            <div class="grillas-celdas-1">cod-proveed</div>
            <div class="grillas-celdas-1">raz-social</div>
            <div class="grillas-celdas-1">moneda</div>
            <div class="grillas-celdas-1">cotiz</div>
            <div class="grillas-celdas-1">cant-ped</div>
            <div class="grillas-celdas-1">fec-entrega</div>
            <div class="grillas-celdas-1">cant-recibida</div>
            <div class="grillas-celdas-1">cant-pendiente</div>
            <div class="grillas-celdas-1">monto-sin-imp</div>
            <div class="grillas-celdas-1">estado</div>
            <div class="grillas-celdas-1">created-at</div>
            @foreach ($lista as $it)
                <div class="grillas-celdas-2 ">{{ $it->nro_compra }}</div>
                <div class="grillas-celdas-2 justify-center">
                    {{ $it->fec_emision 
                        ? \Carbon\Carbon::createFromFormat('d/m/Y H:i:s', $it->fec_emision)->format('d/m/Y') 
                        : '' 
                    }}                        
                </div>
                <div class="grillas-celdas-2">{{ $it->cod_artic }}</div>
                <div class="grillas-celdas-2">{{ $it->descrip }}</div>
                <div class="grillas-celdas-2">{{ $it->desc_adicional }}</div>
                <div class="grillas-celdas-2">{{ $it->deposito }}</div>
                <div class="grillas-celdas-2">{{ $it->cod_proveed }}</div>
                <div class="grillas-celdas-2">{{ $it->raz_social }}</div>
                <div class="grillas-celdas-2">{{ $it->moneda }}</div>

                <div class="grillas-celdas-2 justify-end">
                    {{ number_format($it->cotiz, 2) }}
                </div>
                
                <div class="grillas-celdas-2 justify-end">
                    {{ number_format($it->cant_pedida, 2) }}
                </div>

                <div class="grillas-celdas-2 justify-center">
                    {{ $it->fec_entrega 
                        ? \Carbon\Carbon::createFromFormat('d/m/Y H:i:s', $it->fec_entrega)->format('d/m/Y') 
                        : '' 
                    }}                        
                </div>

                <div class="grillas-celdas-2 justify-end">
                    {{ number_format($it->cant_recibida, 2) }}
                </div>
                <div class="grillas-celdas-2 justify-end">
                    {{ number_format($it->cant_pendiente, 2) }}
                </div>
                <div class="grillas-celdas-2 justify-end">
                    {{ number_format($it->monto_sin_imp, 2) }}
                </div>

                <div class="grillas-celdas-2">{{ $it->estado }}</div>

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
