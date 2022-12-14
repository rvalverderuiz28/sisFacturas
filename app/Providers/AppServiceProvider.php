<?php

namespace App\Providers;

use App\View\Components\common\BsProgressbar;
use App\View\Components\dashboard\graficos\borras\PedidosPorDia;
use App\View\Components\dashboard\graficos\CobranzasMesesProgressBar;
use App\View\Components\dashboard\graficos\GraficoMetaCobranzas;
use App\View\Components\dashboard\graficos\GraficoMetasDelMes;
use App\View\Components\dashboard\graficos\GraficoPedidoCobranzasDelDia;
use App\View\Components\dashboard\graficos\GraficoPedidosAtendidoAnulados;
use App\View\Components\dashboard\graficos\GraficoPedidosMetaProgress;
use App\View\Components\dashboard\graficos\PedidosMesCountProgressBar;
use App\View\Components\dashboard\graficos\PedidosAsignadosProgressBar;
use App\View\Components\dashboard\graficos\QtyPedidoFisicoElectronicos;
use App\View\Components\dashboard\graficos\TopClientesPedidos;
use App\View\Components\dashboard\tablas\FisElecJefeOperaciones;
use Carbon\Carbon;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        \Blade::component('grafico-pedidos-mes-count-progress-bar', PedidosMesCountProgressBar::class);
        \Blade::component('grafico-meta-pedidos-progress-bar', PedidosAsignadosProgressBar::class);
        \Blade::component('grafico-pedidos-por-dia', PedidosPorDia::class);
        \Blade::component('grafico-meta_cobranzas', GraficoMetaCobranzas::class);
        \Blade::component('grafico-pedidos-meta-progress', GraficoPedidosMetaProgress::class);
        \Blade::component('grafico-top-clientes-pedidos', TopClientesPedidos::class);
        \Blade::component('grafico-metas-mes', GraficoMetasDelMes::class);
        \Blade::component('bs-progressbar', BsProgressbar::class);
        \Blade::component('grafico-pedidos-elect-fisico', QtyPedidoFisicoElectronicos::class);
        \Blade::component('tabla-jef-operaciones-fis-elect', FisElecJefeOperaciones::class);

        \Blade::component('grafico-pedidos-atendidos-anulados', GraficoPedidosAtendidoAnulados::class);

        \Blade::component('grafico-cobranzas-meses-progressbar', CobranzasMesesProgressBar::class);

        \Blade::component('grafico-pedido_cobranzas-del-dia', GraficoPedidoCobranzasDelDia::class);

        Carbon::setUTF8(true);
        Carbon::setLocale(config('app.locale'));
        setlocale(LC_ALL, 'es_MX', 'es', 'ES', 'es_MX.utf8');
    }
}
