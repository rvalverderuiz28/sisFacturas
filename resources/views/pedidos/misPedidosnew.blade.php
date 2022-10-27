@extends('adminlte::page')

@section('title', 'Lista de mis pedidos')

@section('content_header')
  <h1>Lista de mis pedidos
    @can('pedidos.create')
      <a href="{{ route('pedidos.create') }}" class="btn btn-info"><i class="fas fa-plus-circle"></i> Agregar</a>
    @endcan
    {{-- @can('pedidos.exportar')
    <div class="float-right btn-group dropleft">
      <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        Exportar
      </button>
      <div class="dropdown-menu">
        <a href="{{ route('mispedidosExcel') }}" class="dropdown-item"><img src="{{ asset('imagenes/icon-excel.png') }}"> EXCEL</a>
      </div>
    </div>
    @endcan --}}
    <div class="float-right btn-group dropleft">
      <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        Exportar
      </button>
      <div class="dropdown-menu">
        <a href="" data-target="#modal-exportar" data-toggle="modal" class="dropdown-item" target="blank_"><img src="{{ asset('imagenes/icon-excel.png') }}"> Excel</a>
      </div>
    </div>
    @include('pedidos.modal.exportar', ['title' => 'Exportar Lista de mis pedidos', 'key' => '4'])
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
      <table cellspacing="5" cellpadding="5" class="table-responsive">
        <tbody>
          <tr>
            <td>Fecha Minima:</td>
            <td><input type="text" value={{ $dateMin }} id="min" name="min" class="form-control"></td>
            <td> </td>
            <td>Fecha Máxima:</td>
            <td><input type="text" value={{ $dateMax }} id="max" name="max"  class="form-control"></td>
          </tr>
        </tbody>
      </table><br>
      <table id="tablaPrincipal" class="table table-striped">
        <thead>
          <tr>
            <th scope="col">Item</th>
            <th scope="col">Código</th>
            <th scope="col">Cliente</th>
            <th scope="col">Razón social</th>
            <th scope="col">Asesor</th>
            <th scope="col">Fecha de registro</th>
            <th scope="col">Total (S/)</th>
            <th scope="col">Estado de pedido</th>
            <th scope="col">Estado de pago</th>
            <th scope="col">Estado de sobre</th>
            <th scope="col">Estado de envío</th>
            <th scope="col">Destino</th>
            <th scope="col">Diferencia</th>
            <th scope="col">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @foreach ($pedidos as $pedido)
            <tr>
              @if ($pedido->id < 10)
                <td>PED000{{ $pedido->id }}</td>
              @elseif($pedido->id < 100)
                <td>PED00{{ $pedido->id }}</td>
              @elseif($pedido->id < 1000)
                <td>PED0{{ $pedido->id }}</td>
              @else
                <td>PED{{ $pedido->id }}</td>
              @endif
              <td>{{ $pedido->codigos }}</td>
              <td>{{ $pedido->celulares }} - {{ $pedido->nombres }}</td>
              <td>{{ $pedido->empresas }}</td>
              <td>{{ $pedido->users }}</td>              
              <td>{{ $pedido->fecha }}</td>
              <td>@php echo number_format($pedido->total,2) @endphp</td>
              <td>{{ $pedido->condiciones }}</td>
              <td>{{ $pedido->condicion_pa }}</td>
              <td>
                @if ($pedido->envio == '1')
                  <span class="badge badge-success">Enviado</span>
                  <span class="badge badge-warning">Por confirmar recepcion</span>
                @elseif ($pedido->envio == '2')
                  <span class="badge badge-success">Enviado</span>
                  <span class="badge badge-info">Recibido</span>
                @else
                  <span class="badge badge-danger">Pendiente</span>
                @endif
              </td>
              <td>{{ $pedido->condicion_env }}</td>
              <td>{{ $pedido->destino }}</td>
              {{-- <td>{{ $pedido->diferencia }}</td> --}}
              @if(($pedido->total_cobro-$pedido->total_pagado)>3)<td style="background: #ca3a3a; color:#ffffff; text-align: center;font-weight: bold;">{{ $pedido->total_cobro-$pedido->total_pagado }}</td>
              @else<td style="background: #44c24b; text-align: center;font-weight: bold;">{{ $pedido->total_cobro-$pedido->total_pagado }}</td>
              @endif
              <td>
                @can('pedidos.pedidosPDF')
                  <a href="{{ route('pedidosPDF', $pedido) }}" class="btn btn-primary btn-sm" target="_blank"><i class="fa fa-file-pdf"></i> PDF</a>
                @endcan
                @can('pedidos.show')
                  <a href="{{ route('pedidos.show', $pedido) }}" class="btn btn-info btn-sm"><i class="fas fa-eye"></i> Ver</a>
                @endcan
                @can('pedidos.edit')
                  <a href="{{ route('pedidos.edit', $pedido->id) }}" class="btn btn-warning btn-sm">Editar</a>
                @endcan
                @can('pedidos.destroy')
                  <a href="" data-target="#modal-delete-{{ $pedido->id }}" data-toggle="modal"><button class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i> Eliminar</button></a>
                @endcan
                @if($pedido->destino == null && $pedido->direccion == '0' && ($pedido->envio)*1 > 0)
                {{-- <a href="" data-target="#modal-destino-{{ $pedido->id }}" data-toggle="modal"><button class="btn btn-outline-dark btn-sm"><i class="fas fa-map"></i> Destino</button></a> --}}
                  <a href="{{ route('envios.createdireccion', $pedido) }}" class="btn btn-dark btn-sm"><i class="fas fa-map"></i> Destino</a>
                @endif
              </td>
            </tr>
          @endforeach
          @foreach ($pedidos2 as $pedido)
            <tr>
              @if ($pedido->id < 10)
                <td>PED000{{ $pedido->id }}</td>
              @elseif($pedido->id < 100)
                <td>PED00{{ $pedido->id }}</td>
              @elseif($pedido->id < 1000)
                <td>PED0{{ $pedido->id }}</td>
              @else
                <td>PED{{ $pedido->id }}</td>
              @endif
              <td>{{ $pedido->codigos }}</td>
              <td>{{ $pedido->celulares }} - {{ $pedido->nombres }}</td>
              <td>{{ $pedido->empresas }}</td>
              <td>{{ $pedido->users }}</td>              
              <td>{{ $pedido->fecha }}</td>
              <td>@php echo number_format($pedido->total,2) @endphp</td>
              <td>{{ $pedido->condiciones }}</td>
              <td>SIN PAGOS REGISTRADOS</td>
              <td>
                @if ($pedido->envio == '1')
                  <span class="badge badge-success">Enviado</span>
                  <span class="badge badge-warning">Por confirmar recepcion</span>
                @elseif ($pedido->envio == '2')
                  <span class="badge badge-success">Enviado</span>
                  <span class="badge badge-info">Recibido</span>
                @else
                  <span class="badge badge-danger">Pendiente</span>
                @endif
              </td>
              <td>{{ $pedido->condicion_env }}</td>
              <td>{{ $pedido->destino }}</td>
              <td style="background: #ca3a3a; color:#ffffff; text-align: center;font-weight: bold;">@php echo number_format($pedido->total,2) @endphp</td>
              <td>
                @can('pedidos.pedidosPDF')
                  <a href="{{ route('pedidosPDF', $pedido) }}" class="btn btn-primary btn-sm" target="_blank"><i class="fa fa-file-pdf"></i> PDF</a>
                @endcan
                @can('pedidos.show')
                  <a href="{{ route('pedidos.show', $pedido) }}" class="btn btn-info btn-sm"><i class="fas fa-eye"></i> Ver</a>
                @endcan
                @can('pedidos.edit')
                  <a href="{{ route('pedidos.edit', $pedido->id) }}" class="btn btn-warning btn-sm">Editar</a>
                @endcan
                @can('pedidos.destroy')
                  <a href="" data-target="#modal-delete-{{ $pedido->id }}" data-toggle="modal"><button class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i> Eliminar</button></a>
                @endcan
                @if($pedido->destino == null && $pedido->direccion == '0' && ($pedido->envio)*1 > 0)
                {{-- <a href="" data-target="#modal-destino-{{ $pedido->id }}" data-toggle="modal"><button class="btn btn-outline-dark btn-sm"><i class="fas fa-map"></i> Destino</button></a> --}}
                  <a href="{{ route('envios.createdireccion', $pedido) }}" class="btn btn-dark btn-sm"><i class="fas fa-map"></i> Destino</a>
                @endif
              </td>
            </tr>
            @include('pedidos.modal')
            @include('pedidos.modal.destino')
          @endforeach    
        </tbody>
      </table>
    </div>
  </div>

@stop

@section('css')
  {{-- <link rel="stylesheet" href="../css/admin_custom.css"> --}}
  <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">  

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

  @if (session('info') == 'registrado' || session('info') == 'actualizado' || session('info') == 'eliminado')
    <script>
      Swal.fire(
        'Pedido {{ session('info') }} correctamente',
        '',
        'success'
      )
    </script>
  @endif

  <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>

  <script>
    /*window.onload = function () {      
      $('#tablaPrincipal').DataTable().draw();
    }*/
  </script>

  <script>
    /* Custom filtering function which will search data in column four between two values */
        $(document).ready(function () { 
        
            $.fn.dataTable.ext.search.push(
                function (settings, data, dataIndex) {
                    var min = $('#min').datepicker("getDate");
                    var max = $('#max').datepicker("getDate");
                    // need to change str order before making  date obect since it uses a new Date("mm/dd/yyyy") format for short date.
                    var d = data[5].split("/");
                    var startDate = new Date(d[1]+ "/" +  d[0] +"/" + d[2]);

                    if (min == null && max == null) { return true; }
                    if (min == null && startDate <= max) { return true;}
                    if(max == null && startDate >= min) {return true;}
                    if (startDate <= max && startDate >= min) { return true; }
                    return false;
                }
            );

      
            $("#min").datepicker({ onSelect: function () { table.draw(); }, changeMonth: true, changeYear: true , dateFormat:"dd/mm/yy"});
            $("#max").datepicker({ onSelect: function () { table.draw(); }, changeMonth: true, changeYear: true, dateFormat:"dd/mm/yy" });
            //var table = $('#tablaPrincipal').DataTable();

            // Event listener to the two range filtering inputs to redraw on input
            /*$('#min, #max').change(function () {
                table.draw();
            });*/

            $('#tablaPrincipal').DataTable({
              processing: true,
              serverSide: true,
              searching: true,
              ajax: "{{ route('mispedidostabla') }}",
              "createdRow": function( row, data, dataIndex){
                  if(data["estado"] == "1")
                  {
                  }else{
                    $(row).addClass('yellow');
                  }           
              },
              rowCallback: function (row, data, index) {
                    var pedidodiferencia=data.diferencia;
                    //pedidodiferencia=0;
                    if(pedidodiferencia==null){
                      $('td:eq(12)', row).css('background', '#ca3a3a').css('color','#ffffff').css('text-align','center').css('font-weight','bold');
                    }else{
                      if(pedidodiferencia>3){
                        $('td:eq(12)', row).css('background', '#ca3a3a').css('color','#ffffff').css('text-align','center').css('font-weight','bold');
                      }else{
                        $('td:eq(12)', row).css('background', '#44c24b').css('text-align','center').css('font-weight','bold');
                      }
                    }
              },
              columns: [
              {//15 columnas
                  data: 'id', 
                  name: 'id',
                  render: function ( data, type, row, meta ) {
                    if(row.id<10){
                      return 'PED000'+row.id;
                    }else if(row.id<100){
                      return 'PED00'+row.id;
                    }else if(row.id<1000){
                      return 'PED0'+row.id;
                    }else{
                      return 'PED'+row.id;
                    } 
                  }
              },
              {data: 'codigos', name: 'codigos', },
              {
                  data: 'celulares', 
                  name: 'celulares',
                  render: function ( data, type, row, meta ) {
                    return row.celulares+' - '+row.nombres
                  },
                  //searchable: true
              },
              {data: 'empresas', name: 'empresas', },
              {data: 'users', name: 'users', },
              {data: 'fecha', name: 'fecha', },
              {
                data: 'total', 
                name: 'total', 
                render: $.fn.dataTable.render.number(',', '.', 2, '')
              },
              {
                data: 'condiciones', 
                name: 'condiciones', 
                render: function ( data, type, row, meta ) {
                    return data;
                }
              },//estado de pedido
              {
                data: 'condicion_pa', 
                name: 'condicion_pa', 
                render: function ( data, type, row, meta ) {
                  if(row.condicion_pa==null){
                    return 'SIN PAGO REGISTRADO';
                  }else{
                    return data;
                  }              
                }
              },//estado de pago
              {
                //estado del sobre
                data: 'envio', 
                name: 'envio', 
                render: function ( data, type, row, meta ) {
                  if(row.envio==null){
                    return '';
                  }else{
                    if(row.envio=='1'){
                      return '<span class="badge badge-success">Enviado</span><br>'+
                              '<span class="badge badge-warning">Por confirmar recepcion</span>';
                    }else if(row.envio=='2'){
                      return '<span class="badge badge-success">Enviado</span><br>'+
                              '<span class="badge badge-info">Recibido</span>';
                    }else{
                      return '<span class="badge badge-danger">Pendiente</span>';
                    }

                  }
                }
              },
              //{data: 'responsable', name: 'responsable', },//estado de envio
              
              //{data: 'condicion_pa', name: 'condicion_pa', },//ss
              {data: 'condicion_envio', name: 'condicion_envio', },//
              {
                data: 'estado',
                name: 'estado',
                render: function ( data, type, row, meta ) {
                    if(row.estado==1){
                      return '<span class="badge badge-success">Activo</span>';
                    }else{
                      return '<span class="badge badge-danger">Anulado</span>';
                    }
                  }
              },
              {
                data: 'diferencia', 
                name: 'diferencia',
                render: function ( data, type, row, meta ) {
                  if(row.diferencia==null){
                    return 'NO REGISTRA PAGO';
                  }else{
                    if(row.diferencia>0){
                      return row.diferencia;
                    }else{
                      return row.diferencia;
                    }
                  }            
                }
              },
              //{data: 'responsable', name: 'responsable', },
              {data: 'action', name: 'action', orderable: false, searchable: false,sWidth:'20%'},
              ],
              language: {
              "decimal": "",
              "emptyTable": "No hay informaciÃ³n",
              "info": "Mostrando del _START_ al _END_ de _TOTAL_ Entradas",
              "infoEmpty": "Mostrando 0 to 0 of 0 Entradas",
              "infoFiltered": "(Filtrado de _MAX_ total entradas)",
              "infoPostFix": "",
              "thousands": ",",
              "lengthMenu": "Mostrar _MENU_ Entradas",
              "loadingRecords": "Cargando...",
              "processing": "Procesando...",
              "search": "Buscar:",
              "zeroRecords": "Sin resultados encontrados",
              "paginate": {
                "first": "Primero",
                "last": "Ultimo",
                "next": "Siguiente",
                "previous": "Anterior"
              }
            },
          });
        });
  </script>

@stop