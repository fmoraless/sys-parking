<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\ClienteVehiculo;
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
    public $modelo, $marca, $color, $fecha_ini, $fecha_fin,$nota, $arrayTarifas;
    public $tarifaSelected;
    private $pagination = 5, $section = 1;


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

        //Buscar Clientes
        if(strlen($this->buscarCliente) > 0)
        {
            $clientes = ClienteVehiculo::leftjoin('users as u', 'u.id', 'cliente_vehiculos.user_id')
                ->leftjoin('vehiculos as v', 'v.id', 'cliente_vehiculos.vehiculo_id')
                ->select('v.id as vehiculo_id', 'v.placa', 'v.marca', 'v.color', 'v.nota', 'v.modelo',
                'u.id as cliente_id', 'nombre', 'nombre', 'telefono', 'movil', 'email', 'direccion')
                ->where('nombre', 'like', '%'. $this->buscarCliente . '%')
                ->get();
        }
        else{
            $clientes = User::where('tipo', 'cliente')->select('id', 'nombre', 'telefono', 'movil', 'email', 'direccion',
            DB::RAW("'' as vehiculos"))
            ->take()->get();
        }
        $this->clientes = $clientes;

        //
        foreach ($cajones as $c) {
            $tarifa = Tarifa::where('tipo_id', $c->tipo_id)->select('id')->first();
            $c->tarifa_id = $tarifa['id'];

            $renta = Renta::where('cajon_id', $c->id)->select('barcode', 'id', 'descripcion as descripcion_coche')
                ->where('estatus', 'ABIERTO')
                ->orderBy('id', 'desc')
                ->first();

            $c->barcode = ($renta['barcode'] == null ? '': $renta['barcode']);
            $c->folio = ($renta['id'] == null ? '': $renta['id']);
            $c->descripcion_coche = ($renta['descripcion_coche'] == null ? '': $renta['descripcion_coche']);
        }

        return view('livewire.rentas.componente', [
            'cajones' => $cajones
        ]);
    }

    protected $listeners = [
      'RegistrarEntrada' => 'RegistrarEntrada',
      'doCheckOut' => 'doCheckOut',
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
            $this->emit('msg-ok', 'No existe el código de barras');
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
}
