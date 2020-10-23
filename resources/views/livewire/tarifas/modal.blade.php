<div class="modal fade" id="modalTarifa" tabindex="-1" role="dialog" aria-labelledby="modalTarifa" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            {{-- header --}}
            <div class="modal-header">
                <h5 class="modal-title">Descripción Vehículo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            {{-- body --}}
            <div class="modal-body">
                <div class="widget-content-area">
                    <div class="widget-one">
                        @include('common.messages')
                        <form>
                            <div class="row">
                                <div class="form-group col-lg-4 col-md-4 col-sm-12">
                                    <label>Tiempo</label>
                                    <select id="tiempo" class="form-control form-small">
                                        <option value="Elegir">Elegir</option>
                                        <option value="Fracción">Fracción</option>
                                        <option value="Hora">Hora</option>
                                        <option value="Día">Día</option>
                                        <option value="Semana">Semana</option>
                                        <option value="Mes">Mes</option>
                                    </select>
                                </div>
                                <div class="form-group col-lg-4 col-md-4 col-sm-12">
                                    <label>Tipo</label>
                                    <select  id="tipo" class="form-control form-small">
                                        <option value="Elegir">Elegir</option>
                                        @foreach($tipos as $t)
                                            <option value="{{$t->id}}">{{$t->descripcion}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group col-lg-4 col-md-4 col-sm-12">
                                    <label>Costo</label>
                                    <input type="number" id="costo" class="form-control text-center" value="0"
                                           placeholder="10.00">
                                </div>
                                <div class="form-group col-lg-8 col-md-8 col-sm-12">
                                    <label>Descripción</label>
                                    <input type="text" id="descripcion" class="form-control text-center"
                                           placeholder="...">
                                </div>
                                <div class="form-group col-lg-4 col-md- 4 col-sm-12">
                                    <label>Jerarquia</label>
                                    <input type="text" id="jerarquia" class="form-control text-center"
                                           value="{{$jerarquia}}" disabled>
                                </div>



                            </div>
                        </form>
                    </div>
                </div>
            </div>
            {{-- footer --}}
            <div class="modal-footer">
                <button class="btn btn-dark" data-dismiss="modal">
                    <i class="flaticon-cancel-12"></i> Cancelar</button>
                <button type="button" class="btn btn-primary saveTarifa"
                        onclick="save()">Guardar</button>
            </div>
        </div>
    </div>
</div>
