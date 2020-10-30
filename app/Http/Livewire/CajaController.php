<?php

namespace App\Http\Livewire;

use App\Caja;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;


class CajaController extends Component
{
    use WithPagination;

    //Properties
    public $tipo = 'Elegir', $concepto, $monto, $comprobante; //para qu se cargue el selet
    public $selected_id, $search; //operar con filas de tabla y busquedas
    public $action = 1, $pagination = 5; // mover entre vistas, paginacion


    public function render()
    {
        if(strlen($this->search) > 0)
        {

            return view('livewire.movimientos.component', [
                //busqueda por tipo de  movimiento o concepto
                'info' => Caja::where('tipo', 'like', '%'. $this->search . '%')
                ->orwhere('concepto', 'like' , '%'. $this->search . '%')
                ->paginate($this->pagination),
            ]);
        }
        //Cuando el usuario no teclea nada
        else {
            $caja = Caja::leftjoin('users as u', 'u.id', 'cajas.user_id')
                ->select('cajas.*', 'u.nombre')
                ->orderBy('id', 'desc')
                ->paginate($this->pagination);

            return view('livewire.movimientos.component', [
                'info' => $caja
            ]);
        }

    }
    //Permite hacer el update cuando estamos buscando
    public function updatingSearch()
    {
        $this->gotoPage(1);
    }

    //Permite posicionarnos en una de las vistas
    public function doAction($action)
    {
        $this->resetInput();
        $this->action = $action;
    }

    //limpia nuestras properties
    public function resetInput()
    {
        $this->concepto = '';
        $this->tipo = 'Elegir';
        $this->monto = '';
        $this->comprobante = '';
        $this->selected_id = null;
        $this->action = 1;
        $this->search = '';
    }

    public function edit($id)
    {
        $record = Caja::find($id); //Se busca el id recibido y se llenan las properties
        $this->selected_id = $id;
        $this->tipo = $record->tipo;
        $this->concepto = $record->concepto;
        $this->monto = $record->monto;
        $this->comprobante = $record->comprobante;
        $this->action = 2;
    }

    public function StoreOrUpdate()
    {
        //valida que el usuario seleccione algo distinto a "Elegir"
        $this->validate([
            'tipo' => 'not_in:Elegir'
        ]);
        //Validar demas campos
        $this->validate([
            'tipo' => 'required',
            'monto' => 'required',
            'concepto' => 'required'
        ]);

        //validar si el usuario a seleccionado una fila para editar y sino, para crear
        if ($this->selected_id <= 0) //el usuario no seleccionó nada de la tabla
        {
            $caja = Caja::create([
               'monto' => $this->monto,
               'tipo' => $this->tipo,
               'concepto' => $this->concepto,
               'user_id' => Auth::user()->id  // auth()->user()->id
            ]);
            //comprobar si se está enmviando una imagen
            //dd($this->image);
            if ($this->image)
            {
                $image = $this->comprobante;
                //separa el nombre del archivo y se le da un nombre unico
                $fileName = time(). '.'. explode('/', explode(':', substr($image, 0,strpos($image, ';')))[1])[1];
                //guarda la imagen en la ruta con su nombre
                $moved = \Image::make($image)->save('images/movs/'.$fileName);
                if ($moved)
                {
                    $caja->comprobante = $fileName;
                    $caja->save();
                }
            }
        }
        else
        {
            $record = Caja::find($this->selected_id);
            $record->update([
                'monto' => $this->monto,
                'tipo' => $this->tipo,
                'concepto' => $this->concepto,
                'user_id' => Auth::user()->id  // auth()->user()->id
            ]);
            if ($this->comprobante)
            {
                $image = $this->comprobante;
                //separa el nombre del archivo y se le da un nombre unico
                $fileName = time().'.' . explode('/', explode(':', substr($image, 0, strpos($image, ';')))[1])[1];
                //guarda la imagen en la ruta con su nombre
                //$moved = \Image::make($image)->save('images/movs/'.$fileName);
                $moved = \Image::make($image)->save('images/movs/'.$fileName);
                if ($moved)
                {
                    $record->comprobante = $fileName;
                    $record->save();
                }
            }
        }
        //FeedBack para el usuario.
        if ($this->selected_id)
            $this->emit('msgok',"Movimiento de caja Actualuizado");
        else
            $this->emit('msgok',"Movimiento de caja Creado con éxito");

        $this->resetInput();
    }

    protected $listener = [
        'deleteRow' => 'destroy',
        'fileUpload' => 'handleFileUpload'
    ];

    public function handleFileUpload($imageData){
        dd($imageData);
        $this->image = $imageData;
    }
    public function destroy($id){
        if ($id){
            $record = Caja::where('id', $id);
            $record->delete();
            $this->resetInput();
        }
    }
}
