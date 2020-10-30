<section>
    <div class="row layout-top-spacing">
        <div class="col-xs-12 col-lg-12 col-md-12 layout-spacing" x-data="{ isOpen : true }" @click.away="isOpen = false">
            <div class="widget-content-area br-4">
                <div class="widget-one">
                    <!-- Titulo y regresar -->
                    <div class="row">
                        @include('common.messages')
                        <div class="col-2">
                            <button class="btn btn-dark" wire:click="$set('section', 1)">
                                <i class="la la-chevron-left"></i>
                            </button>
                        </div>
                        <div class="col-8">
                            <h5 class="text-center"><b>Ticket de pensión</b></h5>
                        </div>
                        <div class="col-2 text-right">
                            <label id="ct"></label>
                        </div>
                    </div>
                    <!-- BUSCADOR -->
                    <div class="row mt-3" x-data="{ isOpen : true }" @click.away="isOpen = false">
                        <div class="col-md-4 ml-auto">
                            <div class="input-group mb-2 mr-sm-2">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="la la-search"></i>
                                    </div>
                                </div>
                                <input type="text" class="form-control" placeholder="buscar"
                                wire:model="buscarCliente"
                                @focus="isOpen = true"
                                @keydown.escape.tab="isOpen = false"
                                @keydown.shift.tab="isOpen = false"
                                >
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i wire:click.prevent="limpiarCliente()" class="la la-trash la-lg"></i>
                                    </div>
                                </div>
                                <ul class="list-group" x-show.transition.opacity="isOpen">
                                    @if($buscarCliente !='')
                                        @foreach($clientes as $r)
                                        <li wire:click="mostrarCliente('{{$r}}')" class="list-group-item list-group-item-action">
                                        <b>{{$r->nombre}}</b> - <h7 class="text-info">Placa</h7>:{{$r->placa}} -
                                            <h7 class="text-success">Marca</h7>:{{$r->marca}} <h7 class="text-success">Color</h7>:{{$r->color}}
                                        </li>
                                        @endforeach
                                    @endif
                                </ul>
                            </div>
                        </div>
                    </div>
                    <!-- Div datos de cliente -->

                    <div class="row mt-4">
                        <h5 class="col-sm-12">Datos cliente</h5>
                        <div class="form-group col-lg-4 col-md-4 col-sm-12">
                            <h7 class="text-info">Nombre* </h7>
                            <div class="input-group mb-2 mr-sm-2">
                                <div class="input-group-prepend">
                                    <div  class="input-group-text"><i class="la la-user la-lg"></i></div>
                                </div>
                                <input type="text" wire:model.lazy="nombre"  class="form-control" maxlength="30"  placeholder="ej: John Doe">

                            </div>
                        </div>
                        <div class="form-group col-lg-2 col-md-2 col-sm-12">
                            <h7 class="text-info">Teléfono</h7>
                            <div class="input-group mb-2-sm-2">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="la la-phone la-lg"></i>
                                    </div>
                                    <input type="text" class="form-control" wire:model.lazy="telefono" maxlength="10" placeholder="ej: 2 7202700">
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-lg-2 col-md-2 col-sm-12">
                            <h7 class="text-info">Celular</h7>
                            <div class="input-group mb-2-sm-2">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="la la-mobile la-lg"></i>
                                    </div>
                                    <input type="text" class="form-control" wire:model.lazy="celular" maxlength="30" placeholder="ej: 982176481">
                                </div>
                            </div>
                        </div>
                        <div class="form-group col-lg-4 col-md-4 col-sm-12">
                            <h7 class="text-info">E-Mail</h7>
                            <div class="input-group mb-2 mr-sm-2">
                                <div class="input-group-prepend">
                                    <div class="input-group-text"><i class="la la-envelope la-lg"></i></div>
                                </div>
                                <input type="text" wire:model.lazy="email" class="form-control"  placeholder="ej: jdoe@example.com">
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <h7 class="text-info">Dirección*</h7>
                            <div class="input-group mb-2 mr-sm-2">
                                <div class="input-group-prepend">
                                    <div class="input-group-text"><i class="la la-home la-lg"></i></div>
                                </div>
                                <input type="text" wire:model.lazy="direccion" class="form-control" maxlength="255"  placeholder="">
                            </div>
                        </div>
                    </div>

                    <!-- Div datos de Vehiculo -->
                    <div class="row">
                        <h5 class="col-sm-12 mt-3">Datos del Vehículo</h5>
                        <div class="form-group col-lg-3 col-md-3 col-sm-12">
                            <h7 class="text-info">Placa *</h7>
                            <div class="input-group mb-2 mr-sm-2">
                                <div class="input-group-prepend">
                                    <div class="input-group-text"><i class="la la-car la-lg"></i></div>
                                </div>
                                <input type="text" wire:model.lazy="placa"  class="form-control" maxlength="30"  placeholder="ej: F5T789">
                            </div>
                        </div>
                        <div class="form-group col-lg-3 col-md-3 col-sm-12">
                            <h7 class="text-info">Descripción</h7>
                            <div class="input-group mb-2 mr-sm-2">
                                <div class="input-group-prepend">
                                    <div class="input-group-text"><i class="la la-edit la-lg"></i></div>
                                </div>
                                <input type="text" wire:model.lazy="nota" class="form-control" maxlength="30"  placeholder="ej: BORA">
                            </div>
                        </div>
                        <div class="form-group col-lg-2 col-md-2 col-sm-12">
                            <h7 class="text-info">Modelo</h7>
                            <div class="input-group mb-2 mr-sm-2">
                                <div class="input-group-prepend">
                                    <div class="input-group-text"><i class="la la-calendar la-lg"></i></div>
                                </div>
                                <input type="text" wire:model.lazy="modelo" class="form-control" maxlength="30"  placeholder="ej: 2001">
                            </div>
                        </div>
                        <div class="form-group col-lg-2 col-md-2 col-sm-12">
                            <h7 class="text-info">Marca</h7>
                            <div class="input-group mb-2 mr-sm-2">
                                <div class="input-group-prepend">
                                    <div class="input-group-text"><i class="la la-copyright la-lg"></i></div>
                                </div>
                                <input type="text" wire:model.lazy="marca" class="form-control" maxlength="30"  placeholder="ej: HONDA">
                            </div>
                        </div>
                        <div class="form-group col-lg-2 col-md-2 col-sm-12">
                            <h7 class="text-info">Color</h7>
                            <div class="input-group mb-2 mr-sm-2">
                                <div class="input-group-prepend">
                                    <div class="input-group-text"><i class="la la-tint la-lg"></i></div>
                                </div>
                                <input type="text" wire:model.lazy="color" class="form-control" maxlength="30"  placeholder="ej: Azul">
                            </div>
                        </div>
                    </div>

                    <!-- Tiempo fecha y calculo a cobrar-->
                    <hr></hr>
                    <hr>
                    <div class="row">

                        <div class="col-md-3 col-lg-3 col-sm-12">
                            Tiempo
                            <select wire:model="tiempo" wire:change="getSalida()" class="form-control text-center" >
                                <option value="0">Elegir</option>
                                @for($i =1; $i<=12; $i++)
                                    @if($i == 1)
                                        <option value="{{$i}}">{{$i}} MES</option>
                                    @else
                                        <option value="{{$i}}">{{$i}} MESES</option>
                                    @endif
                                @endfor
                            </select>

                        </div>
                        <div class="col-md-2 col-lg-2 col-sm-12">
                            <div class="form-group mb-0">Fecha de Ingreso
                                <input  class="form-control" type="text" value="{{ \Carbon\Carbon::now()->format('d-m-Y') }}" disabled>
                            </div>
                        </div>
                        <div class="col-md-2 col-lg-2 col-sm-12">
                            <div class="form-group mb-0">Fecha de Salida (aprox.)
                                <input wire:model="fecha_fin" class="form-control" type="text" disabled>
                            </div>
                        </div>
                        <div class="col-md-2 col-lg-2 col-sm-12">
                            <div class="form-group mb-0" >Total Calculado
                                <input  class="form-control total" type="text" disabled value="${{number_format($total,2)}}" >
                            </div>
                        </div>
                        <div class="col-md-3 col-lg-3 col-sm-12">
                            <div class="form-group mb-0" >Total Manual
                                <input wire:model="total"  class="form-control total" type="number"  >
                            </div>
                        </div>
                        <div class="col-md-4 col-lg-4 col-sm-12">
                            @if($tiempo > 0)
                                <button wire:click.prevent="RegistrarTicketRenta()" class="btn btn-success mt-4">Registrar Entrada</button>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
