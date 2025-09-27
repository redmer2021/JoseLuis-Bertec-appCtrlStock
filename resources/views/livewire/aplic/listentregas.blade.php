<div>
    <span class="font-bold text-[1.5rem]">ENTREGAS PENDIENTES</span>
    <div class="w-[100rem] overflow-x-auto">
        <div class="grid gap-1 grid-cols-[130px_300px_300px_250px_80px_100px_100px_150px_130px_110px_110px_300px_110px_110px_110px_300px_110px_110px_110px_110px]">
            <div class="grillas-celdas-1">cod_artic</div>
            <div class="grillas-celdas-1">descrip</div>
            <div class="grillas-celdas-1">desc_adic</div>
            <div class="grillas-celdas-1">cod_art_kit</div>
            <div class="grillas-celdas-1">renglon</div>
            <div class="grillas-celdas-1">cant_pedida</div>
            <div class="grillas-celdas-1">pend_desc</div>
            <div class="grillas-celdas-1">pend_factu</div>
            <div class="grillas-celdas-1">nro_pedido</div>
            <div class="grillas-celdas-1">fec_pedido</div>
            <div class="grillas-celdas-1">fec_entrega</div>
            <div class="grillas-celdas-1">raz_social</div>
            <div class="grillas-celdas-1">plan_entrega</div>
            <div class="grillas-celdas-1">nro_o_compra</div>
            <div class="grillas-celdas-1">cod_vend</div>
            <div class="grillas-celdas-1">vendedor</div>
            <div class="grillas-celdas-1">importe</div>
            <div class="grillas-celdas-1">moneda</div>
            <div class="grillas-celdas-1">cotiza</div>
            <div class="grillas-celdas-1">created_at</div>

            @foreach ($lista as $it)
                <div class="grillas-celdas-2 ">{{ $it->cod_artic }}</div>
                <div class="grillas-celdas-2 ">{{ $it->descrip }}</div>
                <div class="grillas-celdas-2 ">{{ $it->desc_adic }}</div>
                <div class="grillas-celdas-2 ">{{ $it->cod_art_kit }}</div>
                <div class="grillas-celdas-2 justify-center">{{ $it->renglon }}</div>                                
                <div class="grillas-celdas-2 justify-end">{{ number_format($it->cant_pedida, 2) }}</div>
                <div class="grillas-celdas-2 justify-end">{{ number_format($it->pend_desc, 2) }}</div>
                <div class="grillas-celdas-2 justify-end">{{ number_format($it->pend_factu,2 ) }}</div>
                <div class="grillas-celdas-2 ">{{ $it->nro_pedido }}</div>
                
                <div class="grillas-celdas-2 justify-center">
                    {{ $it->fec_pedido 
                        ? \Carbon\Carbon::createFromFormat('d/m/Y H:i:s', $it->fec_pedido)->format('d/m/Y') 
                        : '' 
                    }}
                </div>
                <div class="grillas-celdas-2 justify-center">
                    {{ $it->fec_entrega 
                        ? \Carbon\Carbon::createFromFormat('d/m/Y H:i:s', $it->fec_entrega)->format('d/m/Y') 
                        : '' 
                    }}
                </div>

                <div class="grillas-celdas-2 ">{{ $it->raz_social }}</div>
                <div class="grillas-celdas-2 justify-center">
                    {{ $it->plan_entrega 
                        ? \Carbon\Carbon::createFromFormat('d/m/Y H:i:s', $it->plan_entrega)->format('d/m/Y') 
                        : '' 
                    }}                        
                </div>
                <div class="grillas-celdas-2 ">{{ $it->nro_o_compra }}</div>
                <div class="grillas-celdas-2 ">{{ $it->cod_vend }}</div>
                <div class="grillas-celdas-2 ">{{ $it->vendedor }}</div>
                <div class="grillas-celdas-2 justify-end">{{ number_format($it->importe, 2) }}</div>
                <div class="grillas-celdas-2 ">{{ $it->moneda }}</div>
                <div class="grillas-celdas-2 justify-end">{{ number_format($it->cotiza, 2) }}</div>
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
