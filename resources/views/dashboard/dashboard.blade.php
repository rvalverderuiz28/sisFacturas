@extends('adminlte::page')
{{-- @extends('layouts.admin') --}}

@section('title', 'Dashboard')

@section('content_header')
  <div><h1>Dashboard</h1>
    <!-- Right navbar links -->
  </div>

  <script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>
  <!--ADMINISTRADOR-->
    <script type="text/javascript">
      google.charts.load('current', {
        'packages': ['bar']
      });
      google.charts.setOnLoadCallback(drawStuff);

      function drawStuff() {
        var data = new google.visualization.arrayToDataTable([
          ['Asesores', 'Pedidos'],
          @foreach ($pedidosxasesor as $vxa)
            ['{{ $vxa->users }}', {{ $vxa->pedidos }}],
          @endforeach
        ]);

        var options = {
          chart: {
            title: 'PEDIDOS DEL MES DE TODOS LOS ASESORES',
            subtitle: 'PEDIDO/ASESOR'
          }
        };

        var chart = new google.charts.Bar(document.getElementById('pedidosxasesor'));
        chart.draw(data, google.charts.Bar.convertOptions(options));
      };
    </script>
    <script type="text/javascript">
      google.charts.load('current', {
        'packages': ['bar']
      });
      google.charts.setOnLoadCallback(drawChart);

      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Clientes', 'Monto'],
          @foreach ($pagosxmes as $pxm)
            ['{{ $pxm->cliente }}', {{ $pxm->pagos }}],
          @endforeach
        ]);

        var options = {
          chart: {
            title: 'MONTO DE PEDIDO POR CLIENTE EN EL MES',
            subtitle: 'TOP 30',
          }
        };

        var chart = new google.charts.Bar(document.getElementById('pagosxmes'));
        chart.draw(data, google.charts.Bar.convertOptions(options));
      }
    </script>
    <script type="text/javascript">
      google.charts.load("current", {packages:["corechart"]});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Asesor', 'Pedidos por día'],
          @foreach($pedidosxasesorxdia as $pxad)
            ['{{$pxad->users}}', {{$pxad->pedidos}}],
          @endforeach
        ]);

        var options = {
          title: '',
          is3D: true,
        };

        var chart = new google.visualization.PieChart(document.getElementById('pedidosxasesorxdia'));
        chart.draw(data, options);
      }
    </script>
  <!--ENCARGADO-->
  <script type="text/javascript">
    google.charts.load('current', {
      'packages': ['bar']
    });
    google.charts.setOnLoadCallback(drawStuff);

    function drawStuff() {
      var data = new google.visualization.arrayToDataTable([
        ['Asesores', 'Pedidos'],
        @foreach ($pedidosxasesor_3meses_encargado as $vxa)
          ['{{ $vxa->users }} - {{ $vxa->fecha }}', {{ $vxa->pedidos }}],
        @endforeach
      ]);

      var options = {
        chart: {
          title: 'HISTORIAL DE PEDIDOS DE LOS ULTIMOS 3 MESES DE MIS ASESORES',
          subtitle: 'PEDIDO/ASESOR'
        }
      };

      var chart = new google.charts.Bar(document.getElementById('pedidosxasesor_3meses_encargado'));
      chart.draw(data, google.charts.Bar.convertOptions(options));
    };
  </script>

  <script type="text/javascript">
    google.charts.load('current', {
      'packages': ['bar']
    });
    google.charts.setOnLoadCallback(drawStuff);

    function drawStuff() {
      var data = new google.visualization.arrayToDataTable([
        ['Asesores', 'Pedidos'],
        @foreach ($pedidosxasesor_encargado as $vxa)
          ['{{ $vxa->users }}', {{ $vxa->pedidos }}],
        @endforeach
      ]);

      var options = {
        chart: {
          title: 'PEDIDOS DEL MES DE MIS ASESORES',
          subtitle: 'PEDIDO/ASESOR'
        }
      };

      var chart = new google.charts.Bar(document.getElementById('pedidosxasesor_encargado'));
      chart.draw(data, google.charts.Bar.convertOptions(options));
    };
  </script>

  <script type="text/javascript">
    google.charts.load('current', {
      'packages': ['bar']
    });
    google.charts.setOnLoadCallback(drawChart);

    function drawChart() {
      var data = google.visualization.arrayToDataTable([
        ['Clientes', 'Monto'],
        @foreach ($pagosxmes_encargado as $pxm)
          ['{{ $pxm->cliente }}', {{ $pxm->pagos }}],
        @endforeach
      ]);

      var options = {
        chart: {
          title: 'MONTO DE PAGOS POR CLIENTE DE MIS ASESORES EN EL MES',
          subtitle: 'TOP 30',
        }
      };

      var chart = new google.charts.Bar(document.getElementById('pagosxmes_encargado'));
      chart.draw(data, google.charts.Bar.convertOptions(options));
    }
  </script>

  <script type="text/javascript">
    google.charts.load("current", {packages:["corechart"]});
    google.charts.setOnLoadCallback(drawChart);
    function drawChart() {
      var data = google.visualization.arrayToDataTable([
        ['Asesor', 'Pedidos por día'],
        @foreach($pedidosxasesorxdia_encargado as $pxad)
          ['{{$pxad->users}}', {{$pxad->pedidos}}],
        @endforeach
      ]);

      var options = {
        title: '',
        is3D: true,
      };

      var chart = new google.visualization.PieChart(document.getElementById('pedidosxasesorxdia_encargado'));
      chart.draw(data, options);
    }
  </script>

  <!--ASESOR-->
  <script type="text/javascript">
    google.charts.load('current', {
      'packages': ['bar']
    });
    google.charts.setOnLoadCallback(drawStuff);

    function drawStuff() {
      var data = new google.visualization.arrayToDataTable([
        ['Fecha', 'Pedidos'],
        @foreach ($pedidosxasesorxdia_asesor as $vxa)
          ['{{ $vxa->fecha }}', {{ $vxa->pedidos }}],
        @endforeach
      ]);

      var options = {
        chart: {
          title: 'HISTORIAL DE MIS PEDIDOS EN EL MES',
          subtitle: 'PEDIDO/ASESOR'
        }
      };

      var chart = new google.charts.Bar(document.getElementById('mispedidosxasesorxdia'));
      chart.draw(data, google.charts.Bar.convertOptions(options));
    };
  </script>
@stop

@section('content')

@if(Auth::user()->rol == 'Administrador')
    <div style="text-align: center; font-family:'Times New Roman', Times, serif">
      <h2>
        <p>Bienvenido <b>{{ Auth::user()->name }}</b> al software empresarial de sisFacturas, eres el <b>{{ Auth::user()->rol }} del sistema</b></p>
      </h2>
    </div> 
    <br>
    <br>
    <div class="container-fluid">
      <div class="row" style="color: #fff">
        <div class="col-lg-3 col-6">
          <div class="small-box bg-info">
            <div class="inner">
              @foreach ($pedidoxmes_total as $mpxm)
              <h3>{{ $mpxm->total  }}</h3>
              @endforeach
              <p>META DE PEDIDOS DEL MES</p>
            </div>
            <div class="icon">
              <i class="ion ion-bag"></i>
            </div>
            <a href="{{ route('pedidos.index') }}" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <div class="col-lg-3 col-6">
          <div class="small-box bg-success">
            <div class="inner">
              @foreach ($montopedidoxmes_total as $mcxm)
                <h3>S/@php echo number_format($mcxm->total,2) @endphp</h3>
              @endforeach
              <p>META DE COBRANZAS DEL MES</p>
            </div>
            <div class="icon">
              <i class="ion ion-stats-bars"></i>
            </div>
            <a href="{{ route('pedidos.index') }}" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <div class="col-lg-3 col-6">
          <div class="small-box bg-warning">
            <div class="inner">
              @foreach ($pagoxmes_total as $pxm)
                <h3>{{ $pxm->pedidos }}</h3>
              @endforeach
              <p>PEDIDOS DEL MES</p>
            </div>
            <div class="icon">
              <i class="ion ion-person-add"></i>
            </div>
            <a href="{{ route('pagos.index') }}" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>
        <div class="col-lg-3 col-6">
          <div class="small-box bg-danger">
            <div class="inner">
              @foreach ($montopagoxmes_total as $cxm)
                <h3>S/@php echo number_format( ($cxm->total)/1000 ,2) @endphp k</h3>
              @endforeach
              <p>COBRANZAS DEL MES</p>
            </div>
            <div class="icon">
              <i class="ion ion-pie-graph"></i>
            </div>
            <a href="{{ route('pagos.index') }}" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
          </div>
        </div>
      </div>
    </div>
    <div class="container-fluid">
      <div class="row">
        <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
          <br>
          <div class="table-responsive" style="text-align: center">
            <img src="imagenes/logo_facturas.png" alt="Logo" width="60%">
          </div>
        </div>
        <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
          <br>
          <div class="table-responsive">
            <table class="table table-striped table-bordered table-condensed table-hover"><br><h4>CANTIDAD DIARIA DE PEDIDOS POR ASESOR</h4>
              <div id="pedidosxasesorxdia" style="width: 100%; height: 500px;"></div>
            </table>
          </div>
      </div>
    <div class="container-fluid">
      <div class="row">
        <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
          <br>
          <div class="table-responsive">
            <table class="table table-striped table-bordered table-condensed table-hover">
              <div class="chart tab-pane active" id="pedidosxasesor" style="width: 100%; height: 550px;">
              </div>
            </table>
          </div>
        </div>
      </div>
    </div>
    <div class="container-fluid">
      <div class="row">
        <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
          <br>
          <div class="table-responsive">
            <table class="table table-striped table-bordered table-condensed table-hover">
              <div id="pagosxmes" style="width: 100%; height: 550px;">
              </div>
            </table>
          </div>
        </div>
      </div>
    </div>
    
    </div>
  {{-- @include('dashboard.modal.alerta') --}}

@elseif (Auth::user()->rol == 'Encargado')
  <div style="text-align: center; font-family:'Times New Roman', Times, serif">
    <h2>
      <p>Bienvenido(a) <b>{{ Auth::user()->name }}</b> al software empresarial de sisFacturas, donde cumples la función de <b>{{ Auth::user()->rol }}</b></p>
    </h2>
  </div>
  <br>
  <br>
  <div class="container-fluid">
    <div class="row" style="color: #fff;">
      <div class="col-lg-1 col-1">        
      </div>
      <div class="col-lg-5 col-5">
        <div class="small-box bg-info">
          <div class="inner">
            <h3>@php echo number_format(Auth::user()->meta_pedido)@endphp</h3>
            <p>META DE PEDIDOS</p>
          </div>
          <div class="icon">
            <i class="ion ion-bag"></i>
          </div>
          <a href="{{ route('pedidos.index') }}" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
      </div>
      <div class="col-lg-5 col-5">
        <div class="small-box bg-success">
          <div class="inner">
            {{-- @foreach ($montoventadia as $mvd) --}}
              <h3>S/{{ Auth::user()->meta_cobro }}</h3>
            {{-- @endforeach --}}
            <p>META DE COBRANZAS</p>
          </div>
          <div class="icon">
            <i class="ion ion-stats-bars"></i>
          </div>
          <a href="{{ route('pagos.index') }}" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
      </div>
      <div class="col-lg-1 col-1">        
      </div>
      <div class="col-lg-1 col-1">        
      </div>
      <div class="col-lg-5 col-5">
        <div class="small-box bg-warning">
          <div class="inner">
              <h3>{{ $meta_pedidoencargado }}</h3>
            <p>TUS PEDIDOS DEL MES</p>
          </div>
          <div class="icon">
            <i class="ion ion-person-add"></i>
          </div>
          <a href="{{ route('pedidos.index') }}" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
      </div>
      <div class="col-lg-5 col-5">
        <div class="small-box bg-danger">
          <div class="inner">
              <h3>S/{{ $meta_pagoencargado->pagos }}</h3>
            <p>MIS COBRANZAS DEL MES</p>
          </div>
          <div class="icon">
            <i class="ion ion-pie-graph"></i>
          </div>
          <a href="{{ route('pagos.index') }}" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
      </div>
      <div class="col-lg-1 col-1">        
      </div>
    </div>
  </div>
  <div class="container-fluid">
    <div class="row">      
      <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
        <br>
        <div class="table-responsive">
          <table class="table table-striped table-bordered table-condensed table-hover"><br><h4>PEDIDOS DEL DIA POR ASESOR</h4>
            <div id="pedidosxasesorxdia_encargado" style="width: 100%; height: 500px;"></div>
          </table>
        </div>
      </div>
      <div class="col-lg-6 col-sm-6 col-md-6 col-xs-12">
        <br>
        <div class="table-responsive">
          <img src="imagenes/logo_facturas.png" alt="Logo" width="80%">
        </div>
      </div>
    </div>
  </div>
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <br>
        <div class="table-responsive">
          <table class="table table-striped table-bordered table-condensed table-hover">
            <div class="chart tab-pane active" id="pedidosxasesor_encargado" style="width: 100%; height: 550px;">
            </div>
          </table>
        </div>
      </div>
    </div>
  </div>
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <br>
        <div class="table-responsive">
          <table class="table table-striped table-bordered table-condensed table-hover">
            <div class="chart tab-pane active" id="pedidosxasesor_3meses_encargado" style="width: 100%; height: 550px;">
            </div>
          </table>
        </div>
      </div>
    </div>
  </div>
  <div class="container-fluid">
    <div class="row">
      <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
        <br>
        <div class="table-responsive">
          <table class="table table-striped table-bordered table-condensed table-hover">
            <div id="pagosxmes_encargado" style="width: 100%; height: 550px;">
            </div>
          </table>
        </div>
      </div>
    </div>
  </div>
  
@elseif (Auth::user()->rol == 'Asesor')
  <div class="container-fluid">
    <div class="row" style="text-align: center; font-family:Georgia, 'Times New Roman', Times, serif">
      <div class="col-lg-9 col-9" style="margin-top:20px"> 
        <h2>
          <p>Bienvenido(a) <b>{{ Auth::user()->name }}</b> al software empresarial de sisFacturas, donde cumples la función de <b>{{ Auth::user()->rol }}</b></p>
        </h2>       
      </div>  
      <div class="col-lg-3 col-3">
        <div class="small-box bg-danger">
          <div class="inner">
            <h3>{{ $pagosobservados_cantidad }}</h3>
            <p>PAGOS OBSERVADOS</p>
          </div>
          <div class="icon">
            <i class="ion ion-bag"></i>
          </div>
          <a href="{{ route('pagos.pagosobservados') }}" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
      </div>  
    </div>
  </div>
  <br>
  <br>
  <div class="container-fluid">
    <div class="row" style="color: #fff;">
      <div class="col-lg-1 col-1">        
      </div>
      <div class="col-lg-5 col-5">
        <div class="small-box bg-info">
          <div class="inner">
            <h3>@php echo number_format(Auth::user()->meta_pedido)@endphp</h3>
            <p>META DE PEDIDOS</p>
          </div>
          <div class="icon">
            <i class="ion ion-bag"></i>
          </div>
          <a href="{{ route('pedidos.mispedidos') }}" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
      </div>
      <div class="col-lg-5 col-5">
        <div class="small-box bg-success">
          <div class="inner">
              <h3>S/{{ Auth::user()->meta_cobro }}</h3>
            <p>META DE COBRANZAS</p>
          </div>
          <div class="icon">
            <i class="ion ion-stats-bars"></i>
          </div>
          <a href="{{ route('pagos.mispagos') }}" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
      </div>
      <div class="col-lg-1 col-1">        
      </div>
      <div class="col-lg-1 col-1">        
      </div>
      <div class="col-lg-5 col-5">
        <div class="small-box bg-warning">
          <div class="inner">
              <h3>{{ $meta_pedidoasesor }}</h3>
            <p>TUS PEDIDOS DEL MES</p>
          </div>
          <div class="icon">
            <i class="ion ion-person-add"></i>
          </div>
          <a href="{{ route('pedidos.mispedidos') }}" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
      </div>
      <div class="col-lg-5 col-5">
        <div class="small-box bg-warning">
          <div class="inner">
              <h3>S/{{ $meta_pagoasesor->pagos }}</h3>
            <p>MIS COBRANZAS DEL MES</p>
          </div>
          <div class="icon">
            <i class="ion ion-pie-graph"></i>
          </div>
          <a href="{{ route('pagos.mispagos') }}" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
      </div>
      <div class="col-lg-1 col-1">        
      </div>
    </div>
  </div>
  <div class="container-fluid">
    <div class="row">  
      <div class="container-fluid">
        <div class="row">
          <div class="col-lg-12 col-sm-12 col-md-12 col-xs-12">
            <br>
            <div class="table-responsive">
              <table class="table table-striped table-bordered table-condensed table-hover">
                <div class="chart tab-pane active" id="mispedidosxasesorxdia" style="width: 100%; height: 550px;">
                </div>
              </table>
            </div>
          </div>
        </div>
      </div>  
      <div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
      </div>
      <div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
        <br>
        <div class="table-responsive">
          <img src="imagenes/logo_facturas.png" alt="Logo" width="100%">
        </div>
      </div>
      <div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
      </div>
    </div>
  </div>
  @include('dashboard.modal.asesoralerta')

@elseif (Auth::user()->rol == 'Operacion')
  <div style="text-align: center; font-family:'Times New Roman', Times, serif">
    <h2>
      <p>Bienvenido(a) <b>{{ Auth::user()->name }}</b> del equipo de <b>OPERACIONES</b> al software empresarial de sisFacturas</b></p>
    </h2>
  </div>
  <br>
  <br>
  <div class="container-fluid">
    <div class="row" style="color: #fff;">
      <div class="col-lg-1 col-1">        
      </div>
      <div class="col-lg-5 col-5">
        <div class="small-box bg-info">
          <div class="inner">
            <h3>{{ $pedidoxatender }}</h3>
            <p>PEDIDOS POR ATENDER</p>
          </div>
          <div class="icon">
            <i class="ion ion-bag"></i>
          </div>
          <a href="{{ route('operaciones.poratender') }}" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
      </div>
      <div class="col-lg-5 col-5">
        <div class="small-box bg-success">
          <div class="inner">
              <h3>{{ $pedidoenatencion }}</h3>
            <p>PEDIDOS EN PROCESO DE ATENCION</p>
          </div>
          <div class="icon">
            <i class="ion ion-stats-bars"></i>
          </div>
          <a href="{{ route('operaciones.enatencion') }}" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
      </div>
      <div class="col-lg-1 col-1">        
      </div>
    </div>
  </div>
  <div class="container-fluid">
    <div class="row">      
      <div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
      </div>
      <div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
        <br>
        <div class="table-responsive">
          <img src="imagenes/logo_facturas.png" alt="Logo" width="100%">
        </div>
      </div>
      <div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
      </div>
    </div>
  </div>
@elseif (Auth::user()->rol == 'Administracion')
  <div style="text-align: center; font-family:'Times New Roman', Times, serif">
    <h2>
      <p>Bienvenido(a) <b>{{ Auth::user()->name }}</b> del equipo de <b>ADMINISTRACION</b> al software empresarial de sisFacturas</b></p>
    </h2>
  </div>
  <br>
  <br>
  <div class="container-fluid">
    <div class="row" style="color: #fff;">
      <div class="col-lg-1 col-1">        
      </div>
      <div class="col-lg-5 col-5">
        <div class="small-box bg-warning">
          <div class="inner">
            <h3>{{ $pagosxrevisar_administracion }}</h3>
            <p>PAGOS POR REVISAR</p>
          </div>
          <div class="icon">
            <i class="ion ion-bag"></i>
          </div>
          <a href="{{ route('administracion.porrevisar') }}" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
      </div>
      <div class="col-lg-5 col-5">
        <div class="small-box bg-danger">
          <div class="inner">
              <h3>{{ $pagosobservados_administracion }}</h3>
            <p>PAGOS OBSERVADOS</p>
          </div>
          <div class="icon">
            <i class="ion ion-stats-bars"></i>
          </div>
          <a href="{{ route('administracion.porrevisar') }}" class="small-box-footer">Más info <i class="fas fa-arrow-circle-right"></i></a>
        </div>
      </div>
      <div class="col-lg-1 col-1">        
      </div>
    </div>
  </div>
  <div class="container-fluid">
    <div class="row">      
      <div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
      </div>
      <div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
        <br>
        <div class="table-responsive">
          <img src="imagenes/logo_facturas.png" alt="Logo" width="100%">
        </div>
      </div>
      <div class="col-lg-4 col-sm-4 col-md-4 col-xs-12">
      </div>
    </div>
  </div>
@else
  <div style="text-align: center; font-family:'Times New Roman', Times, serif">
    <h2>
      <p>Bienvenido(a) <b>{{ Auth::user()->name }}</b> al software empresarial de sisFacturas</b></p>
    </h2>
  </div>
  <br>
  <br>
  <div class="col-lg-12 col-12" style="text-align: center">
    <img src="imagenes/logo_facturas.png" alt="Logo" width="50%">
  </div>
@endif

@stop

@section('css')
  <style>
    .content-header{
    background-color: white !important;
    }
    .content{
    background-color: white !important;
    }
  </style>
  
@stop

@section('js')

  <script src="{{ asset('js/datatables.js') }}"></script>

  @if (!$pedidossinpagos == null)
    <script>
      $('#staticBackdrop').modal('show')
    </script>
  @endif

  {{-- <script>
    // CARGAR PEDIDOS DE CLIENTE SELECCIONADO
    window.onload = function () {
      $.ajax({
        url: "{{ route('notifications.get') }}"
        method: 'GET',
        success: function(data) {
          $('#my-notification').html(data.html);
        }
      });
    };
  </script> --}}
@stop
