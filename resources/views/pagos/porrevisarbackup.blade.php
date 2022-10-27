@extends('adminlte::page')

@section('title', 'Lista de Pagos')

@section('content_header')
  <h1>Lista de pagos POR REVISAR
    @can('pagos.create')
      <a href="{{ route('pagos.create') }}" class="btn btn-info"><i class="fas fa-plus-circle"></i> Agregar</a>
    @endcan
    <div class="float-right btn-group dropleft">
      {{-- <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        Exportar
      </button> --}}
      {{-- <div class="dropdown-menu">
        <a href="{{ route('excelContratos') }}" class="dropdown-item"><img src="{{ asset('img/icon-excel.png') }}"> EXCEL</a>
      </div> --}}
    </div>
  </h1>
  @if($superasesor > 0)
  <br>
  <div class="bg-4">
    <h1 class="t-stroke t-shadow-halftone2" style="text-align: center">
      asesores con privilegios superiores: {{ $superasesor }}
    </h1>
  </div>
  @endif
@stop

@section('content')

  <div class="card">
    <div class="card-body">
      <table id="tablaPrincipal" class="table table-striped">
        <thead>
          <tr>
            <th scope="col">COD.</th>
            <th scope="col">Codigo pedido</th>
            <th scope="col">Asesor</th>
            <th scope="col">Observacion</th>
            <th scope="col">Total cobro</th>
            <th scope="col">Total pagado</th>
            <th scope="col">Estado</th>
            <th scope="col">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($pagos as $pago)
            <tr>
              <td>PAG000{{ $pago->id }}</td>
              <td>{{ $pago->codigos }}</td>
              <td>{{ $pago->users }}</td>
              <td>{{ $pago->observacion }}</td>
              <td>@php echo number_format($pago->total_deuda,2) @endphp</td>
              <td>@php echo number_format($pago->total_pago,2) @endphp</td>
              <td>{{ $pago->condicion }}</td>
              <td>
                @can('administracion.show')
                  <a href="{{ route('pagos.show', $pago) }}" class="btn btn-info btn-sm">Ver</a>
                @endcan
                @can('administracion.revisar')
                  <a href="{{ route('administracion.revisar', $pago) }}" class="btn btn-success btn-sm">Revisar</a>
                @endcan                
                @can('administracion.destroy')
                  <a href="" data-target="#modal-delete-{{ $pago->id }}" data-toggle="modal"><button class="btn btn-danger btn-sm">Eliminar</button></a>
                @endcan
              </td>
            </tr>
            @include('pagos.modals.modalDelete')
          @endforeach
        </tbody>
      </table>
    </div>
  </div>

@stop

@section('css')
  <link rel="stylesheet" href="../css/admin_custom.css">
  <style>
    .bg-4{
      background: linear-gradient(to right, rgb(240, 152, 25), rgb(237, 222, 93));
    }

    .t-stroke {
        color: transparent;
        -moz-text-stroke-width: 2px;
        -webkit-text-stroke-width: 2px;
        -moz-text-stroke-color: #000000;
        -webkit-text-stroke-color: #ffffff;
    }

    .t-shadow-halftone2 {
        position: relative;
    }

    .t-shadow-halftone2::after {
        content: "AWESOME TEXT";
        font-size: 10rem;
        letter-spacing: 0px;
        background-size: 100%;
        -webkit-text-fill-color: transparent;
        -moz-text-fill-color: transparent;
        -webkit-background-clip: text;
        -moz-background-clip: text;
        -moz-text-stroke-width: 0;
        -webkit-text-stroke-width: 0;
        position: absolute;
        text-align: center;
        left: 0px;
        right: 0;
        top: 0px;
        z-index: -1;
        background-color: #ff4c00;
        transition: all 0.5s ease;
        text-shadow: 10px 2px #6ac7c2;
    }

  </style>
@stop

@section('js')

  <script src="{{ asset('js/datatables.js') }}"></script>

  @if (session('info') == 'registrado' || session('info') == 'eliminado' || session('info') == 'actualizado')
    <script>
      Swal.fire(
        'Pago {{ session('info') }} correctamente',
        '',
        'success'
      )
    </script>
  @endif

  @if (session('info2') == 'error')
    <script>
      Swal.fire(
        'Pago no se compleo',
        '',
        'success'
      )
    </script>
  @endif

@stop