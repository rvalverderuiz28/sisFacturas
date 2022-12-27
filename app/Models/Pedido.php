<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    use HasFactory;

    /**************
     * CONSTANTES PEDIDO
     */
    const POR_ATENDER = 'POR ATENDER PEDIDO';//1
    const EN_PROCESO_ATENCION = 'EN PROCESO ATENCION';//2
    const ATENDIDO = 'ATENDIDO';//3
    const ANULADO = 'ANULADO';//4
    const PENDIENTE_ANULACION = 'PENDIENTE ANULACION';//

    /**************
     * CONSTANTES PEDIDO NUMERICO
     */
    const POR_ATENDER_INT = 1;
    const EN_PROCESO_ATENCION_INT = 2;
    const ATENDIDO_INT = 3;
    const ANULADO_INT = 4;

    /**************
     * CONSTANTES CONDICION ENVIO
     */
    const POR_ATENDER_PEDIDO = 'POR ATENDER - OPE'; // POR ATENDER - OPE
    const INCOMPLETO = 'INCOMPLETO';
    const ATENDIDO_OP = 'ATENDIDO - OPE'; // ATENDIDO - OPE
    const JEFE_OP = 'JEFE - OPE'; // JEFE - OPE
    const REPARTO_COURIER = 'REPARTO - COURIER';
    const SEG_PROVINCIA = 'SEGUIMIENTO PROVINCIA - COURIER';
    const ENTREGADO_CLIENTE = 'ENTREGADO - CLIENTE';
    const JEFE_OP_CONF = 'JEFE CONF - OPE';
    const RECEPCION_COURIER = 'RECEPCION - COURIER';
    const ENTREGADO_SIN_SOBRE = 'ENTREGADO SIN SOBRE - OPE';

    const CONFIRMACION_SIN_SOBRE = 'ENTREGADO SIN SOBRE - CLIENTE';
    const MOTORIZADO = 'MOTORIZADO';

    /**************
     * CONSTANTES CONDICION ENVIO NUMERICO
     */
    const POR_ATENDER_PEDIDO_INT = 1;
    const INCOMPLETO_INT = 2;
    const ATENDIDO_OP_INT = 3;
    const BANCARIZACION_INT = 4;
    const JEFE_OP_INT = 5;
    const COURIER_INT = 6;
    const SOBRE_ENVIAR_INT = 7;
    const REPARTO_COURIER_INT = 8;
    const SEG_PROVINCIA_INT = 9;
    const ENTREGADO_CLIENTE_INT = 10;
    const JEFE_OP_CONF_INT = 11;
    const RECEPCION_COURIER_INT = 12;
    const ENTREGADO_SIN_SOBRE_INT = 13;

    const CONFIRMACION_SIN_SOBRE_INT = 14;

    const MOTORIZADO_INT = 15;

    //envio
    const ENVIO_CONFIRMAR_RECEPCION = '1';//ENVIADO CONFIRMAR RECEPCION
    const ENVIO_RECIBIDO = '2';//ENVIADO RECIBIDO

    //condicion de envio en cadena
    const PENDIENTE_DE_ENVIO = 'PENDIENTE DE ENVIO';//1


    //condicion de envio en entero
    const PENDIENTE_DE_ENVIO_CODE = 1;
    const EN_REPARTO_CODE = 2;
    const ENTREGADO_CODE = 3;

    const PORATENDDER = 1;


    /* relacion de conciones de envio y enteros */

    public static $estadosCondicion = [
        'ANULADO' => 4,
        'POR ATENDER' => 1,
        'EN PROCESO ATENCION' => 2,
        'ATENDIDO' => 3,
    ];

    public static $estadosCondicionCode = [
        4 => 'ANULADO',
        1 => 'POR ATENDER',
        2 => 'EN PROCESOS DE ATENCION',
        3 => 'ATENDIDO',
    ];

    /******************
     * CONDICION DE ENVIO
     */

    public static $estadosCondicionEnvio = [
        'POR ATENDER' => 1,
        'INCOMPLETO' => 2,
        'ATENDIDO OP' => 3,
        'BANCARIZACION' => 4,
        'JEFE_OP' => 5,
        'COURIER' => 6,
        'SOBRE_ENVIAR' => 7,
        'EN_REPARTO' => 8,
        'SEG_PROVINCIA' => 9,
        'ENTREGADO' => 10,
        'JEFE_OP_CONF_INT' => 11,
        'MOTORIZADO' => 15,
    ];

    public static $estadosCondicionEnvioCode = [
        1 => 'POR ATENDER',
        2 => 'INCOMPLETO',
        3 => 'PDF',
        4 => 'BANCARIZACION',
        5 => 'JEFE_OP',
        6 => 'COURIER',
        7 => 'SOBRE_ENVIAR',
        8 => 'EN_REPARTO',
        9 => 'SEG_PROVINCIA',
        10 => 'ENTREGADO',
        11 => 'JEFE_OP_CONF_INT',
        12 => 'RECEPCION_COURIER',
        13 => 'ENTREGADO_SIN_SOBRE',
        14 => 'CONFIRMACION_SIN_SOBRE',
        15 => 'MOTORIZADO'
    ];


    protected $guarded = ['id'];

    protected $dates = [
        'fecha_anulacion',
        'fecha_anulacion_confirm',
        'fecha_anulacion_denegada',
    ];

    /* public function user()
    {
        return $this->belongsTo('App\Models\User');
    } */

    public function cliente()
    {
        return $this->belongsTo(Cliente::class);
    }
    public function imagenAtencion()
    {
        return $this->hasMany(ImagenAtencion::class,'pedido_id');
    }

    public function detallePedidos()
    {
        return $this->hasMany(DetallePedido::class);
    }

    public function pagoPedidos()
    {
        return $this->hasMany(PagoPedido::class);
    }

    public function getIdCodeAttribute()
    {
        return generate_correlativo('PED', $this->id, 4);
    }

    public static function generateIdCode($id)
    {
        return generate_correlativo('PED', $id, 4);
    }

    public function notasCreditoFiles()
    {
        $data = setting("pedido." . $this->id . ".nota_credito_file");
        if (is_array($data)) {
            return $data;
        }
        return [];
    }

    public function adjuntosFiles()
    {
        $data = setting("pedido." . $this->id . ".adjuntos_file");
        if (is_array($data)) {
            return $data;
        }
        return [];
    }

    public function scopeActivo($query, $status = '1')
    {
        return $query->where('pedidos.estado', '=', $status);
    }

    public function scopePagados($query)
    {
        return $query->where('pedidos.pago', '=', 1)->where('pedidos.pagado', '=', 2);
    }

    public function scopeNoPagados($query)
    {
        return $query->whereIn('pedidos.pago', [0, 1])//no hay pago
        ->whereIn('pedidos.pagado', [0, 1]);//no hay pago o adelanto
    }

    public function scopeAtendidos($query)
    {
        return $query->where($this->qualifyColumn('condicion_code'), '=', self::ATENDIDO_INT);
    }

    public function scopePorAtender($query)
    {
        return $query->where($this->qualifyColumn('condicion_code'), '=', self::POR_ATENDER_INT);
    }
    public function scopePoratenderestatus($query)
    {
        return $query->whereIn($this->qualifyColumn('condicion_code'), '=', [self::POR_ATENDER_INT,self::EN_PROCESO_ATENCION_INT ]);
    }

    public function scopeCurrentUser($query)
    {
        return $query->where($this->qualifyColumn('user_id'), '=', auth()->id());
    }

    public function scopeNoPendingAnulation($query)
    {
        return $query->where($this->qualifyColumn('pendiente_anulacion'), '<>', '1');
    }

    /**
     * @param Builder $query
     * @param $roles
     */
    public function scopeSegunRolUsuario($query, $roles = [])
    {
        if (in_array(User::ROL_ADMIN, $roles)) {
            if (auth()->user()->rol == User::ROL_ADMIN) {
                return $query;
            }
        }
        if (in_array(User::ROL_ENCARGADO, $roles)) {
            if (auth()->user()->rol == User::ROL_ENCARGADO) {
                return $query->whereIn($this->qualifyColumn('user_id'), User::query()->select('id')->activo()->where('users.supervisor', auth()->id()));
            }
        }
        if (in_array(User::ROL_ASESOR, $roles)) {
            if (auth()->user()->rol == User::ROL_ASESOR) {
                return $query->where($this->qualifyColumn('user_id'), '=', auth()->id());
            }
        }
        return $query;
    }
}
