<secton id="Salidas">
    <div class="row-layout-top-spacing">
        <div class="col-xm-12 col-lg-12 col-sm-12 col-md-12 layout-spacing">
            <div class="widget-content-area br-4">
                <div class="widget-one">
                    <!-- row Header -->
                    <div class="row">
                        <div class="col-2">
                            <button class="btn btn-dark" wire:click="$set('section',1)">
                                <i class="la la-chevron-left"></i>
                            </button>
                        </div>
                        <div class="col-8">
                            <h5 class="text-center"><b>REGISTRAR SALIDA</b></h5>
                        </div>
                        <div class="col-2">
                            <label id="tc"></label>
                        </div>
                    </div>
                    <!-- /row Header -->

                    <!-- -->
                    <div class="row">
                        <div class="col-12">
                            @if(count($errors) > 0)
                                @foreach($errors->all() as $error)
                                <small class="text-danger">{{$error}}</small>
                                @endforeach
                            @endif
                            <div class="input-group mb-4 mt-4">
                                <div class="input-group-prepend">
                                    <span class="input-group-text">
                                        <i class="la la-barcode"></i>
                                    </span>
                                </div>
                                <input type="text" id="code" wire:keydown.enter="BuscarTicket()" wire:model="barcode" class="form-control"
                                       maxlength="9" placeholder="Ingresa o escanea el código de barras" autofocus>
                                <div class="input-group-append">
                                    <span wire:click="BuscarTicket()" class="input-group-text" style="cursor: pointer;">
                                        <i class="la la-print la-lg"></i>Registrar Salida
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- row info pago -->
                    <div class="row">
                        <div class="col-lg-8 col-md-8 col-sm-12">
                            <div class="col-sm-12">
                                <h6><b>Folio</b>: {{ $obj->id }}</h6>
                                <input type="hidden" id="ticketid" value="{{$obj->id}}">
                            </div>
                            <div class="div col-sm-12">
                                <h6><b>Estatus:</b>{{ $obj->estatus }}</h6>
                            </div>
                            <div class="div col-sm-12">
                                <h6><b>Tarifa:</b>{{ number_format($obj->tarifa->costo,2) }}</h6>
                            </div>
                            <div class="div col-sm-12">
                                <h6><b>Acceso:</b>{{ \Carbon\Carbon::parse($obj->acceso)->format('d/m/Y h:m:s') }}</h6>
                            </div>
                            <div class="div col-sm-12">
                                <h6><b>Barcode:</b>{{ $obj->barcode }}</h6>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-4 col-sm-12">
                            <blockquote class="blockquote text-center">
                                <h5><b>Cobro hasta el momento</b></h5>
                                <h6>Tiempo transcurrido: {{ $obj->tiempo }}</h6>
                                <h6>Total: {{ number_format($obj->total,2) }}</h6>
                            </blockquote>
                        </div>
                    </div>
                    <!-- /row info pago -->
                </div>
            </div>
        </div>
    </div>
</secton>
