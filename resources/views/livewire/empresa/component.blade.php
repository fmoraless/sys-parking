<div class="widget-content-area">
   <div class="widget-one">
       <div class="row">
           @include('common.messages')
           <div class="col-12">
               <h4 class="text-center">Datos de la empresa</h4>
           </div>
           <div class="form-group col-sm-12">
               <label for="">Nombre</label>
               <input wire:model.lazy="nombre" type="text" class="form-control text-left">
           </div>
           <div class="form-group col-sm-12 col-md-4 col-lg-4">
               <label for="">Teléfono</label>
               <input wire:model.lazy="telefono" maxlength="12" type="text" class="form-control text-left">
           </div>
           <div class="form-group col-sm-12 col-md-4 col-lg-4">
               <label for="">Email</label>
               <input wire:model.lazy="email" maxlength="65" type="text" class="form-control text-left">
           </div>
           <div class="form-group col-sm-12 col-md-4 col-lg-4">
               <label for="">Logo</label>
               <input type="file" class="form-control" id="image"
               wire:change="$emit('fileChoosen', this)" accept="image/x-png, image/gif, image/jpeg">
           </div>
           <div class="form-group col-sm-12">
               <label for="">Dirección</label>
               <input wire:model.lazy="direccion" type="text" class="form-control text-left">
           </div>
           <div class="col-sm-12">
               <button type="button"
               wire:click.prevent="Guardar"
               class="btn btn-primary ml-2"
               >
                   <i class="mbri-success"></i>
                Guardar
               </button>
           </div>
       </div>
   </div>
</div>
<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', function(){
        window.livewire.on('fileChoosen', () => {
            console.log($(this))
            let inputField = document.getElementById('image')
            let file = inputField.files[0]
            let reader = new FileReader();
            //console.log(reader.result);
            reader.onloadend = () => {
                window.livewire.emit('logoUpload', reader.result)
            }
            reader.readAsDataURL(file);
        });
    });
</script>
