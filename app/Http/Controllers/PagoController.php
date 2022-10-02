<?php

namespace App\Http\Controllers;

use App\Events\PagoEvent;
use App\Models\Cliente;
use App\Models\DetallePago;
use App\Models\Pago;
use App\Models\PagoPedido;
use App\Models\Pedido;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PagoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $pagos = Pago::join('users as u', 'pagos.user_id', 'u.id')
            ->join('detalle_pagos as dpa', 'pagos.id', 'dpa.pago_id') 
            ->join('pago_pedidos as pp', 'pagos.id', 'pp.pago_id')
            ->rightjoin('pedidos as p', 'pp.pedido_id', 'p.id')
            ->rightjoin('detalle_pedidos as dpe', 'p.id', 'dpe.pedido_id')
            ->select('pagos.id',
                    'dpe.codigo as codigos',
                    'u.name as users',
                    'pagos.observacion',
                    'dpe.total as total_deuda',
                    'pagos.total_cobro',
                    DB::raw('sum(dpa.monto) as total_pago'),
                    'pagos.condicion',
                    'pagos.created_at as fecha'
                    )
            ->where('pagos.estado', '1')
            ->where('dpe.estado', '1')
            ->where('dpa.estado', '1')
            //->where('pagos.condicion', 'ABONADO')
            ->groupBy('pagos.id',
                    'dpe.codigo',
                    'u.name',
                    'pagos.observacion','dpe.total',
                    'pagos.total_cobro',
                    'pagos.condicion',
                    'pagos.created_at')
            ->get();
        
        $superasesor = User::where('rol', 'Super asesor')->count();

        return view('pagos.index', compact('pagos', 'superasesor'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $clientes = Cliente::where('estado', '1')
                            ->where('user_id', Auth::user()->id)
                            ->where('tipo', '1')
                            ->get();
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
                        'dp.total')
                ->where('pedidos.cliente_id', $request->cliente_id)
                ->where('pedidos.pago', '0')
                ->where('pedidos.estado', '1')
                ->where('dp.estado', '1')                
                ->get();
            
            foreach ($pedidos as $pedido) {
                $html .= '<option value="' . $pedido->id . '_' . $pedido->codigo . '_' . $pedido->total . '">Código: ' . $pedido->codigo . ' - Total: S/' . $pedido->total . '</option>';
            }
        }
        return response()->json(['html' => $html]);
    }

    public function store(Request $request)
    {
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

            while ($contPe < count((array)$pedido_id)) {

                $pagoPedido = PagoPedido::create([
                        'pago_id' => $pago->id,
                        'pedido_id' => $pedido_id[$contPe],
                        'estado' => '1'
                    ]);

                //INDICADOR DE PAGOS
                $pedido = Pedido::find($pagoPedido->pedido_id);

                $pedido->update([
                    'pago' => '1',
                ]);

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
                       
            if($deuda_total - $pagado <= 3){
                $pago->update([
                    'condicion' => 'PAGO', 
                    'notificacion' => 'Nuevo pago registrado',                    
                    'diferencia' => '0'//ACTUALIZAR LA DEUDA EN EL PAGO
                ]);

                //ACTUALIZAR QUE CLIENTE NO DEBE
                $cliente = Cliente::find($request->cliente_id);                
                $cliente->update([
                        'deuda' => '0',
                    ]);

                event(new PagoEvent($pago));
            }
            else
            {
                //ACTUALIZAR LA DEUDA EN EL PAGO
                $pago->update([
                    'condicion' => 'ADELANTO',
                    'diferencia' => $deuda_total - $pagado
                ]);
            }
            
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
                    'pagos.created_at')
            ->first();
        
        $pagoPedidos = PagoPedido::join('pedidos as p', 'pago_pedidos.pedido_id', 'p.id')
            ->join('detalle_pedidos as dp', 'p.id', 'dp.pedido_id')
            ->select('pago_pedidos.id', 
                    'dp.codigo',
                    'p.id as pedidos',
                    'p.condicion',
                    'dp.total')
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
                    'dp.total')
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
    
    public function destroy(Pago $pago)    
    {   
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

    public function MisPagos()
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
            ->groupBy('pagos.id', 
                    'dpe.codigo', 
                    'u.name',
                    'pagos.observacion', 'dpe.total',
                    'pagos.total_cobro',
                    'pagos.condicion', 
                    'pagos.created_at')
            ->get();
        
        $superasesor = User::where('rol', 'Super asesor')->count();

        return view('pagos.mispagos', compact('pagos', 'superasesor'));
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
            ->where('pagos.condicion', 'OBSERVADO')
            ->groupBy('pagos.id', 
                    'dpe.codigo', 
                    'u.name',
                    'pagos.observacion', 'dpe.total',
                    'pagos.total_cobro',
                    'pagos.condicion', 
                    'pagos.created_at')
            ->get();

        $superasesor = User::where('rol', 'Super asesor')->count();

        return view('pagos.pagosobservados', compact('pagos', 'superasesor'));
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
                    'pagos.saldo',
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
                    'pagos.saldo',
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
                    'pagos.saldo',
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
                    'pagos.saldo',
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
            "ABONADO" => 'ABONADO'
        ];

        $cuentas = [
            "BCP" => 'BCP',
            "BBVA" => 'BBVA',
            "YAPE" => 'YAPE'
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
                    'pagos.saldo',
                    'pagos.condicion', 
                    'pagos.estado', 
                    'pagos.created_at as fecha')
            ->where('pagos.id', $pago->id)
            ->groupBy('pagos.id', 
                    'u.name',
                    'c.celular',
                    'c.nombre',
                    'pagos.observacion', 
                    'pagos.saldo',
                    'pagos.condicion', 
                    'pagos.estado', 
                    'pagos.created_at')
            ->first();
        
        $pagoPedidos = PagoPedido::join('pedidos as p', 'pago_pedidos.pedido_id', 'p.id')
            /* ->join('clientes as c', 'p.user_id', 'c.id') */
            ->join('detalle_pedidos as dp', 'p.id', 'dp.pedido_id')
            ->select('pago_pedidos.id', 
                    /* 'c.celular', //cliente
                    'c.nombre', //cliente */
                    'dp.codigo',
                    'p.id as pedidos',
                    'p.condicion',
                    'dp.total')
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
        $fecha_aprobacion = Carbon::now()->format('d/m/Y');

        try {
            DB::beginTransaction();           

            // ACTUALIZANDO CABECERA PAGOS
            $condicion = $request->condicion;

            $pago->update([
                'condicion' => $condicion,
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
            $observacion = $request->observacion;
            $cuenta = $request->cuenta;
            $titular = $request->titular;
            $fecha_deposito = $request->fecha_deposito;
            $cont = 0;

            while ($cont < count((array)$detalle_id)) {

                DetallePago::where('id', $detalle_id[$cont])
                        ->update(array('observacion' => $observacion[$cont],
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
