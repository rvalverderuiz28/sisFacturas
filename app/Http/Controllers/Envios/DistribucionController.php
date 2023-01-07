<?php

namespace App\Http\Controllers\Envios;

use App\Http\Controllers\Controller;
use App\Models\Departamento;
use App\Models\DetallePedido;
use App\Models\DireccionGrupo;
use App\Models\Distrito;
use App\Models\GrupoPedido;
use App\Models\Pedido;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class DistribucionController extends Controller
{
    public function index()
    {
        $ver_botones_accion = 1;

        if (Auth::user()->rol == "Asesor") {
            $ver_botones_accion = 0;
        } else if (Auth::user()->rol == "Super asesor") {
            $ver_botones_accion = 0;
        } else if (Auth::user()->rol == "Encargado") {
            $ver_botones_accion = 1;
        } else {
            $ver_botones_accion = 1;
        }


        $_pedidos = Pedido::join('clientes as c', 'pedidos.cliente_id', 'c.id')
            ->join('users as u', 'pedidos.user_id', 'u.id')
            ->join('detalle_pedidos as dp', 'pedidos.id', 'dp.pedido_id')
            ->select(
                DB::raw("COUNT(u.identificador) AS total, u.identificador ")
            )
            ->where('pedidos.estado', '1')
            ->whereIn('pedidos.condicion_envio_code', [Pedido::RECEPCION_COURIER_INT])
            ->where('dp.estado', '1')
            ->groupBy('u.identificador');


        $distritos = Distrito::whereIn('provincia', ['LIMA', 'CALLAO'])
            ->where('estado', '1')
            ->WhereNotIn('distrito', ['CHACLACAYO', 'CIENEGUILLA', 'LURIN', 'PACHACAMAC', 'PUCUSANA', 'PUNTA HERMOSA', 'PUNTA NEGRA', 'SAN BARTOLO', 'SANTA MARIA DEL MAR'])
            ->pluck('distrito', 'distrito');

        $departamento = Departamento::where('estado', "1")
            ->pluck('departamento', 'departamento');

        $superasesor = User::where('rol', 'Super asesor')->count();

        $_pedidos = $_pedidos->get();
        $motorizados = User::query()->where('rol', '=', 'MOTORIZADO')->whereNotNull('zona')->get();

        return view('envios.distribuirsobres', compact('superasesor', 'motorizados', 'ver_botones_accion', 'distritos', 'departamento', '_pedidos'));
    }

    public function datatable(Request $request)
    {
        $query = GrupoPedido::query()->with('pedidos')
            ->select([
                'grupo_pedidos.*',
                'codigos' => DB::table('grupo_pedido_items')->selectRaw('GROUP_CONCAT(grupo_pedido_items.codigo)')->whereRaw('grupo_pedido_items.grupo_pedido_id=grupo_pedidos.id'),
                'productos' => DB::table('grupo_pedido_items')->selectRaw('GROUP_CONCAT(grupo_pedido_items.razon_social)')->whereRaw('grupo_pedido_items.grupo_pedido_id=grupo_pedidos.id'),
            ]);
        /*
        $pedidoQuery = Pedido::join('clientes as c', 'pedidos.cliente_id', 'c.id')
            ->join('users as u', 'pedidos.user_id', 'u.id')
            ->join('detalle_pedidos as dp', 'pedidos.id', 'dp.pedido_id')
            ->select([
                'pedidos.*',
                'u.identificador as users',
                'dp.codigo as codigos',
                'dp.nombre_empresa as empresas',
                'dp.total as total',
                'dp.envio_doc',
                'dp.fecha_envio_doc',
                'dp.cant_compro',
                'dp.fecha_envio_doc_fis',
                'dp.foto1',
                'dp.foto2',
                'dp.fecha_recepcion',
                DB::raw("DATEDIFF(DATE(NOW()), DATE(pedidos.created_at)) AS dias")
            ])
            ->where('pedidos.estado', '1')
            ->whereIn('pedidos.condicion_envio_code', [Pedido::RECEPCION_COURIER_INT])
            ->where('dp.estado', '1')
            ->conDireccionEnvio()
            ->sinZonaAsignadaEnvio();
*/
        //add_query_filtros_por_roles_pedidos($pedidoQuery, 'u.identificador');

        $motorizados = User::query()->where('rol', '=', 'MOTORIZADO')->whereNotNull('zona')->get();
        $color_zones = [];
        $color_zones['NORTE'] = 'warning';
        $color_zones['CENTRO'] = 'info';
        $color_zones['SUR'] = 'dark';
        if (is_array($request->exclude_ids) && count($request->exclude_ids) > 0) {
            $query->whereNotIn('id', $request->exclude_ids);
        }
        return datatables()->eloquent($query)
            ->addColumn('codigos', function ($pedido) {
                return collect(explode(',', $pedido->codigos))->map(function ($codigo, $index) {
                    return ($index + 1) . ") <b>" . $codigo . "</b>";
                })->join('<hr class="my-1"><br>');
            })
            ->addColumn('productos', function ($pedido) {
                return collect(explode(',', $pedido->productos))->map(function ($codigo, $index) {
                    return ($index + 1) . ")  <b>" . $codigo . "</b>";
                })->join('<hr class="my-1"><br>');
            })
            ->addColumn('condicion_envio', function ($pedido) {
                $badge_estado = '';
                $color = Pedido::getColorByCondicionEnvio(Pedido::RECEPCION_COURIER);
                $badge_estado .= '<span class="badge badge-success py-2" style="background-color: ' . $color . '!important;">' . Pedido::RECEPCION_COURIER . '</span>';
                return $badge_estado;
            })
            ->addColumn('action', function ($pedido) use ($motorizados, $color_zones) {
                $btn = [];
                foreach ($motorizados as $motorizado) {
                    $btn[] = "<div class='text-center p-1'><button data-zona='$motorizado->zona' data-elTable='#tablaPrincipal" . Str::upper($motorizado->zona) . "' data-ajax-post='" . route('envios.distribuirsobres.asignarzona', ['grupo_pedido_id' => $pedido->id, 'motorizado_id' => $motorizado->id, 'zona' => Str::upper($motorizado->zona)]) . "'
 class='add-row-datatable  btn btn-" . ($color_zones[Str::upper($motorizado->zona)] ?? 'info') . " btn-sm btn-block my-0' type='button'>
<span class='spinner-border spinner-border-sm' role='status' aria-hidden='true' style='display: none'></span>
  <span class='sr-only' style='display: none'>" . (Str::upper($motorizado->zona)) . "</span>" . (Str::upper($motorizado->zona)) . "</button></div>";
                }
                if (count($motorizados) == 0) {
                    $btn[] = '<li class="list-group-item alert alert-warning p-8 text-center mb-0">No hay motorizados registrados</li>';
                }
                return "<ul class='d-flex'>" . join('', $btn) . "</ul>";
            })
            ->rawColumns(['action', 'condicion_envio', 'productos', 'codigos'])
            ->make(true);

    }

    public function asignarZona(Request $request)
    {
        $this->validate($request, [
            'grupo_pedido_id' => 'required',
            'motorizado_id' => 'required',
            'zona' => 'required',
        ]);
        $pedidoGrupo = GrupoPedido::query()->findOrFail($request->grupo_pedido_id);

        /*$pedido = Pedido::query()->findOrFail($request->pedido_id);
        if ($request->has('revertir_asignar_zona')) {
            $pedido->update([
                'env_zona_asignada' => null
            ]);
        } else {
            $pedido->update([
                'env_zona_asignada' => $request->zona
            ]);
        }*/
        return response()->json($pedidoGrupo);
    }

    public function agrupar(Request $request)
    {
        $this->validate($request, [
            'zona' => ['required', Rule::in(['NORTE', 'SUR', 'CENTRO'])],
            'motorizado_id' => 'required',
            'groups' => 'required|array',
        ]);
        $groups = GrupoPedido::query()->with(['pedidos'])->whereIn('id', $request->groups)->get();

        $zona = $request->get('zona');

        foreach ($groups as $grupo) {
            $pedidos = $grupo->pedidos;
            $firstProduct = collect($pedidos)->first();
            $cliente = $firstProduct->cliente;
            $lista_codigos = collect($pedidos)->pluck('codigo')->join(',');
            $lista_productos = DetallePedido::wherein("pedido_id", collect($pedidos)->pluck('id'))->pluck('nombre_empresa')->join(',');

            $groupData = [
                'condicion_envio_code' => Pedido::REPARTO_COURIER_INT,//RECEPCION CURRIER
                'condicion_envio' => Pedido::REPARTO_COURIER,//RECEPCION CURRIER
                'producto' => $lista_productos,
                'distribucion' => $zona,
                'destino' => $firstProduct->env_destino,
                'direccion' => $firstProduct->env_direccion,
                'fecha_recepcion' => now(),
                'codigos' => $lista_codigos,

                'estado' => '1',

                'cliente_id' => $cliente->id,
                'user_id' => $firstProduct->user_id,

                'nombre' => $firstProduct->env_nombre_cliente_recibe,
                'celular' => $firstProduct->env_celular_cliente_recibe,

                'nombre_cliente' => $cliente->nombre,
                'celular_cliente' => $cliente->celular,
                'icelular_cliente' => $cliente->icelular,

                'distrito' => $firstProduct->env_distrito,
                'referencia' => $firstProduct->env_referencia,
                'observacion' => $firstProduct->env_observacion,
                'cantidad' => count($pedidos),
                'motorizado_id' => $request->motorizado_id,
            ];

            if ($request->get("visualizar") == '1') {
                $grupos[] = $groupData;
            } else {
                $direcciongrupo = DireccionGrupo::create($groupData);
                $grupos[] = $direcciongrupo->refresh();
                Pedido::whereIn('id', collect($pedidos)->pluck('id'))->update([
                    'env_zona_asignada' => null,
                    'estado_ruta' => '1',
                    'condicion_envio_code' => Pedido::REPARTO_COURIER_INT,
                    'condicion_envio' => Pedido::REPARTO_COURIER,
                    'direccion_grupo' => $direcciongrupo->id,
                ]);
                $grupo->delete();
            }
        }
        return $grupos;
    }

    public function desagrupar(Request $request)
    {
        $grupo = GrupoPedido::query()->findOrFail($request->grupo_id);
        $pedido = Pedido::join('clientes as c', 'pedidos.cliente_id', 'c.id')
            ->select([
                'pedidos.*',
                'detalle_pedidos.nombre_empresa'
            ])
            ->join('detalle_pedidos', 'pedidos.id', 'detalle_pedidos.pedido_id')
            ->activo()
            ->where('detalle_pedidos.estado', '1')
            ->findOrFail($request->pedido_id);

        DB::beginTransaction();
        $detach = $grupo->pedidos()->detach([$pedido->id]);

        if ($grupo->pedidos()->count() == 0) {
            $grupo->delete();
            return response()->json([
                'data' => null,
                'pedido' => $pedido,
                'detach' => $detach,
                'success' => true
            ]);
        }

        $grupoPedido = GrupoPedido::creteGroupByPedido($pedido, true);
        $grupoPedido->pedidos()->attach($pedido->id, [
            'razon_social' => $pedido->nombre_empresa,
            'codigo' => $pedido->codigo,
        ]);

        DB::commit();
        return response()->json([
            'data' => $grupo->refresh()->load(['pedidos']),
            'grupo2' => $grupoPedido,
            'pedido' => $pedido,
            'detach' => $detach,
            'success' => true
        ]);
    }

}