<?php

namespace App\View\Components\dashboard\graficos;

use App\Abstracts\Widgets;
use App\Models\Pedido;
use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class CobranzasMesesProgressBar extends Widgets
{
    public $progressData = [];

    public $general = [];


    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        $currentDate = $this->startDate->clone();
        $this->generateDataByMonth($currentDate);
        $title = $this->getDateTitle();
        if (\auth()->user()->rol == User::ROL_ASESOR) {
            $this->progressData = [];
        }
        return view('components.dashboard.graficos.cobranzas-meses-progress-bar', compact('title'));
    }

    public function generateDataByMonth(CarbonInterface $date)
    {
        if (auth()->user()->rol == User::ROL_LLAMADAS) {//HASTA MAÑANA
            $id = auth()->user()->id;
            $asesores = User::rolAsesor()->where('llamada', '=', $id)->get();
        } else {
            $encargado = null;
            if (auth()->user()->rol == User::ROL_ENCARGADO) {
                $encargado = auth()->user()->id;
            }
            $asesores = User::query()
                ->activo()
                ->rolAsesor()
                ->when($encargado != null, function ($query) use ($encargado) {
                    return $query->where('supervisor', '=', $encargado);
                })
                ->get();
        }

        $progressData = [];
        $asesoresIdentificadores = [];
        $asesoresNames = [];
        foreach ($asesores as $asesor) {
            if (auth()->user()->rol != User::ROL_ADMIN
                && auth()->user()->rol != User::ROL_JEFE_LLAMADAS//HASTA MAÑANA
                && auth()->user()->rol != User::ROL_LLAMADAS) {//HASTA MAÑANA
                if (auth()->user()->rol != User::ROL_ENCARGADO) {
                    if (auth()->user()->id != $asesor->id) {
                        continue;
                    }
                } else {
                    if (auth()->user()->id != $asesor->supervisor) {
                        continue;
                    }
                }
            }
            if (!isset($asesoresIdentificadores[$asesor->identificador])) {
                $asesoresIdentificadores[$asesor->identificador] = [];
                $asesoresNames[$asesor->identificador] = [];
            }
            $asesoresIdentificadores[$asesor->identificador][] = $asesor->id;
            $asesoresNames[$asesor->identificador][] = explode(" ", $asesor->name)[0];
        }

        foreach ($asesoresIdentificadores as $identificador => $ids) {
            $limit = 4;
            $currentCount = 0;
            while ($currentCount < $limit) {
                $dateCurrent = $date->clone()->addMonths($currentCount);
                $progressData[$identificador][$dateCurrent->format('m-Y')] = $this->getDataProgress($identificador, $ids, collect($asesoresNames[$identificador])->first(), $dateCurrent);
                $currentCount++;
            }
        }

        $alldata = [];
        $activos = [];
        $pagados = [];
        foreach ($progressData as $dates) {
            foreach ($dates as $datestr => $item) {
                $all = data_get($item, 'activos');
                $pay = data_get($item, 'pagados');
                $activos[$datestr][] = $all;
                $pagados[$datestr][] = $pay;
            }
        }
        foreach ($activos as $datestr => $list) {
            $all = collect($list)->sum();
            $pay = collect($pagados[$datestr])->sum();
            if ($all > 0) {
                $p = round(($pay / $all) * 100,2);
            } else {
                $p = 0;
            }
            if (auth()->user()->rol == User::ROL_ADMIN) {
                $name = 'General';
            } else {
                $name = auth()->user()->name;
            }
            $alldata[$datestr] = [
                "code" => '',
                "name" => $name,
                "progress" => $p,
                "activos" => $all,
                "pagados" => $pay,
            ];
        }

        $this->progressData = $progressData;
        $this->general = $alldata;

    }

    public function getDataProgress($identificador, $ids, $name, CarbonInterface $date)
    {
        $all = $this->applyFilter(Pedido::query()->whereIn('user_id', $ids)->activo(), 'created_at', $date)->count();

        $pay = \DB::table($this->applyFilter(
            Pedido::query()
                ->select(['pedidos.id', \DB::raw('sum(detalle_pedidos.total) AS total'), \DB::raw('sum(pago_pedidos.abono) as abonado')])
                ->activo()
                ->pagados()
                ->join('detalle_pedidos', 'detalle_pedidos.pedido_id', '=', 'pedidos.id')
                ->join('pago_pedidos', 'pago_pedidos.pedido_id', '=', 'pedidos.id')
                ->whereIn('user_id', $ids)
                ->where('detalle_pedidos.estado', '=', 1)
                ->where('pago_pedidos.estado', '=', 1)
                ->groupBy('pedidos.id'),
            ['pedidos.created_at', 'pago_pedidos.created_at'],
            $date)
        )->whereRaw('total=abonado')->count();

        if ($all > 0) {
            $p = round(($pay / $all) * 100,2);
        } else {
            $p = 0;
        }
        return [
            "identificador" => $identificador,
            "code" => "Asesor {$identificador}",
            "name" => $name,
            "activos" => $all,
            "pagados" => $pay,
            "progress" => $p,
            "date" => $date->clone(),
        ];
    }
}