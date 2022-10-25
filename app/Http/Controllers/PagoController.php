<?php

namespace App\Http\Controllers;

use App\Events\PagoEvent;
use App\Models\Cliente;
use App\Models\DetallePago;
use App\Models\DetallePedido;
use App\Models\Pago;
use App\Models\PagoPedido;
use App\Models\Pedido;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use DataTables;

class PagoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pagosobservados_cantidad = Pago::where('user_id', Auth::user()->id)//PAGOS OBSERVADOS
                ->where('estado', '1')
                ->where('condicion', 'OBSERVADO')
                ->count();
        
        $superasesor = User::where('rol', 'Super asesor')->count();

        return view('pagos.index', compact('pagosobservados_cantidad', 'superasesor'));
    }

    public function indextabla(Request $request)
    {
        $pagos=null;
        if(Auth::user()->rol == "Encargado"){
            $pagos = Pago::join('users as u', 'pagos.user_id', 'u.id')
                ->join('clientes as c', 'pagos.cliente_id', 'c.id')
                ->join('detalle_pagos as dpa', 'pagos.id', 'dpa.pago_id') 
                ->join('pago_pedidos as pp', 'pagos.id', 'pp.pago_id')
                ->rightjoin('pedidos as p', 'pp.pedido_id', 'p.id')
                ->rightjoin('detalle_pedidos as dpe', 'p.id', 'dpe.pedido_id')
                ->select('pagos.id as id',
                        'u.identificador as users',
                        'c.celular',
                        'pagos.observacion',                        
                        'pagos.total_cobro',
                        DB::raw('sum(dpe.total) as total_deuda'),
                        DB::raw('sum(pp.abono) as total_pago'),
                        'pagos.condicion',
                        DB::raw('DATE_FORMAT(pagos.created_at, "%d/%m/%Y") as fecha')
                        )
                ->where('u.supervisor', Auth::user()->id)
                //->where('pagos.estado', '1')
                ->where('dpe.estado', '1')
                ->where('dpa.estado', '1')
                ->groupBy('pagos.id',
                        'dpe.codigo',
                        'u.identificador',
                        'c.celular',
                        'pagos.observacion','dpe.total',
                        'pagos.total_cobro',
                        'pagos.condicion',
                        'pagos.created_at'
                        )
                ->orderBy('pagos.created_at', 'DESC')
                ->get();
        }else{
            $pagos = Pago::join('users as u', 'pagos.user_id', 'u.id')
                ->join('clientes as c', 'pagos.cliente_id', 'c.id')
                ->join('detalle_pagos as dpa', 'pagos.id', 'dpa.pago_id') 
                ->join('pago_pedidos as pp', 'pagos.id', 'pp.pago_id')
                ->rightjoin('pedidos as p', 'pp.pedido_id', 'p.id')
                ->rightjoin('detalle_pedidos as dpe', 'p.id', 'dpe.pedido_id')
                ->select('pagos.id as id',
                        'u.identificador as users',
                        'c.celular',
                        'pagos.observacion',                        
                        'pagos.total_cobro',
                        DB::raw('sum(dpe.total) as total_deuda'),
                        DB::raw('sum(pp.abono) as total_pago'),
                        'pagos.condicion',
                        DB::raw('DATE_FORMAT(pagos.created_at, "%d/%m/%Y") as fecha')
                        )
                //->where('pagos.estado', '1')
                ->where('p.estado', '1')
                ->where('dpa.estado', '1')                
                ->groupBy('pagos.id',
                        'u.identificador',
                        'c.celular',
                        'pagos.observacion',
                        'pagos.total_cobro',
                        'pagos.condicion',
                        'pagos.created_at'
                        )
                //->orderBy('pagos.created_at', 'DESC')
                ->get();                
        }
        $pagoList = [];
        $cont = 0;
        foreach ($pagos as $pago){
            $pago_pedidos = PagoPedido::
                select('pedido_id as id')
                ->where('pago_pedidos.pago_id', $pago->id)
                ->get();

            $pedidos = Pedido::select('codigo as codigos')
                ->whereIn('id', $pago_pedidos)
                ->get();

                $pagoList[$cont] = array(
                'id' => $pago->id,
                'codigos' => $pedidos,
                'users' => $pago->users,
                'celular' => $pago->celular,
                'observacion' => $pago->observacion,
                'total_cobro' => $pago->total_cobro,
                'total_pago' => $pago->total_pago,
                'total_deuda' => $pago->total_deuda,
                'fecha' => $pago->fecha,
                'condicion' => $pago->condicion
            );

            $cont++;
        }
        return Datatables::of($pagoList)
                    ->addIndexColumn()
                    ->addColumn('action', function($pago){     
                        $btn='';
                        if(Auth::user()->rol == "Administrador"){
                            $btn=$btn.'<a href="'.route('pagos.show', $pago['id']).'" class="btn btn-info btn-sm">Ver</a>';
                            $btn=$btn.'<a href="'.route('pagos.edit', $pago['id']).'" class="btn btn-warning btn-sm">Editar</a>';
                            $btn = $btn.'<a href="" data-target="#modal-delete" data-toggle="modal" data-delete="'.$pago['id'].'"><button class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i> Eliminar</button></a>';
                        }else if(Auth::user()->rol == "Encargado"){
                            $btn=$btn.'<a href="'.route('pagos.show', $pago['id']).'" class="btn btn-info btn-sm">Ver</a>';
                            $btn=$btn.'<a href="'.route('pagos.edit', $pago['id']).'" class="btn btn-warning btn-sm">Editar</a>';
                            $btn = $btn.'<a href="" data-target="#modal-delete" data-toggle="modal" data-delete="'.$pago['id'].'"><button class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i> Eliminar</button></a>';
                        }else if(Auth::user()->rol == "Asesor"){
                            $btn=$btn.'<a href="'.route('pagos.show', $pago['id']).'" class="btn btn-info btn-sm">Ver</a>';
                            $btn=$btn.'<a href="'.route('pagos.edit', $pago['id']).'" class="btn btn-warning btn-sm">Editar</a>';
                            $btn = $btn.'<a href="" data-target="#modal-delete" data-toggle="modal" data-delete="'.$pago['id'].'"><button class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i> Eliminar</button></a>';
                        }else{
                            $btn=$btn.'<a href="'.route('pagos.show', $pago['id']).'" class="btn btn-info btn-sm">Ver</a>';
                            $btn=$btn.'<a href="'.route('pagos.edit', $pago['id']).'" class="btn btn-warning btn-sm">Editar</a>';
                            $btn = $btn.'<a href="" data-target="#modal-delete" data-toggle="modal" data-delete="'.$pago['id'].'"><button class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i> Eliminar</button></a>';
                        }
                        
                        return $btn;
                    })
                    ->rawColumns(['action'])
                    ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $clientes=null;
        if(Auth::user()->rol == "Administrador"){
            $clientes = Cliente::where('estado', '1')
            //->where('user_id', Auth::user()->id)
            ->where('tipo', '1')
            ->get();
        }
        else{
            $clientes = Cliente::where('estado', '1')
                            ->where('user_id', Auth::user()->id)
                            ->where('tipo', '1')
                            ->get();

        }
        
        $pedidos = Pedido::join('detalle_pedidos as dp', 'pedidos.id', 'dp.pedido_id')
                            ->select('pedidos.id', 
                                    'dp.codigo')
                            ->where('pedidos.estado', '1')
                            ->where('pedidos.pago', '0')
                            ->get();
        $bancos = [
            "BCP" => 'BCP',
            "BBVA" => 'BBVA',
            "INTERBANK" => 'INTERBANK',
            "SCOTIABANK" => 'SCOTIABANK',
            "PICHINCHA" => 'PICHINCHA',
            "YAPE" => 'YAPE',
            "PLIN" => 'PLIN',
            "TUNKI" => 'TUNKI',
            "SALDO ANTERIOR" => 'SALDO ANTERIOR'
        ];
        return view('pagos.create', compact('clientes', 'pedidos', 'bancos'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function pedidoscliente(Request $request)
    {
        if (!$request->cliente_id) {
            $html = '<option value="">' . trans('---- SELECCIONE ----') . '</option>';
        } else {
            $html = '<option value="">' . trans('---- SELECCIONE ----') . '</option>';
            $pedidos = Pedido::join('detalle_pedidos as dp', 'pedidos.id', 'dp.pedido_id')
                ->select('pedidos.id', 
                        'dp.codigo',
                        'dp.total',
                        'dp.saldo')
                ->where('pedidos.cliente_id', $request->cliente_id)
                /* ->where('pedidos.pago', '0') */
                ->where('pedidos.pagado', '<>', '2')
                ->where('pedidos.estado', '1')
                ->where('dp.estado', '1')                
                ->get();
            
            foreach ($pedidos as $pedido) {
                $saldo_mostrar = $pedido->saldo;
                $saldo_mostrar=str_replace(',','.',$saldo_mostrar);
                $html .= '<option value="' . $pedido->id . '_' . $pedido->codigo . '_' . $pedido->total . '_' . $pedido->saldo . '">Código: ' . $pedido->codigo . ' - Total: S/' . $pedido->total . ' - Saldo: S/' . $pedido->saldo . '</option>';
            }
        }
        return response()->json(['html' => $html]);
    }

    public function pedidosclientetabla(Request $request)
    {        
        $pedidos=null;
        if (!$request->cliente_id) {            
        } else {
            $idrequest=explode("_",$request->cliente_id);           
            $pedidos = Pedido::join('detalle_pedidos as dp', 'pedidos.id', 'dp.pedido_id')
                ->select('pedidos.id', 
                        'dp.codigo',
                        'dp.total',
                        'dp.saldo')
                ->where('pedidos.cliente_id', $idrequest)
                ->where('pedidos.pagado', '<>', '2')
                ->where('pedidos.estado', '1')
                ->where('dp.estado', '1')
                ->where('dp.total','>', '0')
                ->where('dp.saldo','>', '0')
                ->get();
            
            return Datatables::of($pedidos)
                    ->addIndexColumn()                  
                    ->make(true);
        }       
    }

    public function store(Request $request)
    {
        //ESTADOS PARA CAMPO "PAGADO" EN PEDIDOS
        //0: DEBE
        //1: ADELANTO
        //2: PAGADO

        $request->validate([
            'imagen' => 'required',
        ]);

        try {
            DB::beginTransaction();
            //MONTO A PAGAR - TOTAL DE LOS PEDIDOS
            $deuda_total = $request->total_pedido_pagar;
            $deuda_total=str_replace(',','',$deuda_total);
            //MONTO TOTAL PAGADO - SUMA DE PAGOS
            $pagado = $request->total_pago_pagar;
            $pagado=str_replace(',','',$pagado);

            $pago = Pago::create([                
                'user_id' => $request->user_id,
                'cliente_id' => $request->cliente_id,
                'total_cobro' => $deuda_total,//total_pedido_pagar
                'total_pagado' => $pagado,//total_pago_pagar
                'condicion' => "ADELANTO",
                /* 'saldo' => '1',
                'diferencia' => '1', */
                'estado' => '1'
            ]);

            // ALMACENANDO PAGO-PEDIDOS
            $pedido_id = $request->pedido_id;
            $contPe = 0;
            $monto_pagado_a_favor = $pagado;

            while ($contPe < count((array)$pedido_id)) {

                $pagoPedido = PagoPedido::create([
                        'pago_id' => $pago->id,
                        'pedido_id' => $pedido_id[$contPe],
                        'estado' => '1'
                    ]);

                //INDICADOR DE PAGOS Y ESTADO DE PAGADO EN EL PEDIDO
                $pedido = Pedido::find($pagoPedido->pedido_id);

                $pedido->update([
                    'pago' => '1'
                ]);

                $detalle_pedido = DetallePedido::where('pedido_id', $pedido->id)->first();
                if($monto_pagado_a_favor >= $detalle_pedido->total){
                    $pedido->update([
                        'pagado' => '2'//PAGADO
                    ]);
                    $detalle_pedido->update([
                        'saldo' => '0'
                    ]);
                    $pagoPedido->update([
                        'pagado' => '2'//PAGADO
                    ]);
                    $monto_pagado_a_favor = ($monto_pagado_a_favor)*1 - ($detalle_pedido->total)*1;
                }else{
                    $pedido->update([
                        'pagado' => '1'//ADELANTO
                    ]);
                    $pagoPedido->update([
                        'pagado' => '1'//ADELANTO
                    ]);
                    $detalle_pedido->update([
                        'saldo' => ($detalle_pedido->total)*1 - ($monto_pagado_a_favor)*1                        
                    ]);
                    if($detalle_pedido->total - $monto_pagado_a_favor <= 3){//SI EL MONTO ES IGUAL O MENOR A 3, SE PERDONA LA DEUDA
                        $pedido->update([
                            'pagado' => '2'//PAGADO
                        ]);
                        $pagoPedido->update([
                            'pagado' => '2'//PAGADO
                        ]);
                    }
                }
                $contPe++;
            }

            // ALMACENANDO DETALLE DE PAGOS
            $monto = $request->monto;            
            $banco = $request->banco;
            $fecha = $request->fecha;
            
            $files = $request->file('imagen');
            $destinationPath = base_path('public/storage/pagos/');

            $cont = 0;
            $fileList = [];

            foreach ($files as $file){
                $file_name = Carbon::now()->second.$file->getClientOriginalName(); //Get file original name
                $fileList[$cont] = array(
                    'file_name' => $file_name,
                );
                $file->move($destinationPath , $file_name);

                $cont++;
            }

            $contPa = 0;

            while ($contPa < count((array)$monto)) {
                if(isset($fileList[$contPa]['file_name'])){ 
                    DetallePago::create([
                        'pago_id' => $pago->id,
                        'monto' => $monto[$contPa],
                        'banco' => $banco[$contPa],
                        'fecha' => $fecha[$contPa],
                        'imagen' => $fileList[$contPa]['file_name'],
                        'estado' => '1'
                    ]);  
                }else{
                    DetallePago::create([
                        'pago_id' => $pago->id,
                        'monto' => $monto[$contPa],
                        'banco' => $banco[$contPa],
                        'fecha' => $fecha[$contPa],
                        'imagen' => 'logo_facturas.png',
                        'estado' => '1'
                ]);
                }

                $contPa++;
            }

            //ACTUALIZAR PEDIDOS A PAGADOS
            //if($deuda_total - $pagado <= 3){
                $pago->update([
                    'condicion' => 'PAGO',
                    'notificacion' => 'Nuevo pago registrado',
                    'diferencia' => $deuda_total - $pagado//'diferencia' => '0'//ACTUALIZAR LA DEUDA EN EL PAGO
                ]);

                //ACTUALIZAR QUE CLIENTE NO DEBE
                $cliente = Cliente::find($request->cliente_id);

                $pedido_deuda = Pedido::where('cliente_id', $request->cliente_id)//CONTAR LA CANTIDAD DE PEDIDOS QUE DEBE
                                        ->where('pagado', '0')
                                        ->count();
                if($pedido_deuda == 0){//SINO DEBE NINGUN PEDIDO EL ESTADO DEL CLIENTE PASA A NO DEUDA(CERO)
                    $cliente->update([
                        'deuda' => '0',
                    ]);
                }                

                event(new PagoEvent($pago));
            /* }
            else
            {
                //ACTUALIZAR LA DEUDA EN EL PAGO
                $pago->update([
                    'condicion' => 'ADELANTO',
                    'diferencia' => $deuda_total - $pagado
                ]);
            } */
            
            //ACTUALIZAR SALDO A FAVOR
            $cliente = Cliente::find($request->cliente_id);

            $saldo = $request->saldo;
            $saldo=str_replace(',','',$saldo);

            if ($request->saldo != null && $request->saldo != 0 ){
                $cliente->update([
                    'saldo' => $saldo,
                ]);
                $pago->update([
                    'saldo' => $saldo,
                ]);
            }else{
                $cliente->update([
                    'saldo' => '0',
                ]);
                $pago->update([
                    'saldo' => '0',
                ]);
            }

            DB::commit();
        } catch (\Throwable $th) {
            throw $th;
            /*DB::rollback();
            dd($th);*/
        }        

        return redirect()->route('pagos.index')->with('info', 'registrado');
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Pago $pago)
    
    {
        $pagos = Pago::join('users as u', 'pagos.user_id', 'u.id')
            ->join('clientes as c', 'pagos.cliente_id', 'c.id')
            ->select('pagos.id', 
                    'u.name as users',
                    'c.celular', //cliente
                    'c.nombre', //cliente
                    'pagos.observacion', 
                    'pagos.condicion', 
                    'pagos.estado', 
                    'pagos.created_at as fecha')
            ->where('pagos.id', $pago->id)
            ->groupBy('pagos.id', 
                    'u.name',
                    'c.celular',
                    'c.nombre',
                    'pagos.observacion', 
                    'pagos.condicion', 
                    'pagos.estado', 
                    'pagos.created_at'
                    )
            ->first();
        
        $pagoPedidos = PagoPedido::join('pedidos as p', 'pago_pedidos.pedido_id', 'p.id')
            ->join('detalle_pedidos as dp', 'p.id', 'dp.pedido_id')
            ->select('pago_pedidos.id', 
                    'dp.codigo',
                    'p.id as pedidos',
                    'p.condicion',
                    'dp.total',
                    'pago_pedidos.pagado',
                    'pago_pedidos.abono'
                    )
            ->where('pago_pedidos.estado', '1')
            ->where('p.estado', '1')
            ->where('dp.estado', '1')
            ->where('pago_pedidos.pago_id', $pago->id)
            ->get();
        
        $detallePagos = DetallePago::
            select('id', 
                    'monto', 
                    'banco', 
                    'imagen',
                    'fecha',
                    'titular',
                    'cuenta',
                    'fecha_deposito',
                    'observacion')
            ->where('estado', '1')
            ->where('pago_id', $pago->id)
            ->get();
        //DB::raw('sum(detalle_pagos.monto) as total')
        return view('pagos.show', compact('pagos', 'pagoPedidos', 'detallePagos'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Pago $pago)
    
    {   
        $pagos = Pago::join('users as u', 'pagos.user_id', 'u.id')
            ->join('clientes as c', 'pagos.cliente_id', 'c.id')
            ->select('pagos.id', 
                    'u.name as users',
                    'c.celular', //cliente
                    'c.nombre', //cliente
                    'c.saldo', //cliente
                    'pagos.observacion', 
                    'pagos.condicion', 
                    'pagos.estado', 
                    'pagos.created_at as fecha')
            ->where('pagos.id', $pago->id)
            ->groupBy('pagos.id', 
                    'u.name',
                    'c.celular',
                    'c.nombre',
                    'c.saldo',
                    'pagos.observacion', 
                    'pagos.condicion', 
                    'pagos.estado', 
                    'pagos.created_at')
            ->first();
        $clientes = Cliente::where('estado', '1')
                            ->where('user_id', Auth::user()->id)
                            ->get();
        $pedidos = Pedido::join('detalle_pedidos as dp', 'pedidos.id', 'dp.pedido_id')
                            ->select('pedidos.id', 
                                    'dp.codigo',
                                    'dp.total')
                            ->where('pedidos.cliente_id', $pago->cliente_id)
                            ->where('pedidos.pago', '0')
                            ->where('pedidos.estado', '1')
                            ->where('dp.estado', '1')                
                            ->get();
        $bancos = [
            "BCP" => 'BCP',
            "BBVA" => 'BBVA',
            "INTERBANK" => 'INTERBANK',
            "SCOTIABANK" => 'SCOTIABANK',
            "PICHINCHA" => 'PICHINCHA',
            "YAPE" => 'YAPE',
            "PLIN" => 'PLIN',
            "TUNKI" => 'TUNKI'
        ];

        $listaPedidos = PagoPedido::join('pedidos as p', 'pago_pedidos.pedido_id', 'p.id')            
            ->join('detalle_pedidos as dp', 'p.id', 'dp.pedido_id')
            ->select('pago_pedidos.id', 
                    'dp.codigo',
                    'p.id as pedidos',
                    'p.condicion',
                    'dp.total',
                    'pago_pedidos.pagado'
                    )
            ->where('pago_pedidos.estado', '1')
            ->where('p.estado', '1')
            ->where('dp.estado', '1')
            ->where('pago_pedidos.pago_id', $pago->id)
            ->get();

        $listaPagos = DetallePago::
            select('id', 
                    'monto', 
                    'banco', 
                    'imagen',
                    'fecha',
                    'observacion')
            ->where('estado', '1')
            ->where('pago_id', $pago->id)
            ->get();

        return view('pagos.edit', compact('pago', 'pagos', 'clientes', 'pedidos', 'bancos', 'listaPedidos', 'listaPagos'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Pago $pago)
    
    {
        $request->validate([
            'imagen' => 'required',
        ]);

        try {
            DB::beginTransaction();

            // ALMACENANDO DETALLE DE PAGOS
            $monto = $request->monto;            
            $banco = $request->banco;
            $fecha = $request->fecha;
            $files = $request->file('imagen');
            $destinationPath = base_path('public/storage/pagos/');

            $cont = 0;
            $fileList = [];

            foreach ($files as $file){
                $file_name = $file->getClientOriginalName(); //Get file original name
                $fileList[$cont] = array(
                    'file_name' => $file_name,
                );
                $file->move($destinationPath , $file_name);

                $cont++;
            }

            $contPa = 0;
            
            while ($contPa < count((array)$monto)) {

                DetallePago::create([
                    'pago_id' => $pago->id,
                    'monto' => $monto[$contPa],
                    'banco' => $banco[$contPa],
                    'fecha' => $fecha[$contPa],
                    'imagen' => $fileList[$contPa]['file_name'],
                    'estado' => '1'
                ]);    

                $contPa++;
            }     
            $deuda_total = $request->total_pedidos;
                $total_pago_pagar = $request->total_pago_pagar;
                $total_pagos = $request->total_pagos;
            $pagado = ($total_pago_pagar*1) + ($total_pagos*1);

            if($deuda_total - $pagado <= 3){
                $pago->update([
                    'condicion' => 'PAGO',
                    'total_pagado' => $pagado,//total_pago_pagar
                    'notificacion' => 'Nuevo pago registrado',                    
                    'diferencia' => '0'//ACTUALIZAR LA DEUDA EN EL PAGO
                ]);

                //ACTUALIZAR QUE CLIENTE NO DEBE
                $cliente = Cliente::find($pago->cliente_id);                
                $cliente->update([
                        'deuda' => '0',
                    ]);

                event(new PagoEvent($pago));
            }else{
                $pago->update([
                    'condicion' => 'ADELANTO',//PENDIENTE DE PAGO
                    'total_pagado' => $pagado,                    
                    'diferencia' => $deuda_total - $pagado//ACTUALIZAR LA DEUDA EN EL PAGO
                ]);
            }

            //ACTUALIZAR SALDO A FAVOR
            $cliente = Cliente::find($pago->cliente_id);

            $saldo = $request->saldo;
            $saldo=str_replace(',','',$saldo);

            if ($request->saldo != null && $request->saldo != 0 ){
                $cliente->update([
                    'saldo' => $saldo,
                ]);
                $pago->update([
                    'saldo' => $saldo,
                ]);
            }else{
                $cliente->update([
                    'saldo' => '0',
                ]);
                $pago->update([
                    'saldo' => '0',
                ]);
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            //throw $th;
        }
        if (Auth::user()->rol == "Asesor"){
            return redirect()->route('pagos.mispagos')->with('info', 'actualizado');
        }else
            return redirect()->route('pagos.index')->with('info', 'actualizado');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function eliminarPedido($id, Pago $pago)
    {
        $pagoPedido = PagoPedido::find($id);
        $pagoPedido->update([
            'estado' => '0'
        ]);
        return redirect()->route('pagos.edit', compact('pago'))->with('info', 'Eliminado');
    }

    public function eliminarPago($id, Pago $pago)
    {
        $detallePago = DetallePago::find($id);
        $detallePago->update([
            'estado' => '0'
        ]);
        return redirect()->route('pagos.edit', compact('pago'))->with('info', 'Eliminado');
    }
    
    public function destroy($pago_id)    
    {   
        $pago = Pago::where('id', $pago_id)->first();
        $detallePago = DetallePago::where('pago_id', $pago->id)->get();
        $pagoPedido = PagoPedido::where('pago_id', $pago->id)->get();

        $pago->update([            
            'estado' => '0'
        ]);

        foreach ($detallePago as $detalleP) {
            DetallePago::where('id', $detalleP->id)
            ->update([
                'estado' => '0'
            ]);
        }

        foreach ($pagoPedido as $pagoP) {
            PagoPedido::where('id', $pagoP->id)
            ->update([
                'estado' => '0'
            ]);

            $pedido = Pedido::find($pagoP->pedido_id);

            $pedido->update([
                'pago' => '0'
            ]);
        }

        return redirect()->route('pagos.index')->with('info', 'eliminado');        
    }

    public function destroyid(Request $request)
    {
        //modificar primero
        if (!$request->hiddenID) {
            $html='';
        } else {
            //$pago_id=;
            $html='';
            $pago = Pago::where('id', $request->hiddenID)
                        ->where('estado', '1')
                        ->first();//solo 1
            $detallePago = DetallePago::where('pago_id', $pago->id)
                            ->where('estado', '1')
                            ->get();//mas de 1
            $pagoPedido = PagoPedido::where('pago_id', $pago->id)
                            ->where('estado', '1')
                            ->get();//mas de 1 saco a pedidos
            //de aqui saldran los pedidos

            $pago->update([            
                'estado' => '0'
                //,'condicion'=>'por considerar'//analizar
                //,'notificacion'=>''//analizar
                //,'saldo'=>''//analizar
                //,'diferencia'=>''//analizar
                //,'updated_at'=>''//analizar
            ]);

            foreach ($detallePago as $detalleP) {
                DetallePago::where('id', $detalleP->id)
                ->update([
                    'estado' => '0'
                    //,'updated_at'=>''//analizar
                ]);
            }

            /*$abonoTotalPedidos=PagoPedido::sum('abono')
                                    ->where('pago_id', $pagoP->id)
                                    ->where('estado', '1')
                                    ->groupBy('pedido_id,')
                                    ->get();*/
            
            $abonoTotalPedidos = //Pedido::join('clientes as c', 'pedidos.cliente_id', 'c.id')
            PagoPedido::select(
                    'pago_pedidos.pago_id',
                    'pago_pedidos.pedido_id',
                    DB::raw('sum(pago_pedidos.abono) as abono')
                )
                ->where('pago_id', $pago->id)
                ->where('estado', '1')
                ->groupBy(
                    'pago_pedidos.pago_id',
                    'pago_pedidos.pedido_id'
                )
                ->get();

            /*
            pago id 1-> pedido id 11  pagado 1  abono 10  estado 1
            */
            
            foreach ($pagoPedido as $pagoP) {
                //por cada pedido
                ///cuanto revertir a saldo
                //Categoria::sum('cantidad')->groupBy('categoria')->get();

                PagoPedido::where('id', $pagoP->id)
                ->where('estado', '1')
                ->where('pagado', '<>', '0')
                ->update([
                    'estado' => '0',
                    'pagado' => '0'
                    //'abono'=>0//es cuanto voy a revertir
                ]);
                //cambiar un pagopedido where pago id para el update, mas rapido
    
                $pedido = Pedido::find($pagoP->pedido_id);//muchos recorridos para actualizar pedido
    
                $pedido->update([
                    'pago' => '0',
                    'pagado' => '0'
                ]);  
            }

            foreach ($abonoTotalPedidos as $abonoP) {
                /* revisar correctamente esta data */
                $detallePedido=DetallePedido::where('id', $abonoP->pedido_id)->first();;//::find($abonoP->pedido_id);
                            
                //decidir si cambiar el estado de detalle pedido o hacer alguna modificacion o reversion
                $detallePedido->update([
                    'saldo' => $detallePedido->saldo + $abonoP->abono
                ]);
                /* revisar correctamente esta data */
                
            }

            $html=$abonoTotalPedidos;
        }
        return response()->json(['html' => $html]);
    }

    public function MisPagos()
    {
        $dateMin = Carbon::now()->subDays(4)->format('d/m/Y');
        $dateMax = Carbon::now()->format('d/m/Y');

        $pagos = Pago::join('users as u', 'pagos.user_id', 'u.id')
            ->join('clientes as c', 'pagos.cliente_id', 'c.id')
            ->join('detalle_pagos as dpa', 'pagos.id', 'dpa.pago_id')
            ->join('pago_pedidos as pp', 'pagos.id', 'pp.pago_id')
            ->join('pedidos as p', 'pp.pedido_id', 'p.id')
            ->join('detalle_pedidos as dpe', 'p.id', 'dpe.pedido_id')
            ->select('pagos.id', 
                    'dpe.codigo as codigos', 
                    'u.name as users',
                    'c.celular',
                    'pagos.observacion', 
                    'dpe.total as total_deuda',
                    DB::raw('sum(dpa.monto) as total_pago'), 
                    'pagos.condicion',                   
                    /* 'pagos.created_at as fecha' */
                    DB::raw('DATE_FORMAT(pagos.created_at, "%d/%m/%Y") as fecha')
                    )
            ->where('pagos.estado', '1')
            ->where('dpe.estado', '1')
            ->where('dpa.estado', '1')
            ->where('u.id', Auth::user()->id)
            ->groupBy('pagos.id', 
                    'dpe.codigo', 
                    'u.name',
                    'c.celular',
                    'pagos.observacion', 'dpe.total',
                    'pagos.total_cobro',
                    'pagos.condicion', 
                    'pagos.created_at')
            ->get();
        
        $pagosobservados_cantidad = Pago::where('user_id', Auth::user()->id)//PAGOS OBSERVADOS
            ->where('estado', '1')
            ->where('condicion', 'OBSERVADO')
            ->count();
        
        $superasesor = User::where('rol', 'Super asesor')->count();

        return view('pagos.mispagos', compact('pagos', 'pagosobservados_cantidad', 'superasesor', 'dateMin', 'dateMax'));
    }

    public function PagosIncompletos()
    {
        $pagos = Pago::join('users as u', 'pagos.user_id', 'u.id')
            ->join('detalle_pagos as dpa', 'pagos.id', 'dpa.pago_id')
            ->join('pago_pedidos as pp', 'pagos.id', 'pp.pago_id')
            ->join('pedidos as p', 'pp.pedido_id', 'p.id')
            ->join('detalle_pedidos as dpe', 'p.id', 'dpe.pedido_id')
            ->select('pagos.id', 
                    'dpe.codigo as codigos', 
                    'u.name as users', 
                    'pagos.observacion', 
                    'dpe.total as total_deuda',
                    DB::raw('sum(dpa.monto) as total_pago'), 
                    'pagos.condicion',                   
                    'pagos.created_at as fecha'
                    )
            ->where('pagos.estado', '1')
            ->where('dpe.estado', '1')
            ->where('dpa.estado', '1')
            ->where('u.id', Auth::user()->id)
            ->where('pagos.condicion', 'ADELANTO')
            ->groupBy('pagos.id', 
                    'dpe.codigo', 
                    'u.name',
                    'pagos.observacion', 'dpe.total',
                    'pagos.total_cobro',
                    'pagos.condicion', 
                    'pagos.created_at')
            ->get();

        $superasesor = User::where('rol', 'Super asesor')->count();

        return view('pagos.pagosincompletos', compact('pagos', 'superasesor'));
    }

    //funcion pagos observados * 
    public function PagosObservados()
    {
        $pagos = Pago::join('users as u', 'pagos.user_id', 'u.id')
            ->join('detalle_pagos as dpa', 'pagos.id', 'dpa.pago_id')
            ->join('pago_pedidos as pp', 'pagos.id', 'pp.pago_id')
            ->join('pedidos as p', 'pp.pedido_id', 'p.id')
            ->join('detalle_pedidos as dpe', 'p.id', 'dpe.pedido_id')
            ->select('pagos.id', 
                    'dpe.codigo as codigos', 
                    'u.name as users', 
                    //'pagos.observacion', cambio 19/10/2022 08.55am anterior * zubieta - a solicitud de ruben
                    'dpa.observacion', //cambio 19/10/2022 08.55am nuevo * zubieta - a solicitud de ruben
                    'dpe.total as total_deuda',
                    DB::raw('sum(dpa.monto) as total_pago'), 
                    'pagos.condicion',                   
                    'pagos.created_at as fecha'
                    )
            ->where('pagos.estado', '1')
            ->where('dpe.estado', '1')
            ->where('dpa.estado', '1')
            ->where('u.id', Auth::user()->id)
            ->where('pagos.condicion', 'OBSERVADO')
            ->groupBy('pagos.id', 
                    'dpe.codigo', 
                    'u.name',
                    //'pagos.observacion', cambio 19/10/2022 08.55am anterior * zubieta - a solicitud de ruben
                    'dpa.observacion', //cambio 19/10/2022 08.55am nuevo * zubieta - a solicitud de ruben
                    'dpe.total',
                    'pagos.total_cobro',
                    'pagos.condicion', 
                    'pagos.created_at')
            ->get();
        
        $pagosobservados_cantidad = Pago::where('user_id', Auth::user()->id)//PAGOS OBSERVADOS
            ->where('estado', '1')
            ->where('condicion', 'OBSERVADO')
            ->count();

        $superasesor = User::where('rol', 'Super asesor')->count();

        return view('pagos.pagosobservados', compact('pagos', 'pagosobservados_cantidad', 'superasesor'));
    }

    public function viewAlmacen()
    {
        return view('ingresos.reportes.index');        
    }

    public function PorRevisar()
    {
        $pagos = Pago::join('users as u', 'pagos.user_id', 'u.id')
            ->join('detalle_pagos as dpa', 'pagos.id', 'dpa.pago_id')
            ->join('pago_pedidos as pp', 'pagos.id', 'pp.pago_id')
            ->join('pedidos as p', 'pp.pedido_id', 'p.id')
            ->join('detalle_pedidos as dpe', 'p.id', 'dpe.pedido_id')
            ->select('pagos.id', 
                    'dpe.codigo as codigos', 
                    'u.name as users', 
                    'pagos.observacion',
                    //'pagos.saldo',
                    'dpe.total as total_deuda', 
                    DB::raw('sum(dpa.monto) as total_pago'), 
                    'pagos.condicion',                   
                    'pagos.created_at as fecha'
                    )
            ->where('pagos.estado', '1')
            ->where('dpe.estado', '1')
            ->where('dpa.estado', '1')
            ->whereIn('pagos.condicion', ['PAGO','OBSERVADO'])
            ->groupBy('pagos.id', 
                    'dpe.codigo', 
                    'u.name', 
                    'pagos.observacion', 
                    //'pagos.saldo',
                    'dpe.total',
                    'pagos.total_cobro',
                    'pagos.condicion', 
                    'pagos.created_at')
            ->get();
        
        $superasesor = User::where('rol', 'Super asesor')->count();

        return view('pagos.porrevisar', compact('pagos', 'superasesor'));
    }

    public function Aprobados()
    {
        $pagos = Pago::join('users as u', 'pagos.user_id', 'u.id')
            ->join('detalle_pagos as dpa', 'pagos.id', 'dpa.pago_id')
            ->join('pago_pedidos as pp', 'pagos.id', 'pp.pago_id')
            ->join('pedidos as p', 'pp.pedido_id', 'p.id')
            ->join('detalle_pedidos as dpe', 'p.id', 'dpe.pedido_id')
            ->select('pagos.id', 
                    'dpe.codigo as codigos', 
                    'u.name as users', 
                    'pagos.observacion', 
                    //'pagos.saldo',
                    'dpe.total as total_deuda',
                    DB::raw('sum(dpa.monto) as total_pago'), 
                    'pagos.condicion',                   
                    'pagos.created_at as fecha'
                    )
            ->where('pagos.estado', '1')
            ->where('dpe.estado', '1')
            ->where('dpa.estado', '1')
            ->where('pagos.condicion', 'ABONADO')
            ->groupBy('pagos.id', 
                    'dpe.codigo', 
                    'u.name', 
                    'pagos.observacion', 
                    //'pagos.saldo',
                    'dpe.total',
                    'pagos.condicion', 
                    'pagos.created_at')
            ->get();

        $superasesor = User::where('rol', 'Super asesor')->count();

        return view('pagos.aprobados', compact('pagos', 'superasesor'));
    }

    public function Revisar(Pago $pago)
    
    {
        $condiciones = [
            "PAGO" => 'PAGO',
            "OBSERVADO" => 'OBSERVADO',
            "ABONADO" => 'ABONADO',
            "ABONADO_PARCIAL" => 'ABONADO_PARCIAL'
        ];

        $cuentas = [
            "BCP" => 'BCP',
            "BBVA" => 'BBVA',
            "YAPE" => 'YAPE',
            "INTERBANK" => 'INTERBANK'
        ];

        $titulares = [
            "EPIFANIO HUAMAN SOLANO" => 'EPIFANIO HUAMAN SOLANO',
            "NIKSER DENIS ORE RIVEROS" => 'NIKSER DENIS ORE RIVEROS'
        ];

        $pagos = Pago::join('users as u', 'pagos.user_id', 'u.id')
            ->join('clientes as c', 'pagos.cliente_id', 'c.id')
            ->select('pagos.id', 
                    'u.name as users',
                    'c.celular', //cliente
                    'c.nombre', //cliente
                    'pagos.observacion', 
                    //'pagos.saldo',
                    'pagos.condicion', 
                    'pagos.estado', 
                    'pagos.created_at as fecha')
            ->where('pagos.id', $pago->id)
            ->groupBy('pagos.id', 
                    'u.name',
                    'c.celular',
                    'c.nombre',
                    'pagos.observacion', 
                    //'pagos.saldo',
                    'pagos.condicion', 
                    'pagos.estado', 
                    'pagos.created_at')
            ->first();
        
        $pagoPedidos = PagoPedido::join('pedidos as p', 'pago_pedidos.pedido_id', 'p.id')
            ->join('detalle_pedidos as dp', 'p.id', 'dp.pedido_id')
            ->select('pago_pedidos.id', 
                    /* 'c.celular', //cliente
                    'c.nombre', //cliente */
                    'dp.codigo',
                    'p.id as pedidos',
                    'p.condicion',
                    'dp.total',
                    'pago_pedidos.pagado',
                    'pago_pedidos.abono'
                    )
            ->where('pago_pedidos.estado', '1')
            ->where('p.estado', '1')
            ->where('dp.estado', '1')
            ->where('pago_pedidos.pago_id', $pago->id)
            ->get();
        
        $detallePagos = DetallePago::
            select('id', 
                    'monto', 
                    'banco', 
                    'imagen',
                    'fecha',
                    'titular',
                    'cuenta',
                    'fecha_deposito',
                    'observacion')
            ->where('estado', '1')
            ->where('pago_id', $pago->id)
            ->get();
        //DB::raw('sum(detalle_pagos.monto) as total')

        return view('pagos.revisar', compact('pago', 'condiciones', 'cuentas', 'titulares', 'pagos', 'pagoPedidos', 'detallePagos'));
    }    

    public function updateRevisar(Request $request, Pago $pago)
    
    {   
        $fecha_aprobacion = Carbon::now()->format('Y-m-d');

        try {
            DB::beginTransaction();           

            // ACTUALIZANDO CABECERA PAGOS
            $condicion = $request->condicion;
            $observacion = $request->observacion;

            $pago->update([
                'condicion' => $condicion,
                'observacion' => $observacion
            ]);

            if($condicion == "ABONADO")
            {
                $pago->update([
                    'fecha_aprobacion' => $fecha_aprobacion,
                ]);
            }
            //INDICADOR DE DEUDA EN CLIENTE
            /* if($condicion == "ABONADO")
            {
                $cliente = Cliente::find($pago->cliente_id);                
                $cliente->update([
                        'deuda' => '0',
                    ]);
            } */
            
            // ACTUALIZANDO DETALLE PAGOS
            $detalle_id = $request->detalle_id;
            //$observacion = $request->observacion;
            $cuenta = $request->cuenta;
            $titular = $request->titular;
            $fecha_deposito = $request->fecha_deposito;
            $cont = 0;

            while ($cont < count((array)$detalle_id)) {

                DetallePago::where('id', $detalle_id[$cont])
                        ->update(array(//'observacion' => $observacion[$cont],
                                        'cuenta' => $cuenta[$cont],
                                        'titular' => $titular[$cont],
                                        'fecha_deposito' => $fecha_deposito[$cont],
                                        )
                                );

                $cont++;
            }     

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }
        
        return redirect()->route('administracion.porrevisar')->with('info', 'actualizado');
    }

    public function DescargarImagen($imagen)
    {   
        $destinationPath = base_path("public/storage/pagos/".$imagen);
        /* $destinationPath = storage_path("app/public/adjuntos/".$pedido->adjunto); */

        return response()->download($destinationPath);
    }
}
