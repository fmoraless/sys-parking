<div class="widget-content-area">
    <div class="widget-one">
        <form>
            <h3>Crear/Editar Movimientos</h3>
            @include('common.messages')

            <div class="row">
                <div class="form-group col-lg-4 col-md-4 col-sm-12">
                    <label for="">Tipo</label>
                    <select name="" id="" wire:model="tipo" class="form-control text-center">
                        <option value="Elegir" disabled>Elegir</option>
                        <option value="Ingreso">Ingreso</option>
                        <option value="Gasto">Gasto</option>
                        <option value="Pago de Renta">Pago de Renta</option>
                    </select>
                </div>
                <div class="form-group col-lg-4 col-md-4 col-sm-12">
                    <label for="">Monto</label>
                    <input type="number" wire:model.lazy="monto" class="form-control text-center" placeholder="ej: 100.00">
                </div>
                <div class="form-group col-lg-3 col-md-3 col-sm-12">
                    <label >Comprobante</label>
                {{-- {{$image}} --}}
                    <input type="file" class="form-control text-center" id="image"
                           wire:change="$emit('fileChoosen',this)" accept="image/x-png, image/gif, image/jpeg"
                    >

                </div>
                <div class="form-group col-lg-12 col-md-12 col-sm-12">
                    <label for="">Ingresa descripción</label>
                    <input id="concepto" type="text" class="form-control" wire:model.lazy="concepto">
                </div>
            </div>
                <div class="row">
                    <div class="col-lg-5 mt-2 text-left">
                        <button type="button" class="btn btn-dark mr-1"
                                wire:click="doAction(1)">
                            <i class="mbri-left"></i>Regresar
                        </button>
                        <button type="button"
                                wire:click="StoreOrUpdate() "
                                class="btn btn-primary ml-2">
                            <i class="mbri-success"></i> Guardar
                        </button>
                    </div>
                </div>
        </form>
    </div>
</div>
