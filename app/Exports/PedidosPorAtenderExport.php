<?php

namespace App\Exports;

use App\Models\Pedido;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class PedidosPorAtenderExport implements FromView, ShouldAutoSize
{
    use Exportable;
    
    public function pedidos($request) {
        $mirol=Auth::user()->rol;

        //$pedidos = null;
        //$pedidos = Pedido::where('estado', '1');
        //$pedidos = $pedidos->where('banco','like','%'.'a'.'%');
        
        if($mirol=="Operario")
        {
            $asesores = User::where('users.rol', 'Asesor')
                -> where('users.estado', '1')
                -> Where('users.operario',Auth::user()->id)
                ->select(
                    DB::raw("users.identificador as identificador")
                )
                ->pluck('users.identificador');

            $pedidos = Pedido::join('clientes as c', 'pedidos.cliente_id', 'c.id')
                ->join('users as u', 'pedidos.user_id', 'u.id')
                ->join('detalle_pedidos as dp', 'pedidos.id', 'dp.pedido_id')
                ->select(
                    'pedidos.id',
                    'u.jefe as jefe',
                    'u.identificador as id_asesor',
                    'dp.codigo as codigo_pedido',
                    'dp.nombre_empresa as empresa',
                    'dp.ruc as ruc',
                    'dp.mes as mes',
                    'dp.tipo_banca as tipo',
                    'dp.cantidad as cantidad',
                    'u.operario as operario',
                    'dp.cant_compro as cant_doc',
                    'pedidos.condicion as estado_pedido',
                    DB::raw('DATE_FORMAT(pedidos.created_at, "%d/%m/%Y") as fecha_registro'),
                    DB::raw('DATE_FORMAT(dp.fecha_envio_doc, "%d/%m/%Y") as fecha_elaboracion'),
                    DB::raw('DATE_FORMAT(dp.fecha_recepcion, "%d/%m/%Y") as fecha_finalizacion')
                )
                ->where('pedidos.estado', '1')
                ->where('dp.estado', '1')
                ->WhereIn('pedidos.condicion',['POR ATENDER','EN PROCESO ATENCION'])
                //->where('pedidos.condicion', 'POR ATENDER')
                ->whereBetween(DB::raw('DATE(pedidos.created_at)'), [$request->desde, $request->hasta])
                ->WhereIn('u.identificador',$asesores)
                //->where('u.operario', Auth::user()->id)
                ->groupBy(
                    'pedidos.id',
                    'u.jefe',
                    'u.identificador',
                    'dp.codigo',
                    'dp.nombre_empresa',
                    'dp.ruc',
                    'dp.mes',
                    'dp.tipo_banca',
                    'dp.cantidad',
                    'u.operario',
                    'dp.cant_compro',
                    'pedidos.condicion',
                    'pedidos.created_at',
                    'dp.fecha_envio_doc',
                    'dp.fecha_recepcion'
                )
                ->orderBy('pedidos.created_at', 'DESC')
                ->get();

        }else if($mirol=="Jefe de operaciones")
        {
            $operarios = User::where('users.rol', 'Operario')
                -> where('users.estado', '1')
                -> where('users.jefe', Auth::user()->id)
                ->select(
                    DB::raw("users.id as id")
                )
                ->pluck('users.id');

            $asesores = User::where('users.rol', 'Asesor')
                -> where('users.estado', '1')
                ->WhereIn('users.operario',$operarios)
                ->select(
                    DB::raw("users.identificador as identificador")
                )
                ->pluck('users.identificador');

            $pedidos = Pedido::join('clientes as c', 'pedidos.cliente_id', 'c.id')
                ->join('users as u', 'pedidos.user_id', 'u.id')
                ->join('detalle_pedidos as dp', 'pedidos.id', 'dp.pedido_id')
                ->select(
                    'pedidos.id',
                    'u.jefe as jefe',
                    'u.identificador as id_asesor',
                    'dp.codigo as codigo_pedido',
                    'dp.nombre_empresa as empresa',
                    'dp.ruc as ruc',
                    'dp.mes as mes',
                    'dp.tipo_banca as tipo',
                    'dp.cantidad as cantidad',
                    'u.operario as operario',
                    'dp.cant_compro as cant_doc',
                    'pedidos.condicion as estado_pedido',
                    DB::raw('DATE_FORMAT(pedidos.created_at, "%d/%m/%Y") as fecha_registro'),
                    DB::raw('DATE_FORMAT(dp.fecha_envio_doc, "%d/%m/%Y") as fecha_elaboracion'),
                    DB::raw('DATE_FORMAT(dp.fecha_recepcion, "%d/%m/%Y") as fecha_finalizacion')
                )
                ->where('pedidos.estado', '1')
                ->where('dp.estado', '1')
                ->WhereIn('pedidos.condicion',['POR ATENDER','EN PROCESO ATENCION'])
                //->where('pedidos.condicion', 'POR ATENDER')
                ->whereBetween(DB::raw('DATE(pedidos.created_at)'), [$request->desde, $request->hasta])
                //->where('u.jefe', Auth::user()->id)
                ->WhereIn('u.identificador',$asesores)
                ->groupBy(
                    'pedidos.id',
                    'u.jefe',
                    'u.identificador',
                    'dp.codigo',
                    'dp.nombre_empresa',
                    'dp.ruc',
                    'dp.mes',
                    'dp.tipo_banca',
                    'dp.cantidad',
                    'u.operario',
                    'dp.cant_compro',
                    'pedidos.condicion',
                    'pedidos.created_at',
                    'dp.fecha_envio_doc',
                    'dp.fecha_recepcion'
                )
                ->orderBy('pedidos.created_at', 'DESC')
                ->get();


        }else{
            $pedidos = Pedido::join('clientes as c', 'pedidos.cliente_id', 'c.id')
            ->join('users as u', 'pedidos.user_id', 'u.id')
            ->join('detalle_pedidos as dp', 'pedidos.id', 'dp.pedido_id')
            ->select(
                'pedidos.id',
                'u.jefe as jefe',
                'u.identificador as id_asesor',
                'dp.codigo as codigo_pedido',
                'dp.nombre_empresa as empresa',
                'dp.ruc as ruc',
                'dp.mes as mes',
                'dp.tipo_banca as tipo',
                'dp.cantidad as cantidad',
                'u.operario as operario',
                'dp.cant_compro as cant_doc',
                'pedidos.condicion as estado_pedido',
                DB::raw('DATE_FORMAT(pedidos.created_at, "%d/%m/%Y") as fecha_registro'),
                DB::raw('DATE_FORMAT(dp.fecha_envio_doc, "%d/%m/%Y") as fecha_elaboracion'),
                DB::raw('DATE_FORMAT(dp.fecha_recepcion, "%d/%m/%Y") as fecha_finalizacion')
            )
            ->where('pedidos.estado', '1')
            ->where('dp.estado', '1')
            //->where('pedidos.condicion', 'POR ATENDER')
            ->WhereIn('pedidos.condicion',['POR ATENDER','EN PROCESO ATENCION'])
            ->whereBetween(DB::raw('DATE(pedidos.created_at)'), [$request->desde, $request->hasta])
            ->groupBy(
                'pedidos.id',
                'u.jefe',
                'u.identificador',
                'dp.codigo',
                'dp.nombre_empresa',
                'dp.ruc',
                'dp.mes',
                'dp.tipo_banca',
                'dp.cantidad',
                'u.operario',
                'dp.cant_compro',
                'pedidos.condicion',
                'pedidos.created_at',
                'dp.fecha_envio_doc',
                'dp.fecha_recepcion'
            )
            ->orderBy('pedidos.created_at', 'DESC')
            ->get();
        }

        

        

        $this->pedidos = $pedidos;
        return $this;
    }            

    public function view(): View {
        return view('pedidos.excel.pedidosporatender', [
            'pedidos'=> $this->pedidos
        ]);
    }
}