<?php

namespace App\Http\Livewire;

use App\User;
use Livewire\Component;
use Livewire\WithPagination;

class UsuarioController extends Component
{
    use WithPagination;

    //Propiedades
    public $tipo = 'Elegir', $nombre,$telefono,$movil, $email,$direccion,$password;
    public $selected_id, $search;
    public $action = 1, $pagination = 5;

    public function render()
    {
        if (strlen($this->search) > 0)
        {
            $info = User::where('nombre', 'like', '%'. $this->search . '%')
                ->orWhere('email', 'like', '%'. $this->search . '%')
                ->paginate($this->pagination)
            ;
            return view('livewire.usuarios.component', ['info' => $info]);
        }else{
            $info = User::orderBy('id', 'desc')
                ->paginate($this->pagination)
            ;
            return view('livewire.usuarios.component', ['info' => $info]);
        }

    }

    public function updatingSearch(): void
    {
        $this->gotoPage(1);
    }

    public function doAction($action)
    {
        $this->resetInput();
        $this->action = $action;
    }

    //limpiar properties
    public function resetInput()
    {
        $this->nombre = '';
        $this->tipo = 'Elegir';
        $this->telefono = '';
        $this->movil = '';
        $this->email = '';
        $this->direccion = '';
        $this->password = '';
        $this->selected_id = null;
        $this->action = 1;
        $this->search = '';
    }
    //mostrar info del registro editar
    public function edit($id)
    {
        $record = User::findOrFail($id);
        $this->nombre = $record->nombre;
        $this->tipo = $record->tipo;
        $this->telefono = $record->telefono;
        $this->movil = $record->movil;
        $this->email = $record->email;
        $this->direccion = $record->direccion;
        $this->password = $record->password;
        $this->selected_id = $record->id;
        $this->action = 2;
        $this->search = '';
    }

    //Altas/actualizaciones
    public function StoreOrUpdate()
    {
        //validar descripcion tenga informacion (campo requerido)
        $this->validate([
            'nombre' => 'required',
            'password' => 'required',
            'email' => 'required|email',
            'tipo' => 'required',
            'tipo' => 'not_in:Elegir',
        ]);

        //validar si existe otro registro con el mismo nombre
        if ($this->selected_id <= 0)
        {
            $user = User::create([
                'nombre'   => $this->nombre,
                'tipo'     => $this->tipo,
                'telefono' => $this->telefono,
                'movil'    => $this->movil,
                'email'    => $this->email,
                'direccion'=> $this->direccion,
                'password' => bcrypt($this->password),
                ]);
        }
        else
        {
            $user = User::find($this->selected_id);
            $user->update([
                'nombre'   => $this->nombre,
                'tipo'     => $this->tipo,
                'telefono' => $this->telefono,
                'movil'    => $this->movil,
                'email'    => $this->email,
                'direccion'=> $this->direccion,
                'password' => bcrypt($this->password),
            ]);
        }

        if($this->selected_id)
            session()->flash('message', 'Usuario Actualizado');
        else
            session()->flash('message', 'Usuario Creado');

        //Limpiar campos
        $this->resetInput();
    }

    protected $listeners = [
        'deleteRow' => 'destroy',
    ];

    //Eliminar registros
    public function destroy($id)
    {
        if($id){
            $record = User::where('id', $id);
            $record->delete();
            $this->resetInput();
//            $this->emit('msgok', 'Registro eliminado con éxito');
        }
    }
}
