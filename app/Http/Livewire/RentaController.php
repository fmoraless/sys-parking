<?php

namespace App\Http\Livewire;

use Livewire\WithPagination;
use App\ClienteVehiculo;
use Livewire\Component;

use App\Cajon;
use App\Renta;
use App\Tarifa;
use App\Tipo;
use App\User;
use App\Vehiculo;
use Carbon\Carbon;
use DB;

class RentaController extends Component
{
    //usar paginacion
    use WithPagination;

    //Propiedades
    public $selected_id, $search, $buscarCliente, $barcode, $obj, $clientes, $clienteSelected;
    public $nombre, $telefono, $celular, $email, $placa, $tipo, $total, $tiempo, $direccion;
    public $modelo, $marca, $color, $fecha_ini, $fecha_fin,$nota, $arrayTarifas, $section = 1;
    public $tarifaSelected;
    private $pagination = 5;


    public function mount()
    {
        $this->arrayTarifas = Tarifa::all();
        if ($this->arrayTarifas->count() > 0) $this->tarifaSelected = $this->arrayTarifas[0]->id;
    }

    public function render()
    {
        $clientes = null;
        $cajones = Cajon::join('tipos as t', 't.id', 'cajones.tipo_id')
            ->select('cajones.*', 't.descripcion as tipo', 't.id as tipo_id', 't.imagen',
            DB::RAW("'' as tarifa_id"), DB::RAW("'' as barcode"),
            DB::RAW("0 as folio"), DB::RAW("'' as descripcion_coche") )
            ->get();
        //dd($cajones);

        //Buscar Clientes
        if(strlen($this->buscarCliente) > 0)
        {
            $clientes = ClienteVehiculo::leftjoin('users as u', 'u.id', 'cliente_vehiculos.user_id')
                ->leftjoin('vehiculos as v', 'v.id', 'cliente_vehiculos.vehiculo_id')
                ->select('v.id as vehiculo_id', 'v.placa', 'v.marca', 'v.color', 'v.nota', 'v.modelo',
                'u.id as cliente_id', 'nombre', 'telefono', 'movil', 'email', 'direccion')
                ->where('nombre', 'like', '%'. $this->buscarCliente . '%')
                ->get();
        }
        else{
            $clientes = User::where('tipo', 'cliente')
                ->select('id', 'nombre', 'telefono', 'movil', 'email', 'direccion',
            DB::RAW("'' as vehiculos"))
            ->take(1)->get();
        }
        $this->clientes = $clientes;

        //
        foreach ($cajones as $c) {
            //dd($cajones);
            $tarifa = Tarifa::where('tipo_id', $c->tipo_id)->select('id')->first();
            //$c->tarifa_id = $tarifa['id'];
            //1era opcion //$c->tarifa_id = isset($renta['id']);
            //$c->tarifa_id = ($renta['id'] ?? '');
            //$c->tarifa_id = $tarifa['id']; error
            $c->tarifa_id = ($tarifa['id']);

            $renta = Renta::where('cajon_id', $c->id)
                ->select('barcode', 'id', 'descripcion as descripcion_coche')
                ->where('estatus', 'ABIERTO')
                ->orderBy('id', 'desc')
                ->first();

            //$c->barcode = ($renta['barcode'] == null ? '': $renta['barcode']);
            //1era opcion // $c->barcode = isset($renta['barcode']) ? ($renta['barcode']) : '' ;
            $c->barcode = ($renta['barcode'] ?? '');

            //$c->folio = ($renta['id'] == null ? '': $renta['id']);
            //1era opcion //$c->folio = isset($renta['folio']) ? ($renta['folio']) : '' ;
            $c->folio = ($renta['folio'] ?? '');

            //$c->descripcion_coche = ($renta['descripcion_coche'] == null ? '': $renta['descripcion_coche']);
            //1era opcion //$c->descripcion_coche = isset($renta['descripcion_coche']) ? ($renta['descripcion_coche']) : '' ;
            $c->descripcion_coche = ($renta['descripcion_coche'] ?? '');
        }

        return view('livewire.rentas.componente', [
            'cajones' => $cajones
        ]);
    }

    protected $listeners = [
        'RegistrarEntrada'   => 'RegistrarEntrada',
        'doCheckOut'       => 'doCheckOut',
        'doCheckIn' => 'RegistrarEntrada'
    ];

    //consultar info de un ticket dado
    public function doCheckOut($barcode, $section = 2)
    {
        $bcode = ($barcode == '' ? $this->barcode: $barcode);
        $obj = Renta::where('barcode',$bcode)->select('*', DB::RAW("'' as tiempo"),
            DB::RAW("'' as total"))->first();

        if ($obj != null)
        {
            $this->section = $section;
            $this->barcode = $bcode;

            $start = Carbon::parse($obj->acceso);
            $end = new \DateTime(Carbon::now());

            $obj->tiempo = $start->diffInHours($end) .':' .$start->diff($end)->format('%I:%S'); //dif en horas + dif en minutos y seg.
            $obj->total = $this->calculateTotal($obj->acceso, $obj->tarifa_id);
            $this->obj = $obj;
        }
        else{
            $this->emit('msg-ops', 'No existe el código de barras');
            $this->barcode = '';
            return;
        }
    }

    //calcular el total a cobrar
    public function calculateTotal($fromDate, $tarifaId, $toDate = '')
    {
        $fraccion = 0;
        $tarifa = Tarifa::where('id',$tarifaId)->first();
        $start = Carbon::parse($fromDate);
        $end = new \DateTime(Carbon::now());
        if (!$toDate == '') $end = Carbon::parse($toDate);

        $tiempo = $start->diffInHours($end). ':' .$start->diff($end)->format('%I:%S');
        $minutos = $start->diffInMinutes($end);

        $horasCompletas = $start->diffInHours($end);
        //Tarifa $13 pesos *hora
        if ($minutos <= 65){ // de 0 a 65 minutos, se cobra tarifa completa ($13)
            $fraccion =$tarifa->costo;
        }
        else{
            $m = ($minutos % 60);
            if (in_array($m, range(0,5))) { // 5 minutos de tolerancia para sacar el ccoche

            }
            elseif (in_array($m, range(6,30))){ // despues de la 1 hora, de 6-30 minutos, se cobra media tarifa ($6.50)
                $fraccion = ($tarifa->costo / 2);
            }
            elseif (in_array($m, range(31, 59))) { // despues de la 1ra hora y del 31 a 59, se cobra tarifa completa
                $fraccion = $tarifa->costo;
            }
        }
        //retornar el total a cobrar
        $total = (($horasCompletas * $tarifa->costo) +$fraccion);
        return $total;
    }

    //registrar entradas de vehiculo al estacionamiento
    public function RegistrarEntrada($tarifa_id, $cajon_id, $estatus = '', $comment = '')
    {
        //dd($tarifa_id);
        if ($estatus == 'OCUPADO') {
            $this->emit('msg-ok', 'El cajon esta ocupado');
            return;
        }
        //ponemos cajon ocupado
        $cajon = Cajon::where('id', $cajon_id)->first();
        $cajon->estatus = 'OCUPADO';
        $cajon->save();

        //Registar entrada
        $renta = Renta::create([
           'acceso' => Carbon::now(),
           'user_id' => auth()->user()->id,
           'tarifa_id' => $tarifa_id,
           'cajon_id' => $cajon_id,
           'descripcion' => $comment
        ]);
        //dd($renta);
        //generar codigo de 7 caracteres para impresion estandar Code-39
        $renta->barcode = sprintf('%07d',$renta->id);
        $renta->save();

        $this->barcode = '';
        $this->emit('getin-ok', 'Entrada registrada en sistema');
        $this->emit('print', $renta->id);

    }

    //Metodo para calcular el tiempo de estancia de vehiculo
    public function CalcularTiempo($fechaEntrada)
    {
        $start = Carbon::parse($fechaEntrada);
        $end = new \DateTime(Carbon::now());
        $tiempo = $start->diffInHours($end) . ':' . $start->diff($end)->format('%I:%S');
        return $tiempo;
    }

    public function BuscarTicket()
    {
        $nuevoTotal = 0;
        // reglas de validacion
        $rules = [
            'barcode' => 'required',
        ];

        $customMessages = [
            'barcode.required' => 'Ingresa o escanea el codigo de barras'
        ];

        $this->validate($rules, $customMessages);

        $ticket = Renta::where('barcode', $this->barcode)->select('*')->first();

        if ($ticket){
            if ($ticket->estatus == 'CERRADO')
            {
                $this->emit('msg-ops', 'El ticket ya tiene registrada la salida');
                $this->barcode = '';
                return;
            }
        }else{
            $this->emit('msg-ops', 'El código no existe en sistema');
            $this->barcode = '';
            return;
        }

        //obtener la tarifa
        $tarifa = Tarifa::where('id', $ticket->tarifa_id)->first();

        //obtener el tiempo
        $tiempo = $this->CalcularTiempo($ticket->acceso);

        //Obtener total
        $nuevoTotal = $this->calculateTotal($ticket->acceso, $ticket->tarifa_id);

        $ticket->salida = Carbon::now();
        $ticket->estatus = 'CERRADO';
        $ticket->total = $nuevoTotal;
        $ticket->hours = $tiempo;
        $ticket->save();

        //poner cajon disponible nuevamente
        $cajon = Cajon::where('id', $ticket->cajon_id)->first();
        $cajon->estatus = 'DISPONIBLE';
        $cajon->save();

        //feedback al user
        if ($ticket){
            $this->barcode = '';
            $this->section = 1;
            $this->emit('getout-ok', 'Salida registrada con exito');

        }else{
            $this->barcode = '';
            $this->section = 1;
            $this->emit('getout-error', 'no se pudo registrar salida');
        }
    }

    //video 45
    //metodo para emitir ticket rapido de entrada de vehiculos
    public function TicketVisita()
    {
        //obtener las tarifas
        $tarifas = Tarifa::select('jerarquia', 'tipo_id', 'id')->orderBy('jerarquia', 'desc')->get();
        $tarifaID;

        //obtenemos el siguiente cajon disponible
        foreach ($tarifas as $j) {
            $cajon = Cajon::where('estatus', 'DISPONIBLE')
                ->where('tipo_id', $j->tipo_id)->first();

            if ($cajon) {
                $tarifaID = $j->id;
                break;

            }
        }

        //Validar si hay un cajon vació
        if ($cajon == null){
            $this->emit('msg-ops', 'Todos los espacios estan ocupados');
            return;
        }

        //Poner el cajon ocupado
        $cajon->estatus="OCUPADO";
        $cajon->save();

        //registrar entrada
        $renta = Renta::create([
           'acceso' => Carbon::now(),
           'user_id' => auth()->user()->id(),
           'tarifa_id' => $tarifaID,
            'cajon_id' => $cajon->id
        ]);

        //Generamos el barcode a 7 digitos (stantar code 39)
        $renta->barcode = sprintf('%07d', $renta->id);
        $renta->save();

        //Feedback al usuario
        $this->barcode = '';
        $this->emit('getin-ok', 'Entrada registrada en sistema');

    }

    public function doCheckIn($tarifa_id, $cajon_id, $estatus = '')
    {
        $this->emit('checkin-ok','Entrada Registrada en Sistema');
    }

    //video 46
    //Ticket de pension
    public function RegistrarTicketRenta(){
        //reglas de validacion
        $rules = [
          'nombre' => 'required|min:3',
          'direccion' => 'required',
          'placa' => 'required',
          'email' =>  'nullable|email'
        ];

        //Mensajes personalizados.
        $customMessages = [
          'nombre.required' => 'EL campo nombre es requerido',
          'direccion.required' => 'Por favor ingresa la direccion',
          'placa.required' => 'Debes ingresar el numero de placa'
        ];

        //Ejecutar las validaciones.
        $this->validate($rules, $customMessages);

        //Verificamos que el vehiculo no tenga tickets abiertos
        $exist = Renta::where('placa', $this->placa)
            ->where('vehiculo_id', '>', 0)
            ->where('estatus', 'ABIERTO')
            ->count();

        if ($exist > 0){
            $this->emit('msg-error', "La placa $this->placa tiene una renta registrada aun vigente");
            return;
        }

        //iniciar transacción
        DB::beginTransaction();
        try {
            if ($this->clienteSelected > 0) {
                $cliente = User::find($this->clienteSelected);
            } else {
                //validar si se ingresó correo  o generamos uno
                if (empty($this->email)) $this->email = str_replace(' ', '_', $this->nombre) . '_' . uniqid() . '_@sysparking.com';
                $cliente = User::create([
                    'nombre' => $this->nombre,
                    'telefono' => $this->telefono,
                    'movil' => $this->celular,
                    'direccion' => $this->direccion,
                    'tipo' => 'Cliente',
                    'email' => $this->email,
                    'password' => bcrypt('secret2020'),
                ]);
            }

            //registrar el vehicuo en sistema
            $vehiculo = Vehiculo::create([
                'placa' => $this->placa,
                'modelo' => $this->modelo,
                'marca' => $this->marca,
                'color' => $this->color,
                'nota' => $this->nota,
            ]);

            //registrar la asociacion vehiculos y clientes
            $cv = ClienteVehiculo::create([
                'user_id' => $cliente->id,
                'vehiculo_id' => $vehiculo->id,

            ]);

            //registrar ticket en  rentas
            $renta = Renta::create([
                'acceso' => Carbon::parse($this->fecha_ini),
                'salida' => Carbon::parse($this->fecha_fin),
                'user_id' => auth()->user()->id,
                'tarifa_id' => $this->tarifaSelected,
                'placa' => $this->placa,
                'modelo' => $this->modelo,
                'marca' => $this->marca,
                'color' => $this->color,
                'descripcion' => $this->nota,
                'direccion' => $this->direccion,
                'vehiculo_id' => $vehiculo->id,
                'total' => $this->total,
                'hours' => $this->tiempo,

            ]);

            //generar el barcode
            $renta->barcode = sprintf('%07d', $renta->id);
            $renta->save();

            //Enviar feedback al usuario
            $this->barcode = '';
            $this->emit('getin-ok', 'Se registro el cliente y renta en sistema');
            $this->emit('print', $renta->id);
            $this->section = 1; //cambiar a la seccion principal.
            $this->limpiarCliente();

            //Confirmar la transaccion
            DB::commit();
            }catch(\Exception $e){
                //En caso de error deshacer para no generar inconsistencia
                DB::rollback();
                $estatus = $e->getMessage();
                dd($e);
            }
    }


    //Metodo para obtener tarifa, fecha de salida y total a cobrar
    // en tickets de renta
    public function getSalida()
    {
        if ($this->tiempo <=0){
            $this->total = number_format(0,2);
            $this->fecha_fin = '';
        }
        else{
            $this->fecha_fin = Carbon::now()->addMonths($this->tiempo)->format('d-m-Y');
            $tarifa = Tarifa::where('tiempo', 'MES')->select('costo')->first();

            if ($tarifa->count())
            {
                //calcular total meses*tarifa
                $this->total = $this->tiempo * $tarifa->costo;
            }
        }
    }


    public function mostrarCliente($cliente)
    {
        //dd($cliente);
        $this->clientes = '';
        $this->buscarCliente ='';
        $clienteJson = json_decode($cliente);

        $this->nombre = $clienteJson->nombre;
        $this->telefono = $clienteJson->telefono;
        $this->celular = $clienteJson->movil;
        $this->email = $clienteJson->email;
        $this->direccion = $clienteJson->direccion;

        $this->placa = $clienteJson->placa;
        $this->modelo = $clienteJson->modelo;
        $this->color = $clienteJson->color;
        $this->marca = $clienteJson->marca;
        $this->nota = $clienteJson->nota;
        $this->clienteSelected = $clienteJson->cliente_id;
    }

    public function limpiarCliente()
    {
        $this->nombre = '';
        $this->telefono = '';
        $this->celular = '';
        $this->email = '';
        $this->direccion = '';

        $this->placa = '';
        $this->modelo = '';
        $this->color = '';
        $this->marca = '';
        $this->nota = '';
        $this->clienteSelected = null;
    }
}
