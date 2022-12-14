<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    //condicion del pedido
    const POR_ATENDER = 'POR ATENDER';//1
    const EN_PROCESO_ATENCION = 'EN PROCESO ATENCION';//2
    const ATENDIDO = 'ATENDIDO';//3
    const ANULADO = 'ANULADO';//4

    //envio
    const ENVIO_CONFIRMAR_RECEPCION = '1';//ENVIADO CONFIRMAR RECEPCION
    const ENVIO_RECIBIDO = '2';//ENVIADO RECIBIDO

    //condicion de envio en cadena
    const PENDIENTE_DE_ENVIO = 'PENDIENTE DE ENVIO';//1
    const EN_REPARTO = 'EN REPARTO';//1
    const ENTREGADO = 'ENTREGADO';

    //condicion de envio en entero
    const PENDIENTE_DE_ENVIO_CODE=1;
    const EN_REPARTO_CODE=2;
    const ENTREGADO_CODE=3;


    /* condicion de pedidos relacion */

    public static $estadosCondicion = [
        'ANULADO' => 0,
        'POR ATENDER' => 1,
        'EN PROCESO ATENCIÓN' => 2,
        'ATENDIDO' => 3,
    ];


    /* condicion de envios relacion */

    public static $estadosCondicionEnvio = [
        'ANULADO' => 0,
        'PENDIENTE DE ENVÍO' => 1,
        'EN REPARTO' => 2,
        'ENTREGADO' => 3,
    ];

    

    protected $guarded = ['id'];

    /* public function user()
    {
        return $this->belongsTo('App\Models\User');
    } */

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
}