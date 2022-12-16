@extends('adminlte::page')

@section('title', 'Pedidos | Pedidos por cobrar')

@section('content_header')
  <h1>Lista de pedidos por cobrar
    {{-- @can('pedidos.create')
      <a href="{{ route('pedidos.create') }}" class="btn btn-info"><i class="fas fa-plus-circle"></i> Agregar</a>
    @endcan --}}
    {{-- @can('pedidos.exportar')
    <div class="float-right btn-group dropleft">
      <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        Exportar
      </button>
      <div class="dropdown-menu">
        <a href="{{ route('pedidossinpagosExcel') }}" class="dropdown-item"><img src="{{ asset('imagenes/icon-excel.png') }}"> EXCEL</a>
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
    @include('pedidos.modal.exportar', ['title' => 'Exportar Lista de pedidos por cobrar', 'key' => '6'])
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
            <th scope="col">Item</th>
            <th scope="col">Código</th>
            <th scope="col">Cliente</th>
            <th scope="col">Razón social</th>
            <th scope="col">Asesor</th>
            <th scope="col">Fecha de registro</th>
            <th scope="col">Total (S/)</th>
            <th scope="col">Estado de pedido</th>
            <th scope="col">Estado de pago</th>
            <th scope="col">Administracion</th>
            <th scope="col">Diferencia</th>
            <th scope="col">Acciones</th>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
      @include('pedidos.modalid')
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
  {{--<script src="{{ asset('js/datatables.js') }}"></script>--}}
  <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>

  <script>
    $(document).ready(function () {

      $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

      $('#tablaPrincipal').DataTable({
        processing: true,
        serverSide: true,
        searching: true,
        "order": [[ 0, "desc" ]],
        ajax: "{{ route('pedidos.sinpagostabla') }}",
        "createdRow": function( row, data, dataIndex){
            /*if(data["estado"] == "1")
            {
            }else{
              $(row).addClass('textred');
            }   */
        },
        rowCallback: function (row, data, index) {
            var pedidodiferencia=data.diferencia;
            //pedidodiferencia=0;
            if(pedidodiferencia==null){
                $('td:eq(9)', row).css('background', '#efb7b7').css('color','##934242').css('text-align','center').css('font-weight','bold');
            }else{
                if(pedidodiferencia>3){
                    $('td:eq(9)', row).css('background', '#efb7b7').css('color','##934242').css('text-align','center').css('font-weight','bold');
                }else{
                    $('td:eq(9)', row).css('background', '#afdfb2').css('text-align','center').css('font-weight','bold');
                }
            }
        },
        initComplete:function(settings,json){

        },
        columns: [
          {
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
            },
            "visible":false,
        },
        {data: 'codigos', name: 'codigos', },
        {
            data: 'celulares',
            name: 'celulares',
            render: function ( data, type, row, meta ) {
              if(row.icelulares!=null)
              {
                return row.celulares+'-'+row.icelulares+' '+row.nombres;
              }else{
                return row.celulares+' '+row.nombres;;
              }
            },
        },
        {data: 'empresas', name: 'empresas', },
        {data: 'users', name: 'users', },
        {
          data: 'fecha',
          name: 'fecha',
        },
        {
          data: 'total',
          name: 'total',
          render: $.fn.dataTable.render.number(',', '.', 2, '')
        },
            {data: 'condicion_code',
                name: 'condicion_code',
                render: function ( data, type, row, meta ) {
                    if(row.condicion_code==1){
                        return '{{\App\Models\Pedido::POR_ATENDER }}';
                    }else if(row.condicion_code==2){
                        return '{{\App\Models\Pedido::EN_PROCESO_ATENCION }}';
                    }else if(row.condicion_code==3){
                        return '{{\App\Models\Pedido::ATENDIDO }}';
                    }else if(row.condicion_code==4){
                        return '{{\App\Models\Pedido::ANULADO }}';
                    }
                }
            },
        {
          data: 'condicion_pa',
          name: 'condicion_pa',
          render: function ( data, type, row, meta ) {
            if(row.condiciones=='ANULADO'){
                return 'ANULADO';
            }else{
              if(row.condicion_pa==null){
                return 'SIN PAGO REGISTRADO';
              }else{
                if(row.condicion_pa=='0'){
                  return '<p>SIN PAGO REGISTRADO</p>'
                }
                if(row.condicion_pa=='1'){
                  return '<p>ADELANTO</p>'
                }
                if(row.condicion_pa=='2'){
                  return '<p>PAGO</p>'
                }
                if(row.condicion_pa=='3'){
                  return '<p>ABONADO</p>'
                }
                //return data;
              }
            }

          }
        },
        {
          data: 'condiciones_aprobado',
          name: 'condiciones_aprobado',
          render: function ( data, type, row, meta ) {
            if(data!=null)
            {
              return data;
            }else{
              return 'SIN REVISAR';
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
        {
          data: 'action',
          name: 'action',
          orderable: false,
          searchable: false,
          sWidth:'20%',
          render: function ( data, type, row, meta ) {
            var urlpdf = '{{ route("pedidosPDF", ":id") }}';
            urlpdf = urlpdf.replace(':id', row.id);
            var urlshow = '{{ route("pedidos.show", ":id") }}';
            urlshow = urlshow.replace(':id', row.id);
            var urledit = '{{ route("pedidos.edit", ":id") }}';
            urledit = urledit.replace(':id', row.id);

              data = '<div class="dropdown"><button class="btn btn-option font-14 dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">  Opciones </button><div class="dropdown-menu" aria-labelledby="dropdownMenuButton">';


              @can('pedidos.pedidosPDF')
              data = data+'<a href="'+urlpdf+'" class="btn-sm dropdown-item" target="_blank"><i class="fa fa-file-pdf text-primary"></i> PDF</a>';
            @endcan
            @can('pedidos.show')
              data = data+'<a href="'+urlshow+'" class="btn-sm dropdown-item"><i class="fas fa-eye text-success"></i> Ver</a>';
            @endcan
            @can('pedidos.edit')
              if(row.condicion_pa==0)
              {
                data = data+'<a href="'+urledit+'" class="btn-sm dropdown-item"><i class="fas fa-edit text-warning"></i>  Editar</a>';
              }
            @endcan
            @can('pedidos.destroy')
            if(row.estado==0)
            {
              data = data+'<a href="" data-target="#modal-restaurar" class="btn-sm dropdown-item" data-toggle="modal" data-restaurar="'+row.id+'" ><i class="fa fa-undo" aria-hidden="true"></i> Restaurar</a>';
            }else{
              if(row.condicion_pa==0)
              {
                data = data+'<a href="" data-target="#modal-delete" class="btn-sm dropdown-item" data-toggle="modal" data-delete="'+row.id+'" data-responsable="{{ $miidentificador }}"><i class="fas fa-trash-alt text-danger"></i> Anular</a>';
              }
            }

            @endcan
            @can('pagos.create')

                //var varpagar1 = "['id'=>:id]";
                //varpagar1 = varpagar1.replace(':id', row.id);
                //console.log(varpagar1);

                var varcreate = '{{ route("pagos.create", ":id") }}';
                varcreate = varcreate.replace(':id', row.cliente_id);

                console.log(varcreate);

                data = data+'<a class="clickpagar btn-sm dropdown-item" href="'+varcreate+'" data-pagar="'+row.cliente_id+'"><i class="fas fa-check text-success"></i> Pagar</a>';

            @endcan

                data = data+'</div></div>';

            return data;
          }
        },

      ],

      });

      $('#tablaPrincipal_filter label input').on('paste', function(e) {
        var pasteData = e.originalEvent.clipboardData.getData('text')
        console.log(pasteData)
        localStorage.setItem("search_tabla",pasteData);
        console.log("set "+pasteData)
      });
      $(document).on("keypress",'#tablaPrincipal_filter label input',function(){
        localStorage.setItem("search_tabla",$(this).val());
        console.log( "search_tabla es "+localStorage.getItem("search_tabla") );
      });


      $(document).on("click",".clickpagar",function(){
        var button = $(event.relatedTarget)
        var idunico = button.data('pagar')
        localStorage.setItem('clickpagar', idunico);
      });
    });

  </script>

  @if (session('info') == 'registrado' || session('info') == 'actualizado' || session('info') == 'eliminado')
    <script>
      Swal.fire(
        'Pedido {{ session('info') }} correctamente',
        '',
        'success'
      )
    </script>
  @endif

@stop
