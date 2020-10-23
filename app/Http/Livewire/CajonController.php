<?php

namespace App\Http\Livewire;

use App\Tipo;
use App\Cajon;
use Livewire\Component;
use Livewire\WithPagination;


class CajonController extends Component
{
    use WithPagination; //Paginacion de livewire

    //propiedades
    public $tipo = 'Elegir', $descripcion, $estatus='DISPONIBLE', $tipos;
    public $selected_id, $search;
    public $action = 1, $pagination =5;

    /*se ejecuta antes de todos los compoenntes */
    public function mount()
    {

    }
    public function render()
    {
        $this->tipos = Tipo::all();

        if(strlen($this->search) > 0)
        {
            $info = Cajon::leftjoin('tipos as t', 't.id','cajones.tipo_id')
                ->select('cajones.*', 't.descripcion as tipo')
                ->where('cajones.descripcion', 'like', '%' .$this->search .'%')
                ->orWhere('cajones.estatus', 'like', '%' .$this->search .'%')
                ->paginate($this->pagination);

            return view('livewire.cajones.component',[
                'info' => $info
            ]);
        }
        else
        {
            $info = Cajon::leftjoin('tipos as t', 't.id','cajones.tipo_id')
                ->select('cajones.*', 't.descripcion as tipo')
                ->orderBy('cajones.id', 'desc')
                ->paginate($this->pagination)
            ;
            return view('livewire.cajones.component',[
                'info' => $info
            ]);
        }
    }

    //Paginado por busqueda
    public function updatingSearch()
    {
        $this->gotoPage(1);
    }

    public function doAction($action)
    {
        $this->resetInput();
        $this->action = $action;
    }

    public function resetInput()
    {
        $this->descripcion = '';
        $this->tipo = 'Elegir';
        $this->estatus = 'DISPONIBLE';
        $this->selected_id = 'null';
        $this->action = 1;
        $this->search = '';
    }

    public function edit($id)
    {
        $record = Cajon::findOrFail($id);
        $this->selected_id = $id;
        $this->tipo = $record->tipo_id;
        $this->descripcion = $record->descripcion;
        $this->estatus = $record->estatus;
        $this->action = 2;
    }

    public function StoreOrUpdate()
    {
        $this->validate([
           'tipo' => 'not_in:Elegir'
        ]);

        $this->validate([
            'tipo' => 'required',
            'descripcion' => 'required',
            'estatus' => 'required'
        ]);

        if ($this->selected_id <= 0)
        {
            $cajon = Cajon::create([
               'descripcion' => $this->descripcion,
               'tipo_id' => $this->tipo,
               'estatus' => $this->estatus
            ]);
        }
        else
        {
            $record = Cajon::find($this->selected_id);
            $record->update([
                'descripcion' => $this->descripcion,
                'tipo_id' => $this->tipo,
                'estatus' => $this->estatus
            ]);
        }
        if ($this->selected_id)
            $this->emit('msgok',"Cajon Actualuizado");
        else
            $this->emit('msgok',"Cajon Creado con éxito");

        $this->resetInput();
    }

    public function destroy($id)
    {
        if ($id)
        {
            $record = Cajon::where('id', $id);
            $record->delete();
            $this->resetInput();
            $this->emit('msgok', 'Regustri eliminado con exito');
        }
    }

    protected $listeners = [
      'deleteRow' => 'destroy'
    ];
}
