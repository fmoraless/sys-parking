<div class="row layout-top-spacing">

    <div class="col-xl-12 col-lg-12 col-md-12 col-12 layout-spacing">
        <div class="widget-content-area br-4">
            <div class="widget-one">
                <h5>
                    <b>  @if($selected_id ==0)
                            Crear Nuevo usuario
                         @else
                            Editar usuario
                        @endif
                    </b>
                </h5>
                @include('common.messages')


                <div class="row">
                    <div class="form-group col-lg-4 col-md-4 col-sm-12">
                        <label>Nombre</label>
                        <input type="text" class="form-control" placeholder="Nombre de usuario"
                               wire:model.lazy="nombre">
                    </div>
                    <div class="form-group col-lg-4 col-md-4 col-sm-12">
                        <label>Telefono</label>
                        <input type="text" class="form-control" placeholder="Teléfono"
                               wire:model.lazy="telefono" maxlength="15">
                    </div>
                    <div class="form-group col-lg-4 col-md-4 col-sm-12">
                        <label>Movil</label>
                        <input type="text" class="form-control" placeholder="Móvil"
                               wire:model.lazy="movil" maxlength="15">
                    </div>
                    <div class="form-group col-lg-4 col-md-4 col-sm-12">
                        <label>Correo</label>
                        <input type="text" class="form-control" placeholder="correo@mail.com"
                               wire:model.lazy="email">
                    </div>
                    <div class="form-group col-lg-4 col-md-4 col-sm-12">
                        <label>TIpo</label>
                        <select name="" id="" wire:model="tipo" class="form-control text-center">
                            <option value="Elegir" disabled>Elegir</option>
                            <option value="admin">Admin</option>
                            <option value="empleado">Empleado</option>
                        </select>
                    </div>
                    <div class="form-group col-lg-4 col-md-4 col-sm-12">
                        <label>Password</label>
                        <input type="password" class="form-control" placeholder="Contraseña"
                               wire:model.lazy="password">
                    </div>
                    <div class="form-group col-lg-4 col-md-4 col-sm-12">
                        <label>Direccion</label>
                        <input type="text" class="form-control" placeholder="Dirección"
                               wire:model.lazy="direccion">
                    </div>
                </div> {{--cierre row1 --}}
                <div class="row">
                    <div class="col-lg-5 mt-2 text-left">
                        <button type="button" wire:click="doAction(1)" class="btn btn-dark mr-1">
                            <i class="mbri-left"></i> Regresar
                        </button>
                         <button type="button"
                            wire:click="StoreOrUpdate() "
                            class="btn btn-primary">
                        <i class="mbri-success"></i> Guardar
                       </button>
                    </div>
                </div> {{-- Cierre Row2--}}
            </div>
         </div>
    </div>
</div>
