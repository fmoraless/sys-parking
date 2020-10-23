<section>
    <div class="row layout-top-spacing">
        <div class="col-xs-12 col-lg-12 col-md-12 layout-spacing" x-data="{ isOpen : true }" @click.away="isOpen = false">
            <div class="widget-content-area br-4">
                <div class="widget-one">
                    <!-- Titulo y regresar -->
                    <div class="row">
                        @include('common.messages')
                        <div class="col-2">
                            <button class="btn btn-dark" wire:click="$set('action', 1)">
                                <i class="la la-chevron-left"></i>
                            </button>
                        </div>
                        <div class="col-8">
                            <h5 class="text-center"><b>Ticket de pensión</b></h5>
                        </div>
                        <div class="col-2 text-right">
                            <label id="tc"></label>
                        </div>
                    </div>
                    <!-- BUSCADOR -->
                    <div class="row mt-3" x-data="{ isOpen : true }" @click.away="isOpen = false">
                        <div class="col-md-4 ml-auto">
                            <div class="input-group mb-2 mr-sm-2">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="la sa-search"></i>
                                    </div>
                                </div>
                                <input type="text" class="form-control" placeholder="buscar"
                                wire:model="buscar_cliente"
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
                                        @foreach()
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
