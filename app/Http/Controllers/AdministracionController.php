<?php

namespace App\Http\Controllers;

use App\Events\PagoEvent;
use App\Models\Cliente;
use App\Models\DetallePago;
use App\Models\DetallePedido;
use App\Models\MovimientoBancario;
use App\Models\EntidadBancaria;
use App\Models\CuentaBancaria;
use App\Models\Titular;
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

class AdministracionController extends Controller
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

    public function PorRevisar(Request $request)
    {
        //$q1 = Carbon::createFromFormat('d/m/Y', $request->q1)->format('Y-m-d');
        //$q2 = Carbon::createFromFormat('d/m/Y', $request->q2)->format('Y-m-d');

        if(!$request->q1)
        {
            $dateMin = Carbon::create(2022, 8, 1, 0, 0, 0)->startOfMonth()->format('d/m/Y');  // Carbon::now()->subDays(24)->format('d/m/Y');
        }else{
            $dateMin = Carbon::createFromFormat('d/m/Y', $request->q1)->format('d/m/Y');
        }
        if(!$request->q2)
        {
            $dateMax = Carbon::now()->format('d/m/Y');
        }else{
            $dateMax = Carbon::createFromFormat('d/m/Y', $request->q2)->format('d/m/Y');
        }

        $superasesor = User::where('rol', 'Super asesor')->count();

        return view('administracion.porrevisar', compact('superasesor','dateMin','dateMax'));
    }

    

    public function PorRevisartabla(Request $request)
    {

        $min = Carbon::createFromFormat('d/m/Y', $request->min)->format('Y-m-d');
        $max = Carbon::createFromFormat('d/m/Y', $request->max)->format('Y-m-d');

        $pagos=null;

        
       
        if(!$request->asesores)
        {
            $pagos = Pago::join('users as u', 'pagos.user_id', 'u.id')
                ->join('clientes as c', 'pagos.cliente_id', 'c.id')
                ->select('pagos.id as id',
                DB::raw(" (CASE WHEN pagos.id<10 THEN concat('PAG',u.identificador,'-',
                                IF ( (select count(dpago.id) from detalle_pagos dpago where dpago.pago_id=pagos.id and dpago.estado in (1) )>1,'V','I' )  ,
                                IF ( (select count(ppedidos.id) from pago_pedidos ppedidos where ppedidos.pago_id=pagos.id and ppedidos.estado in (1) ) >1,'V','I' ),
                                '-',pagos.id
                                ) 
                            WHEN pagos.id<100  THEN concat('PAG',u.identificador,'-',
                                IF ( (select count(dpago.id) from detalle_pagos dpago where dpago.pago_id=pagos.id and dpago.estado in (1) )>1,'V','I' )  ,
                                IF ( (select count(ppedidos.id) from pago_pedidos ppedidos where ppedidos.pago_id=pagos.id and ppedidos.estado in (1) ) >1,'V','I' ),
                                '-',pagos.id) 
                            WHEN pagos.id<1000  THEN concat('PAG',u.identificador,'-',
                                IF ( (select count(dpago.id) from detalle_pagos dpago where dpago.pago_id=pagos.id and dpago.estado in (1) )>1,'V','I' )  ,
                                IF ( (select count(ppedidos.id) from pago_pedidos ppedidos where ppedidos.pago_id=pagos.id and ppedidos.estado in (1) ) >1,'V','I' ),
                                '-',pagos.id) 
                            ELSE concat('PAG',u.identificador,'-',
                                IF ( (select count(dpago.id) from detalle_pagos dpago where dpago.pago_id=pagos.id and dpago.estado in (1) )>1,'V','I' )  ,
                                IF ( (select count(ppedidos.id) from pago_pedidos ppedidos where ppedidos.pago_id=pagos.id and ppedidos.estado in (1) ) >1,'V','I' ),
                                '-',pagos.id) END) AS id2"),
                        'u.identificador as users',
                        'c.celular',
                        'c.icelular',
                        DB::raw(" (CASE WHEN pagos.subcondicion='COURIER PERDONADO' THEN 'COURIER PERDONADO'
                                    else CONCAT(c.celular,IF(ISNULL(c.icelular),'',CONCAT('-',c.icelular) )) end) as cliente "),
                        'pagos.observacion',
                        'pagos.total_cobro',
                        'pagos.condicion',
                        'pagos.subcondicion',
                        'pagos.created_at',
                        DB::raw('(select DATE_FORMAT( MIN(dpa.fecha), "%Y-%m-%d")   from detalle_pagos dpa where dpa.pago_id=pagos.id and dpa.estado=1) as fecha'),
                        DB::raw('(select DATE_FORMAT( MIN(dpa.fecha), "%d/%m/%Y %H:%i:%s")   from detalle_pagos dpa where dpa.pago_id=pagos.id and dpa.estado=1) as fecha2'),
                        DB::raw('(select UNIX_TIMESTAMP(MIN(dpa.fecha))   from detalle_pagos dpa where dpa.pago_id=pagos.id and dpa.estado=1) as fecha_timestamp'),
                        DB::raw(" (select count(dpago.id) from detalle_pagos dpago where dpago.pago_id=pagos.id and dpago.estado in (1) ) as cantidad_voucher "),
                        DB::raw(" (select count(ppedidos.id) from pago_pedidos ppedidos where ppedidos.pago_id=pagos.id and ppedidos.estado in (1)  ) as cantidad_pedido "),
                        DB::raw(" ( select GROUP_CONCAT(ppp.codigo) from pago_pedidos ped inner join pedidos ppp on ped.pedido_id =ppp.id where pagos.id=ped.pago_id and ped.estado=1 and ppp.estado=1 and ped.pagado in (1,2)) as codigos "),
                        DB::raw(" (select sum(ped2.abono) from pago_pedidos ped2 where ped2.pago_id =pagos.id and ped2.estado=1 and ped2.pagado in (1,2) ) as total_pago ")   
                        )
                ->whereIn('pagos.condicion', ['PAGO','ADELANTO']) 
                ->where('pagos.estado', '1')  
                ->whereBetween(DB::raw('( (select DATE( MIN(dpa.fecha))   from detalle_pagos dpa where dpa.pago_id=pagos.id and dpa.estado=1)  )'), [$min, $max]) //rango de fechas
                ->get();
        }else{
            $pagos = Pago::join('users as u', 'pagos.user_id', 'u.id')
            ->join('clientes as c', 'pagos.cliente_id', 'c.id')
            ->select('pagos.id as id',
                    DB::raw(" (CASE WHEN pagos.id<10 THEN concat('PAG',u.identificador,'-',
                                IF ( (select count(dpago.id) from detalle_pagos dpago where dpago.pago_id=pagos.id and dpago.estado in (1) )>1,'V','I' )  ,
                                IF ( (select count(ppedidos.id) from pago_pedidos ppedidos where ppedidos.pago_id=pagos.id and ppedidos.estado in (1) ) >1,'V','I' ),
                                '-',pagos.id
                                ) 
                            WHEN pagos.id<100  THEN concat('PAG',u.identificador,'-',
                                IF ( (select count(dpago.id) from detalle_pagos dpago where dpago.pago_id=pagos.id and dpago.estado in (1) )>1,'V','I' )  ,
                                IF ( (select count(ppedidos.id) from pago_pedidos ppedidos where ppedidos.pago_id=pagos.id and ppedidos.estado in (1) ) >1,'V','I' ),
                                '-',pagos.id) 
                            WHEN pagos.id<1000  THEN concat('PAG',u.identificador,'-',
                                IF ( (select count(dpago.id) from detalle_pagos dpago where dpago.pago_id=pagos.id and dpago.estado in (1) )>1,'V','I' )  ,
                                IF ( (select count(ppedidos.id) from pago_pedidos ppedidos where ppedidos.pago_id=pagos.id and ppedidos.estado in (1) ) >1,'V','I' ),
                                '-',pagos.id) 
                            ELSE concat('PAG',u.identificador,'-',
                                IF ( (select count(dpago.id) from detalle_pagos dpago where dpago.pago_id=pagos.id and dpago.estado in (1) )>1,'V','I' )  ,
                                IF ( (select count(ppedidos.id) from pago_pedidos ppedidos where ppedidos.pago_id=pagos.id and ppedidos.estado in (1) ) >1,'V','I' ),
                                '-',pagos.id) END) AS id2"),
                    'u.identificador as users',
                    'c.celular',
                    'c.icelular',
                    DB::raw(" (CASE WHEN pagos.subcondicion='COURIER PERDONADO' THEN 'COURIER PERDONADO'
                                    else CONCAT(c.celular,IF(ISNULL(c.icelular),'',CONCAT('-',c.icelular) )) end) as cliente "),
                    'pagos.observacion',                        
                    'pagos.total_cobro',
                    'pagos.condicion',
                    'pagos.subcondicion',
                    'pagos.created_at',
                    //DB::raw('(select DATE_FORMAT( MIN(dpa.fecha), "%d/%m/%Y %H:%i:%s")   from detalle_pagos dpa where dpa.pago_id=pagos.id and dpa.estado=1) as fecha'),
                    DB::raw('(select DATE_FORMAT( MIN(dpa.fecha), "%Y-%m-%d")   from detalle_pagos dpa where dpa.pago_id=pagos.id and dpa.estado=1) as fecha'),
                    DB::raw('(select DATE_FORMAT( MIN(dpa.fecha), "%d/%m/%Y %H:%i:%s")   from detalle_pagos dpa where dpa.pago_id=pagos.id and dpa.estado=1) as fecha2'),
                    DB::raw('(select UNIX_TIMESTAMP(MIN(dpa.fecha))   from detalle_pagos dpa where dpa.pago_id=pagos.id and dpa.estado=1) as fecha_timestamp'),
                    DB::raw(" (select count(dpago.id) from detalle_pagos dpago where dpago.pago_id=pagos.id and dpago.estado in (1) ) as cantidad_voucher "),
                    DB::raw(" (select count(ppedidos.id) from pago_pedidos ppedidos where ppedidos.pago_id=pagos.id and ppedidos.estado in (1)  ) as cantidad_pedido "),
                    DB::raw(" ( select GROUP_CONCAT(ppp.codigo) from pago_pedidos ped inner join pedidos ppp on ped.pedido_id =ppp.id where pagos.id=ped.pago_id and ped.estado=1 and ppp.estado=1 and ped.pagado in (1,2)) as codigos "),
                    DB::raw(" (select sum(ped2.abono) from pago_pedidos ped2 where ped2.pago_id =pagos.id and ped2.estado=1 and ped2.pagado in (1,2) ) as total_pago ")   
                    )
            ->where('pagos.user_id',$request->asesores) 
            ->whereIn('pagos.condicion', ['PAGO','ADELANTO'])
            ->where('pagos.estado', '1')
            ->whereBetween(DB::raw('( (select DATE( MIN(dpa.fecha))   from detalle_pagos dpa where dpa.pago_id=pagos.id and dpa.estado=1)  )'), [$min, $max]) //rango de fechas
            ->get(); 
        }  
        
        return Datatables::of($pagos)
            ->addIndexColumn()
            ->addColumn('action', function($pago){     
                $btn='';

                if(Auth::user()->rol == "Administrador"){
                    $btn=$btn.'<a href="'.route('pagos.show', $pago['id']).'" class="btn btn-info btn-sm">Ver</a>';

                    $btn=$btn.'<a href="'.route('administracion.revisar', $pago).'" class="btn btn-success btn-sm">Revisar</a>';

                    $btn = $btn.'<a href="" data-target="#modal-delete" data-toggle="modal" data-delete="'.$pago['id'].'"><button class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i> Eliminar</button></a>';
                }
                
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function Revisar(Pago $pago)    
    {
        //$request->pago_id
        

        $cuentas = [
            "BCP" => 'BCP',
            "BBVA" => 'BBVA',
            "YAPE" => 'YAPE',
            "INTERBANK" => 'INTERBANK'
        ];

        $titulares = [
            "EPIFANIO SOLANO HUAMAN" => 'EPIFANIO SOLANO HUAMAN',
            "NIKSER DENIS ORE RIVEROS" => 'NIKSER DENIS ORE RIVEROS'
        ];

        $bancos = [
            "BCP" => 'BCP',
            "BBVA" => 'BBVA',
            "INTERBANK" => 'INTERBANK'
        ];


        


        $pagos = Pago::join('users as u', 'pagos.user_id', 'u.id')
            ->join('clientes as c', 'pagos.cliente_id', 'c.id')
            ->select('pagos.id', 
                    DB::raw(" (CASE WHEN (select count(dpago.id) from detalle_pagos dpago where dpago.pago_id=pagos.id and dpago.estado in (1) )>1 then 'V' else 'I' end) as cantidad_voucher "),
                    DB::raw(" (CASE WHEN (select count(ppedidos.id) from pago_pedidos ppedidos where ppedidos.pago_id=pagos.id and ppedidos.estado in (1)  )>1 then 'V' else 'I' end) as cantidad_pedido "),
                    'u.identificador as users',
                    'c.celular', //cliente
                    'c.nombre', //cliente
                    'pagos.observacion', 
                    //'pagos.saldo',
                    'pagos.condicion', 
                    'pagos.estado', 
                    'pagos.created_at as fecha')
            ->where('pagos.id', $pago->id)
            ->groupBy('pagos.id', 
                    'u.identificador',
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
            //->where('pago_pedidos.abono','>' ,'0')
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
                    DB::raw('DATE_FORMAT(fecha_deposito, "%d/%m/%Y") as fecha_deposito'),
                    DB::raw('DATE_FORMAT(fecha_deposito, "%Y-%m-%d") as fecha_deposito_change'),
                    //'fecha_deposito',
                    'observacion')
            ->where('estado', '1')
            ->where('pago_id', $pago->id)
            ->get();
        //DB::raw('sum(detalle_pagos.monto) as total')

        $condiciones = [
            //"PAGO" => 'PAGO',
            "OBSERVADO" => 'OBSERVADO',
            "ABONADO" => 'ABONADO',
            "PENDIENTE" => 'PENDIENTE',
            //"ABONADO_PARCIAL" => 'ABONADO_PARCIAL'
        ];

        return view('administracion.revisar', compact('pago', 'condiciones', 'cuentas', 'titulares', 'pagos', 'pagoPedidos', 'detallePagos','bancos'));
    }

    public function Administracionpendientes(Request $request)
    {
        if(!$request->q1)
        {
            $dateMin = Carbon::now()->subDays(24)->format('d/m/Y');
        }else{
            $dateMin = Carbon::createFromFormat('d/m/Y', $request->q1)->format('d/m/Y');
        }
        if(!$request->q2)
        {
            $dateMax = Carbon::now()->format('d/m/Y');
        }else{
            $dateMax = Carbon::createFromFormat('d/m/Y', $request->q2)->format('d/m/Y');
        }

        $superasesor = User::where('rol', 'Super asesor')->count();

        return view('administracion.pendientes', compact('superasesor','dateMin','dateMax'));
    }

    public function Administracionpendientestabla(Request $request)
    {

        $min = Carbon::createFromFormat('d/m/Y', $request->min)->format('Y-m-d');
        $max = Carbon::createFromFormat('d/m/Y', $request->max)->format('Y-m-d');

        $pagos=null;

        $pagos = Pago::join('users as u', 'pagos.user_id', 'u.id')
                ->join('clientes as c', 'pagos.cliente_id', 'c.id')
                ->select('pagos.id as id',
                            DB::raw(" (CASE WHEN pagos.id<10 THEN concat('PAG',u.identificador,'-',
                                IF ( (select count(dpago.id) from detalle_pagos dpago where dpago.pago_id=pagos.id and dpago.estado in (1) )>1,'V','I' )  ,
                                IF ( (select count(ppedidos.id) from pago_pedidos ppedidos where ppedidos.pago_id=pagos.id and ppedidos.estado in (1) ) >1,'V','I' ),
                                '-',pagos.id
                                ) 
                            WHEN pagos.id<100  THEN concat('PAG',u.identificador,'-',
                                IF ( (select count(dpago.id) from detalle_pagos dpago where dpago.pago_id=pagos.id and dpago.estado in (1) )>1,'V','I' )  ,
                                IF ( (select count(ppedidos.id) from pago_pedidos ppedidos where ppedidos.pago_id=pagos.id and ppedidos.estado in (1) ) >1,'V','I' ),
                                '-',pagos.id) 
                            WHEN pagos.id<1000  THEN concat('PAG',u.identificador,'-',
                                IF ( (select count(dpago.id) from detalle_pagos dpago where dpago.pago_id=pagos.id and dpago.estado in (1) )>1,'V','I' )  ,
                                IF ( (select count(ppedidos.id) from pago_pedidos ppedidos where ppedidos.pago_id=pagos.id and ppedidos.estado in (1) ) >1,'V','I' ),
                                '-',pagos.id) 
                            ELSE concat('PAG',u.identificador,'-',
                                IF ( (select count(dpago.id) from detalle_pagos dpago where dpago.pago_id=pagos.id and dpago.estado in (1) )>1,'V','I' )  ,
                                IF ( (select count(ppedidos.id) from pago_pedidos ppedidos where ppedidos.pago_id=pagos.id and ppedidos.estado in (1) ) >1,'V','I' ),
                                '-',pagos.id) END) AS id2"),
                        'u.identificador as users',
                        'c.celular',
                        'pagos.observacion',                        
                        'pagos.total_cobro',
                        'pagos.condicion',
                        'pagos.created_at',
                        DB::raw('(select DATE_FORMAT( MIN(dpa.fecha), "%Y-%m-%d")   from detalle_pagos dpa where dpa.pago_id=pagos.id and dpa.estado=1) as fecha'),
                        DB::raw('(select DATE_FORMAT( MIN(dpa.fecha), "%d/%m/%Y %H:%i:%s")   from detalle_pagos dpa where dpa.pago_id=pagos.id and dpa.estado=1) as fecha2'),
                        DB::raw('(select UNIX_TIMESTAMP(MIN(dpa.fecha))   from detalle_pagos dpa where dpa.pago_id=pagos.id and dpa.estado=1) as fecha_timestamp'),
                        DB::raw(" (select count(dpago.id) from detalle_pagos dpago where dpago.pago_id=pagos.id and dpago.estado in (1) ) as cantidad_voucher "),
                        DB::raw(" (select count(ppedidos.id) from pago_pedidos ppedidos where ppedidos.pago_id=pagos.id and ppedidos.estado in (1)  ) as cantidad_pedido "),
                        DB::raw(" ( select GROUP_CONCAT(ppp.codigo) from pago_pedidos ped inner join pedidos ppp on ped.pedido_id =ppp.id where pagos.id=ped.pago_id and ped.estado=1 and ppp.estado=1 and ped.pagado in (1,2)) as codigos "),
                        DB::raw(" (select sum(ped2.abono) from pago_pedidos ped2 where ped2.pago_id =pagos.id and ped2.estado=1 and ped2.pagado in (1,2) ) as total_pago ")   
                        )
                ->whereIn('pagos.condicion', ['PENDIENTE'])
                ->where('pagos.estado', '1') 
                ->whereBetween(DB::raw('( (select DATE( MIN(dpa.fecha))   from detalle_pagos dpa where dpa.pago_id=pagos.id and dpa.estado=1)  )'), [$min, $max]); //rango de fechas
                
        if(!$request->asesores)
        {
           
        }else{
            $pagos=$pagos->where('pagos.user_id',$request->asesores);
        }  

        $pagos=$pagos->get();
      
        
        return Datatables::of($pagos)
            ->addIndexColumn()
            ->addColumn('action', function($pago){     
                $btn='';

                if(Auth::user()->rol == "Administrador"){
                    $btn=$btn.'<a href="'.route('pagos.show', $pago['id']).'" class="btn btn-info btn-sm">Ver</a>';

                    $btn=$btn.'<a href="'.route('administracion.revisarpendiente', $pago).'" class="btn btn-success btn-sm">Revisar</a>';

                    $btn = $btn.'<a href="" data-target="#modal-delete" data-toggle="modal" data-delete="'.$pago['id'].'"><button class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i> Eliminar</button></a>';
                }
                
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function Revisarpendiente(Pago $pago)    
    {
        //$request->pago_id
        

        $cuentas = [
            "BCP" => 'BCP',
            "BBVA" => 'BBVA',
            "YAPE" => 'YAPE',
            "INTERBANK" => 'INTERBANK'
        ];

        $titulares = [
            "EPIFANIO SOLANO HUAMAN" => 'EPIFANIO SOLANO HUAMAN',
            "NIKSER DENIS ORE RIVEROS" => 'NIKSER DENIS ORE RIVEROS'
        ];

        $bancos = [
            "BCP" => 'BCP',
            "BBVA" => 'BBVA',
            "INTERBANK" => 'INTERBANK'
        ];


        


        $pagos = Pago::join('users as u', 'pagos.user_id', 'u.id')
            ->join('clientes as c', 'pagos.cliente_id', 'c.id')
            ->select('pagos.id', 
                    DB::raw(" (CASE WHEN (select count(dpago.id) from detalle_pagos dpago where dpago.pago_id=pagos.id and dpago.estado in (1) )>1 then 'V' else 'I' end) as cantidad_voucher "),
                    DB::raw(" (CASE WHEN (select count(ppedidos.id) from pago_pedidos ppedidos where ppedidos.pago_id=pagos.id and ppedidos.estado in (1)  )>1 then 'V' else 'I' end) as cantidad_pedido "),
                    'u.identificador as users',
                    'c.celular', //cliente
                    'c.nombre', //cliente
                    'pagos.observacion', 
                    //'pagos.saldo',
                    'pagos.condicion', 
                    'pagos.estado', 
                    'pagos.created_at as fecha')
            ->where('pagos.id', $pago->id)
            ->groupBy('pagos.id', 
                    'u.identificador',
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
            //->where('pago_pedidos.abono','>' ,'0')
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
                    DB::raw('DATE_FORMAT(fecha_deposito, "%d/%m/%Y") as fecha_deposito'),
                    DB::raw('DATE_FORMAT(fecha_deposito, "%Y-%m-%d") as fecha_deposito_change'),
                    //'fecha_deposito',
                    'observacion')
            ->where('estado', '1')
            ->where('pago_id', $pago->id)
            ->get();
        //DB::raw('sum(detalle_pagos.monto) as total')

        $condiciones = [
            //"PAGO" => 'PAGO',
            "OBSERVADO" => 'OBSERVADO',
            "ABONADO" => 'ABONADO',
            "PENDIENTE" => 'PENDIENTE',
            //"ABONADO_PARCIAL" => 'ABONADO_PARCIAL'
        ];

        return view('administracion.revisarpendiente', compact('pago', 'condiciones', 'cuentas', 'titulares', 'pagos', 'pagoPedidos', 'detallePagos','bancos'));
    }

    public function Observados()
    {               
        $superasesor = User::where('rol', 'Super asesor')->count();

        $dateMin = Carbon::now()->subDays(4)->format('d/m/Y');
        $dateMax = Carbon::now()->format('d/m/Y');

        return view('administracion.observados', compact('superasesor','dateMin','dateMax'));
    }

    public function Observadostabla(Request $request)
    {
        $pagos=null;
       
        if(!$request->asesores)
        {
            $pagos = Pago::join('users as u', 'pagos.user_id', 'u.id')
                ->join('clientes as c', 'pagos.cliente_id', 'c.id')
                ->select('pagos.id as id',
                            DB::raw(" (CASE WHEN pagos.id<10 THEN concat('PAG',u.identificador,'-',
                                IF ( (select count(dpago.id) from detalle_pagos dpago where dpago.pago_id=pagos.id and dpago.estado in (1) )>1,'V','I' )  ,
                                IF ( (select count(ppedidos.id) from pago_pedidos ppedidos where ppedidos.pago_id=pagos.id and ppedidos.estado in (1) ) >1,'V','I' ),
                                '-',pagos.id
                                ) 
                            WHEN pagos.id<100  THEN concat('PAG',u.identificador,'-',
                                IF ( (select count(dpago.id) from detalle_pagos dpago where dpago.pago_id=pagos.id and dpago.estado in (1) )>1,'V','I' )  ,
                                IF ( (select count(ppedidos.id) from pago_pedidos ppedidos where ppedidos.pago_id=pagos.id and ppedidos.estado in (1) ) >1,'V','I' ),
                                '-',pagos.id) 
                            WHEN pagos.id<1000  THEN concat('PAG',u.identificador,'-',
                                IF ( (select count(dpago.id) from detalle_pagos dpago where dpago.pago_id=pagos.id and dpago.estado in (1) )>1,'V','I' )  ,
                                IF ( (select count(ppedidos.id) from pago_pedidos ppedidos where ppedidos.pago_id=pagos.id and ppedidos.estado in (1) ) >1,'V','I' ),
                                '-',pagos.id) 
                            ELSE concat('PAG',u.identificador,'-',
                                IF ( (select count(dpago.id) from detalle_pagos dpago where dpago.pago_id=pagos.id and dpago.estado in (1) )>1,'V','I' )  ,
                                IF ( (select count(ppedidos.id) from pago_pedidos ppedidos where ppedidos.pago_id=pagos.id and ppedidos.estado in (1) ) >1,'V','I' ),
                                '-',pagos.id) END) AS id2"),
                        'u.identificador as users',
                        'c.celular',
                        'c.icelular',
                        DB::raw(" CONCAT(c.celular,IF(ISNULL(c.icelular),'',CONCAT('-',c.icelular) )) as cliente"),
                        'pagos.observacion',                        
                        'pagos.total_cobro',
                        'pagos.condicion',
                        DB::raw('(select DATE_FORMAT( MIN(dpa.fecha), "%d/%m/%Y %H:%i:%s")   from detalle_pagos dpa where dpa.pago_id=pagos.id and dpa.estado=1) as fecha'),
                        DB::raw('(select UNIX_TIMESTAMP(MIN(dpa.fecha))   from detalle_pagos dpa where dpa.pago_id=pagos.id and dpa.estado=1) as fecha_timestamp'),
                        DB::raw(" (select count(dpago.id) from detalle_pagos dpago where dpago.pago_id=pagos.id and dpago.estado in (1) ) as cantidad_voucher "),
                        DB::raw(" (select count(ppedidos.id) from pago_pedidos ppedidos where ppedidos.pago_id=pagos.id and ppedidos.estado in (1)  ) as cantidad_pedido "),
                        DB::raw(" ( select GROUP_CONCAT(ppp.codigo) from pago_pedidos ped inner join pedidos ppp on ped.pedido_id =ppp.id where pagos.id=ped.pago_id and ped.estado=1 and ppp.estado=1 and ped.pagado in (1,2)) as codigos "),
                        DB::raw(" (select sum(ped2.abono) from pago_pedidos ped2 where ped2.pago_id =pagos.id and ped2.estado=1 and ped2.pagado in (1,2) ) as total_pago ")   
                        )                
                ->whereIn('pagos.condicion', ['OBSERVADO'])
                ->where('pagos.estado', '1')              
                ->get();                
        }else{
            $pagos = Pago::join('users as u', 'pagos.user_id', 'u.id')
            ->join('clientes as c', 'pagos.cliente_id', 'c.id')
            ->select('pagos.id as id',
                    'u.identificador as users',
                    'c.celular',
                    'c.icelular',
                    DB::raw(" CONCAT(c.celular,IF(ISNULL(c.icelular),'',CONCAT('-',c.icelular) )) as cliente"),
                    'pagos.observacion',                        
                    'pagos.total_cobro',
                    'pagos.condicion',
                    DB::raw('(select DATE_FORMAT( MIN(dpa.fecha), "%d/%m/%Y %H:%i:%s")   from detalle_pagos dpa where dpa.pago_id=pagos.id and dpa.estado=1) as fecha'),
                    DB::raw('(select UNIX_TIMESTAMP(MIN(dpa.fecha))   from detalle_pagos dpa where dpa.pago_id=pagos.id and dpa.estado=1) as fecha_timestamp'),
                    DB::raw(" (select count(dpago.id) from detalle_pagos dpago where dpago.pago_id=pagos.id and dpago.estado in (1) ) as cantidad_voucher "),
                    DB::raw(" (select count(ppedidos.id) from pago_pedidos ppedidos where ppedidos.pago_id=pagos.id and ppedidos.estado in (1)  ) as cantidad_pedido "),
                    DB::raw(" ( select GROUP_CONCAT(ppp.codigo) from pago_pedidos ped inner join pedidos ppp on ped.pedido_id =ppp.id where pagos.id=ped.pago_id and ped.estado=1 and ppp.estado=1 and ped.pagado in (1,2)) as codigos "),
                    DB::raw(" (select sum(ped2.abono) from pago_pedidos ped2 where ped2.pago_id =pagos.id and ped2.estado=1 and ped2.pagado in (1,2) ) as total_pago ")   
                    )
            ->where('pagos.user_id',$request->asesores) 
            ->whereIn('pagos.condicion', ['OBSERVADO'])
            ->where('pagos.estado', '1') 
            ->get(); 
        }
        
        return Datatables::of($pagos)
            ->addIndexColumn()
            ->addColumn('action', function($pago){     
                $btn='';
                if(Auth::user()->rol == "Administrador"){
                    $btn=$btn.'<a href="'.route('pagos.show', $pago['id']).'" class="btn btn-info btn-sm">Ver</a>';
                    $btn=$btn.'<a href="'.route('administracion.revisarobservado', $pago).'" class="btn btn-success btn-sm">Revisar</a>';
                    $btn = $btn.'<a href="" data-target="#modal-delete" data-toggle="modal" data-delete="'.$pago['id'].'"><button class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i> Eliminar</button></a>';
                }
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function Revisarobservado(Pago $pago)    
    {
        //$request->pago_id
        

        $cuentas = [
            "BCP" => 'BCP',
            "BBVA" => 'BBVA',
            "YAPE" => 'YAPE',
            "INTERBANK" => 'INTERBANK'
        ];

        $titulares = [
            "EPIFANIO SOLANO HUAMAN" => 'EPIFANIO SOLANO HUAMAN',
            "NIKSER DENIS ORE RIVEROS" => 'NIKSER DENIS ORE RIVEROS'
        ];

        $bancos = [
            "BCP" => 'BCP',
            "BBVA" => 'BBVA',
            "INTERBANK" => 'INTERBANK'
        ];


        


        $pagos = Pago::join('users as u', 'pagos.user_id', 'u.id')
            ->join('clientes as c', 'pagos.cliente_id', 'c.id')
            ->select('pagos.id', 
                    DB::raw(" (CASE WHEN (select count(dpago.id) from detalle_pagos dpago where dpago.pago_id=pagos.id and dpago.estado in (1) )>1 then 'V' else 'I' end) as cantidad_voucher "),
                    DB::raw(" (CASE WHEN (select count(ppedidos.id) from pago_pedidos ppedidos where ppedidos.pago_id=pagos.id and ppedidos.estado in (1)  )>1 then 'V' else 'I' end) as cantidad_pedido "),
                    'u.identificador as users',
                    'c.celular', //cliente
                    'c.nombre', //cliente
                    'pagos.observacion', 
                    //'pagos.saldo',
                    'pagos.condicion', 
                    'pagos.estado', 
                    'pagos.created_at as fecha')
            ->where('pagos.id', $pago->id)
            ->groupBy('pagos.id', 
                    'u.identificador',
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
            //->where('pago_pedidos.abono','>' ,'0')
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
                    DB::raw('DATE_FORMAT(fecha_deposito, "%d/%m/%Y") as fecha_deposito'),
                    DB::raw('DATE_FORMAT(fecha_deposito, "%Y-%m-%d") as fecha_deposito_change'),
                    //'fecha_deposito',
                    'observacion')
            ->where('estado', '1')
            ->where('pago_id', $pago->id)
            ->get();
        //DB::raw('sum(detalle_pagos.monto) as total')

        $condiciones = [
            //"PAGO" => 'PAGO',
            "OBSERVADO" => 'OBSERVADO',
            "ABONADO" => 'ABONADO',
            "PENDIENTE" => 'PENDIENTE',
            //"ABONADO_PARCIAL" => 'ABONADO_PARCIAL'
        ];

        return view('administracion.revisarobservado', compact('pago', 'condiciones', 'cuentas', 'titulares', 'pagos', 'pagoPedidos', 'detallePagos','bancos'));
    }

    public function Abonados()
    {               
        $superasesor = User::where('rol', 'Super asesor')->count();

        return view('administracion.abonados', compact('superasesor'));
    }

    public function Abonadostabla(Request $request)
    {
        $pagos=null;
        if(!$request->asesores)
        {
            $pagos = Pago::join('users as u', 'pagos.user_id', 'u.id')
                ->join('clientes as c', 'pagos.cliente_id', 'c.id')
                ->select('pagos.id as id',
                        'u.identificador as users',
                        'c.celular',
                        'pagos.observacion',                        
                        'pagos.total_cobro',
                        'pagos.condicion',
                        DB::raw('(select DATE_FORMAT( MIN(dpa.fecha), "%d/%m/%Y %H:%i:%s")   from detalle_pagos dpa where dpa.pago_id=pagos.id and dpa.estado=1) as fecha'),
                        DB::raw('(select UNIX_TIMESTAMP(MIN(dpa.fecha))   from detalle_pagos dpa where dpa.pago_id=pagos.id and dpa.estado=1) as fecha_timestamp'),
                        DB::raw(" (select count(dpago.id) from detalle_pagos dpago where dpago.pago_id=pagos.id and dpago.estado in (1) ) as cantidad_voucher "),
                        DB::raw(" (select count(ppedidos.id) from pago_pedidos ppedidos where ppedidos.pago_id=pagos.id and ppedidos.estado in (1)  ) as cantidad_pedido "),
                        DB::raw(" ( select GROUP_CONCAT(ppp.codigo) from pago_pedidos ped inner join pedidos ppp on ped.pedido_id =ppp.id where pagos.id=ped.pago_id and ped.estado=1 and ppp.estado=1 and ped.pagado in (1,2)) as codigos "),
                        DB::raw(" (select sum(ped2.abono) from pago_pedidos ped2 where ped2.pago_id =pagos.id and ped2.estado=1 and ped2.pagado in (1,2) ) as total_pago ")   
                        )                
                ->whereIn('pagos.condicion', ['ABONADO_PARCIAL'])
                ->where('pagos.estado', '1')              
                ->get();                
        }else{
            $pagos = Pago::join('users as u', 'pagos.user_id', 'u.id')
            ->join('clientes as c', 'pagos.cliente_id', 'c.id')
            ->select('pagos.id as id',
                    'u.identificador as users',
                    'c.celular',
                    'pagos.observacion',                        
                    'pagos.total_cobro',
                    'pagos.condicion',
                    DB::raw('(select DATE_FORMAT( MIN(dpa.fecha), "%d/%m/%Y %H:%i:%s")   from detalle_pagos dpa where dpa.pago_id=pagos.id and dpa.estado=1) as fecha'),
                    DB::raw('(select UNIX_TIMESTAMP(MIN(dpa.fecha))   from detalle_pagos dpa where dpa.pago_id=pagos.id and dpa.estado=1) as fecha_timestamp'),
                    DB::raw(" (select count(dpago.id) from detalle_pagos dpago where dpago.pago_id=pagos.id and dpago.estado in (1) ) as cantidad_voucher "),
                    DB::raw(" (select count(ppedidos.id) from pago_pedidos ppedidos where ppedidos.pago_id=pagos.id and ppedidos.estado in (1)  ) as cantidad_pedido "),
                    DB::raw(" ( select GROUP_CONCAT(ppp.codigo) from pago_pedidos ped inner join pedidos ppp on ped.pedido_id =ppp.id where pagos.id=ped.pago_id and ped.estado=1 and ppp.estado=1 and ped.pagado in (1,2)) as codigos "),
                    DB::raw(" (select sum(ped2.abono) from pago_pedidos ped2 where ped2.pago_id =pagos.id and ped2.estado=1 and ped2.pagado in (1,2) ) as total_pago ")   
                    )
            ->where('pagos.user_id',$request->asesores) 
            ->whereIn('pagos.condicion', ['ABONADO_PARCIAL'])
            ->where('pagos.estado', '1') 
            ->get(); 
        }
        
        return Datatables::of($pagos)
            ->addIndexColumn()
            ->addColumn('action', function($pago){     
                $btn='';
                if(Auth::user()->rol == "Administrador"){
                    $btn=$btn.'<a href="'.route('pagos.show', $pago['id']).'" class="btn btn-info btn-sm">Ver</a>';
                    $btn=$btn.'<a href="'.route('administracion.revisar', $pago).'" class="btn btn-success btn-sm">Revisar</a>';
                    $btn = $btn.'<a href="" data-target="#modal-delete" data-toggle="modal" data-delete="'.$pago['id'].'"><button class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i> Eliminar</button></a>';
                }
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
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

        return view('administracion.aprobados', compact('pagos', 'superasesor'));
    }

    public function Aprobadostabla(Request $request)
    {
        $pagos=null;
        if(!$request->asesores)
        {
            $pagos = Pago::join('users as u', 'pagos.user_id', 'u.id')
                ->join('clientes as c', 'pagos.cliente_id', 'c.id')
                ->select('pagos.id as id',
                        DB::raw(" (CASE WHEN pagos.id<10 THEN concat('PAG',u.identificador,'-',
                                IF ( (select count(dpago.id) from detalle_pagos dpago where dpago.pago_id=pagos.id and dpago.estado in (1) )>1,'V','I' )  ,
                                IF ( (select count(ppedidos.id) from pago_pedidos ppedidos where ppedidos.pago_id=pagos.id and ppedidos.estado in (1) ) >1,'V','I' ),
                                '-',pagos.id
                                ) 
                            WHEN pagos.id<100  THEN concat('PAG',u.identificador,'-',
                                IF ( (select count(dpago.id) from detalle_pagos dpago where dpago.pago_id=pagos.id and dpago.estado in (1) )>1,'V','I' )  ,
                                IF ( (select count(ppedidos.id) from pago_pedidos ppedidos where ppedidos.pago_id=pagos.id and ppedidos.estado in (1) ) >1,'V','I' ),
                                '-',pagos.id) 
                            WHEN pagos.id<1000  THEN concat('PAG',u.identificador,'-',
                                IF ( (select count(dpago.id) from detalle_pagos dpago where dpago.pago_id=pagos.id and dpago.estado in (1) )>1,'V','I' )  ,
                                IF ( (select count(ppedidos.id) from pago_pedidos ppedidos where ppedidos.pago_id=pagos.id and ppedidos.estado in (1) ) >1,'V','I' ),
                                '-',pagos.id) 
                            ELSE concat('PAG',u.identificador,'-',
                                IF ( (select count(dpago.id) from detalle_pagos dpago where dpago.pago_id=pagos.id and dpago.estado in (1) )>1,'V','I' )  ,
                                IF ( (select count(ppedidos.id) from pago_pedidos ppedidos where ppedidos.pago_id=pagos.id and ppedidos.estado in (1) ) >1,'V','I' ),
                                '-',pagos.id) END) AS id2"),
                        'u.identificador as users',
                        'c.celular',
                        'pagos.observacion',                        
                        'pagos.total_cobro',
                        'pagos.condicion',
                        DB::raw('(select DATE_FORMAT( MIN(dpa.fecha), "%d/%m/%Y %H:%i:%s")   from detalle_pagos dpa where dpa.pago_id=pagos.id and dpa.estado=1) as fecha'),
                        DB::raw('(select UNIX_TIMESTAMP(MIN(dpa.fecha))   from detalle_pagos dpa where dpa.pago_id=pagos.id and dpa.estado=1) as fecha_timestamp'),
                        DB::raw(" (select count(dpago.id) from detalle_pagos dpago where dpago.pago_id=pagos.id and dpago.estado in (1) ) as cantidad_voucher "),
                        DB::raw(" (select count(ppedidos.id) from pago_pedidos ppedidos where ppedidos.pago_id=pagos.id and ppedidos.estado in (1)  ) as cantidad_pedido "),
                        DB::raw(" ( select GROUP_CONCAT(ppp.codigo) from pago_pedidos ped inner join pedidos ppp on ped.pedido_id =ppp.id where pagos.id=ped.pago_id and ped.estado=1 and ppp.estado=1 and ped.pagado in (1,2)) as codigos "),
                        DB::raw(" (select sum(ped2.abono) from pago_pedidos ped2 where ped2.pago_id =pagos.id and ped2.estado=1 and ped2.pagado in (1,2) ) as total_pago ")   
                        )
                ->whereIn('pagos.condicion', ['ABONADO'])
                ->where('pagos.estado', '1')              
                ->get();                
        }else{
            $pagos = Pago::join('users as u', 'pagos.user_id', 'u.id')
            ->join('clientes as c', 'pagos.cliente_id', 'c.id')
            ->select('pagos.id as id',
                    'u.identificador as users',
                    'c.celular',
                    'pagos.observacion',                        
                    'pagos.total_cobro',
                    'pagos.condicion',
                    DB::raw('(select DATE_FORMAT( MIN(dpa.fecha), "%d/%m/%Y %H:%i:%s")   from detalle_pagos dpa where dpa.pago_id=pagos.id and dpa.estado=1) as fecha'),
                    DB::raw('(select UNIX_TIMESTAMP(MIN(dpa.fecha))   from detalle_pagos dpa where dpa.pago_id=pagos.id and dpa.estado=1) as fecha_timestamp'),
                    DB::raw(" (select count(dpago.id) from detalle_pagos dpago where dpago.pago_id=pagos.id and dpago.estado in (1) ) as cantidad_voucher "),
                    DB::raw(" (select count(ppedidos.id) from pago_pedidos ppedidos where ppedidos.pago_id=pagos.id and ppedidos.estado in (1)  ) as cantidad_pedido "),
                    DB::raw(" ( select GROUP_CONCAT(ppp.codigo) from pago_pedidos ped inner join pedidos ppp on ped.pedido_id =ppp.id where pagos.id=ped.pago_id and ped.estado=1 and ppp.estado=1 and ped.pagado in (1,2)) as codigos "),
                    DB::raw(" (select sum(ped2.abono) from pago_pedidos ped2 where ped2.pago_id =pagos.id and ped2.estado=1 and ped2.pagado in (1,2) ) as total_pago ")   
                    )
            ->where('pagos.user_id',$request->asesores) 
            ->whereIn('pagos.condicion', ['ABONADO'])
            ->where('pagos.estado', '1') 
            ->get(); 
        }
        
        return Datatables::of($pagos)
            ->addIndexColumn()
            ->addColumn('action', function($pago){     
                $btn='';
                if(Auth::user()->rol == "Administrador"){
                    $btn=$btn.'<a href="'.route('pagos.show', $pago['id']).'" class="btn btn-info btn-sm">Ver</a>';
                    //$btn=$btn.'<a href="'.route('administracion.revisar', $pago).'" class="btn btn-success btn-sm">Editar</a>';                    
                    $btn = $btn.'<a href="" data-target="#modal-desabonar" data-toggle="modal" data-desabonar="'.$pago['id'].'" data-pago="'.$pago['id2'].'"><button class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i> Desabonar</button></a>';
                }
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    

}