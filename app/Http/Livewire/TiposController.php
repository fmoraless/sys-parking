<?php

namespace App\Http\Livewire;

use Intervention\Image\Facades\Image;
use Livewire\WithPagination;
use Livewire\Component;
use App\Tipo;

class TiposController extends Component
{
    use WithPagination;

    //public properties
    public $descripcion, $image; //campos de la tabla tipos
    public $selected_id, $search; //para búsquedas y fila seleccionada
    public $action = 1; //permitir movernos entre forms (ventanas)
    private $pagination = 5; //Paginacion de tabla

    //es el primer que se ejecuta al iniciar el componente
    public function mount()
    {
        //iniciarlizar variables / data
    }

    //se ejecuta despues del mount
    public function render()
    {
        if (strlen($this->search) > 0)
        {
            $info = Tipo::where('descripcion', 'like', '%' . $this->search . '%')
                ->paginate($this->pagination);
            return view('livewire.tipos.component', [
                'info' => $info,
            ]);
        }
        else {
            // caso contrario solo retornamos el componente inyectado con 5 registros
            return view('livewire.tipos.component', [
                'info' => Tipo::paginate($this->pagination),
            ]);
        }
    }

    //para busquedas con paginacion
    public function updatingSearch(): void
    {
        $this->gotoPage(1);
    }

    //para moverse entre ventanas
    public function doAction($action)
    {
        $this->action = $action;
    }

    //limpiar properties
    public function resetInput()
    {
        $this->descripcion = '';
        $this->selected_id = null;
        $this->action = 1;
        $this->search = '';
    }

    //mostrar info del registro editar
    public function edit($id)
    {
        $record = Tipo::findOrFail($id);
        $this->descripcion = $record->descripcion;
        $this->selected_id = $record->id;
        $this->action = 2;
    }

    //Altas/actualizaciones
    public function StoreOrUpdate()
    {
        //validar descripcion tenga informacion (campo requerido)
        $this->validate([
           'descripcion' => 'required|min:4'
        ]);

        //validar si existe otro registro con el mismo nombre
        if ($this->selected_id > 0)
        {
            $existe = Tipo::where('descripcion', $this->descripcion)
                ->where('id', '<>', $this->selected_id)
                ->select('descripcion')
                ->get();

                if ($existe->count() > 0){
                session()->flash('msg-error', 'Ya existe el tipo');
                $this->resetInput();
                return;
            }
        }
        else
        {
            $existe = Tipo::where('descripcion', $this->descripcion)
                ->select('descripcion')
                ->get();

                if ($existe->count() > 0){
                session()->flash('msg-error', 'Ya existe');
                $this->resetInput();
                return;
            }
        }

        if ($this->selected_id <= 0){
            //Creamos el registro
            $tipo = Tipo::create([
                'descripcion' => $this->descripcion
            ]);
            if($this->image)
            {
                $image = $this->image;
                $fileName = time().'.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
                $moved = \Image::make($image)->save('images/tipos/'.$fileName);

                if($moved)
                {
                    $tipo ->imagen = $fileName;
                    $tipo->save();
                }
            }
        }
        else
        {
            //buscamos el registro tipo
            $record = Tipo::find($this->selected_id);
            //actualizamos la info
            $record->update([
               'descripcion' => $this->descripcion
            ]);
            if($this->image) {
                $image = $this->image;
                $fileName = time().'.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
                $moved = \Image::make($image)->save('images/tipos/'. $fileName);

                if ($moved) {
                    $record->imagen = $fileName;
                    $record->save();
                }
            }
        }
        if($this->selected_id)
            session()->flash('message', 'Tipo Actualizado');
        else
            session()->flash('message', 'Tipo Creado');

        //Limpiar campos
        $this->resetInput();
    }

    //listeners / escuchar eventos y ejecutar acciones solicitadas
    protected $listeners = [
        'deleteRow' => 'destroy',
        'fileUpload' =>'handleFileUpload'
    ];

    public function handleFileUpload($imageData)
    {
        //dd($imageData);
        $this->image = $imageData;
    }

    //Eliminar registros
    public function destroy($id)
    {
        if($id){
            $record = Tipo::where('id', $id);
            $record->delete();
            $this->resetInput();
        }
    }


}
