<?php

namespace App\Http\Livewire;

use Livewire\WithPagination;
use Livewire\Component;
use App\Tarifa;
use App\Tipo;

class TarifaController extends Component
{

    use WithPagination;

    //properties
    public  $tiempo = 'Elegir', $descripcion, $costo, $tipo = 'Elegir', $jerarquia; //campos de la tabla/modelo
    public  $selected_id, $search;                           //para búsquedas y fila seleccionada
    public  $action = 1;                                     //manejo de ventanas
    private $pagination = 6;                                 //paginación de tabla
    public  $tipos;

    public function mount()
    {
        $this->getJerarquia();

    }


    public function getJerarquia(){
        $tarifas = Tarifa::count();
        if ($tarifas > 0) {
            $tarifa = Tarifa::select('jerarquia')->orderBy('jerarquia', 'desc')->first();
            $this->jerarquia = $tarifa->jerarquia + 1;
        } else {
            $this->jerarquia = 0;
        }
    }
    //método que se ejecuta al inciar el componente
    public function render()
    {

        $this->tipos = Tipo::all();

        if (strlen($this->search) > 0) {

            $info = Tarifa::leftjoin('tipos as t', 't.id', 'tarifas.tipo_id')
                ->where('tarifas.descripcion', 'like','%'. $this->search . '%')
                ->orWhere('tarifas.tiempo', 'like', '%' . $this->search . '%')
                ->select('tarifas.*', 't.descripcion as tipo')
                ->orderBy('tarifas.jerarquia', 'desc')
                ->orderBy('tarifas.tiempo', 'desc')
                ->orderBy('t.descripcion')
                ->paginate($this->pagination);

            return view('livewire.tarifas.component', [
                'info' => $info,
            ]);
        } else {

            $tarifas = Tarifa::leftjoin('tipos as t', 't.id', 'tarifas.tipo_id')
                ->select('tarifas.*', 't.descripcion as tipo')
                ->orderBy('tarifas.jerarquia', 'desc')
                ->orderBy('tarifas.tiempo', 'desc')
                ->orderBy('t.descripcion')

                ->paginate($this->pagination);


            return view('livewire.tarifas.component', [
                'info' => $tarifas,
            ]);
        }
    }

    //permite la búsqueda cuando se navega entre el paginado
    public function updatingSearch(): void
    {
        $this->gotoPage(1);
    }

    //activa la vista edición o creación
    public function doAction($action)
    {
        $this->resetInput();
        $this->action = $action;
    }

    //método para reiniciar variables
    private function resetInput()
    {
        $this->descripcion = '';
        $this->tiempo = 'Elegir';
        $this->costo = '';
        $this->tipo = 'Elegir';
        $this->selected_id = null;
        $this->action = 1;
        $this->search = '';
        //$this->jerarquia =null;
    }

    //buscamos el registro seleccionado y asignamos la info a las propiedades
    public function edit($id)
    {
        $record = Tarifa::findOrFail($id);
        $this->selected_id = $record->id;
        $this->descripcion = $record->descripcion;
        $this->tiempo = $record->tiempo;
        $this->costo = $record->costo;
        $this->tipo = $record->tipo->id;
        $this->jerarquia = $record->jerarquia;
        $this->action = 2;
    }


    //método para registrar y/o actualizar registros
    public function CreateOrUpdate()
    {

        $this->validate([
            'descripcion' => 'required',
            'costo'  => 'required',
            'tipo'   => 'required',
            'tiempo' => 'required',
            'tiempo' => 'not_in:Elegir',
            'tipo'   => 'not_in:Elegir'
        ]);


        if ($this->selected_id > 0) {
            $existe = Tarifa::where('tiempo', $this->tiempo)
                ->where('tipo_id', $this->tipo)
                ->where('id', '<>', $this->selected_id)
                ->select('tiempo')->get();
        } else {

            $existe = Tarifa::where('tiempo', $this->tiempo)
                ->where('tipo_id', $this->tipo)
                ->select('tiempo')->get();
        }


        if ($existe->count() > 0) {
            session()->flash('msg-error', 'Ya existe la tarifa');
            $this->resetInput();
            return;
        }




        if ($this->selected_id <= 0) {

            $tarifa =  Tarifa::create([
                'tiempo' => $this->tiempo,
                'descripcion' => $this->descripcion,
                'costo' => $this->costo,
                'tipo_id' => $this->tipo,
                'jerarquia' => $this->jerarquia
            ]);
        } else {

            $tarifa = Tarifa::find($this->selected_id);
            $tarifa->update([
                'tiempo' => $this->tiempo,
                'descripcion' => $this->descripcion,
                'costo' => $this->costo,
                'tipo_id' => $this->tipo,
                'jerarquia' => $this->jerarquia
            ]);
        }
        // if ($this->jerarquia == 1) {
        //     Tarifa::where('id', '<>', $tarifa->id)->update([
        //         'jerarquia' => 0
        //     ]);
        // }

        if ($this->selected_id){
            session()->flash('message', 'Tarifa Actualizada');
            $this->emit('msg-ok','Tarifa Actualizada');

        }else{
            session()->flash('message', 'Tarifa Creada');
            $this->emit('msg-ok', 'Tarifa Creada');
        }
        $this->resetInput();
        $this->getJerarquia();
    }


    //escuchar eventos y ejecutar acción solicitada
    protected $listeners = [
        'deleteRow'     => 'destroy',
        'createFromModal'=> 'createFromModal'
    ];


    public function createFromModal($info){

        $data = json_decode($info);

        $this->selected_id = $data->id;
        $this->tiempo = $data->tiempo;
        $this->tipo = $data->tipo;
        $this->costo = $data->costo;
        $this->descripcion = $data->descripcion;
        $this->jerarquia = $data->jerarquia;

        $this->CreateOrUpdate();

    }

    //método para eliminar un registro dado
    public function destroy($id)
    {
        if ($id) {

            $record = Tarifa::where('id', $id);
            $record->delete();
            $this->resetInput();

        }
    }
}
