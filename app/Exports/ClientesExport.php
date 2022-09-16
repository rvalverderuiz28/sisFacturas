<?php

namespace App\Exports;

use App\Models\Cliente;
use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;

class ClientesExport implements FromView
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function view(): View
    {
        $dateM = Carbon::now()->format('m');
        $dateY = Carbon::now()->format('Y');

        $clientes1 = Cliente::
        join('users as u', 'clientes.user_id', 'u.id')
        ->join('pedidos as p', 'clientes.id', 'p.cliente_id')
        ->select('clientes.id',
                'clientes.nombre',
                'clientes.celular', 
                'clientes.estado', 
                'u.name as users',
                'clientes.provincia',
                'clientes.distrito',
                'clientes.direccion',
                'clientes.referencia',
                'clientes.dni',
                'clientes.deuda',
                DB::raw('MAX(p.created_at) as fecha'),
                DB::raw('MAX(DATE_FORMAT(p.created_at, "%d")) as dia'),
                DB::raw('MAX(DATE_FORMAT(p.created_at, "%m")) as mes'),
                DB::raw('MAX(DATE_FORMAT(p.created_at, "%Y")) as anio')
                )
        ->where('clientes.estado','1')
        ->where('clientes.tipo','1')
        ->where('clientes.pidio','1')
        ->groupBy(
            'clientes.id',
            'clientes.nombre',
            'clientes.celular', 
            'clientes.estado', 
            'u.name',
            'clientes.provincia',
            'clientes.distrito',
            'clientes.direccion',
            'clientes.referencia',
            'clientes.dni',
            'clientes.deuda',
        )
        ->get();

        $clientes2 = Cliente::
        join('users as u', 'clientes.user_id', 'u.id')
        ->select('clientes.id',
                'clientes.nombre',
                'clientes.celular', 
                'clientes.estado', 
                'u.name as users',
                'clientes.provincia',
                'clientes.distrito',
                'clientes.direccion',
                'clientes.referencia',
                'clientes.dni',
                'clientes.deuda'
                )
        ->where('clientes.estado','1')
        ->where('clientes.tipo','1')
        ->where('clientes.pidio','0')
        ->groupBy(
            'clientes.id',
            'clientes.nombre',
            'clientes.celular', 
            'clientes.estado', 
            'u.name',
            'clientes.provincia',
            'clientes.distrito',
            'clientes.direccion',
            'clientes.referencia',
            'clientes.dni',
            'clientes.deuda',
        )
        ->get();

        return view('clientes.excel.index', compact('clientes1', 'clientes2', 'dateM', 'dateY'));
    }
}