@extends('adminlte::page')

@section('title', 'Lista de pedidos por confirmar')

@section('content_header')
  <h1>Recepcion para motorizados - ENVIOS
    {{-- <div class="float-right btn-group dropleft">
      <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        Exportar
      </button>
      <div class="dropdown-menu">
        <a href="{{ route('pedidosporenviarExcel') }}" class="dropdown-item"><img src="{{ asset('imagenes/icon-excel.png') }}"> EXCEL</a>
      </div>
    </div> --}}
    {{-- @can('clientes.exportar') --}}
    <div class="float-right btn-group dropleft">
        <button type="button" class="btn btn-option" data-toggle="modal" data-target="#modal-escanear" data-backdrop="static" style="margin-right:16px;" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-barcode" aria-hidden="true"></i> Escanear
        </button>
      <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        Exportar
      </button>
      <div class="dropdown-menu">
        <a href="" data-target="#modal-exportar" data-toggle="modal" class="dropdown-item" target="blank_"><img src="{{ asset('imagenes/icon-excel.png') }}"> Excel</a>
      </div>
    </div>
    @include('pedidos.modal.exportar', ['title' => 'Exportar pedidos POR ENVIAR', 'key' => '1'])
    {{-- @endcan --}}
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
      {{-- <table cellspacing="5" cellpadding="5">
        <tbody>
          <tr>
            <td>Destino:</td>
            <td>
              <select name="destino" id="destino" class="form-control">
                <option value="LIMA">LIMA</option>
                <option value="PROVINCIA">PROVINCIA</option>
              </select>
            </td>
          </tr>
        </tbody>
      </table><br> --}}
      <table id="tablaPrincipal" class="table table-striped">
        <thead>
          <tr>
            <th scope="col">Item</th>
            <th scope="col">Código</th>
            <th scope="col">Asesor</th>
            <th scope="col">Cliente</th>
            <th scope="col">Razón social</th>
            <th scope="col">Fecha de registro</th>

            <th scope="col">Dirección de envío</th>
            <th scope="col">Estado de envio</th>
            <th scope="col">Estado de sobre</th>
            <th scope="col">Acciones</th>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
      @include('pedidos.modal.confirmar_recepcion_log')
      @include('envios.modal.enviarid')
      @include('pedidos.modal.recibirid')

      @include('pedidos.modal.verdireccionid')
      @include('pedidos.modal.editdireccionid')
      @include('pedidos.modal.destinoid')
        @include('pedidos.modal.escaneaqr')
    </div>
  </div>

@stop

@section('css')
  <link rel="stylesheet" href="/css/admin_custom.css">
  <style>
    img:hover{
      transform: scale(1.2)
    }

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

        /************
         * ESCANEAR PEDIDO
         */

        $('#modal-escanear').on('shown.bs.modal', function () {
            $('#codigo_confirmar').focus();
            $('#codigo_accion').val("fernandez");
            $('#titulo-scan').html("Escanear para confirmar la <span class='text-success'>Recepción de sobres</span>");
            $('#modal-escanear').on('click', function(){
                console.log("focus");
                $('#codigo_confirmar').focus();

                return false;
            });
        })

        $('#codigo_confirmar').change(function (event) {
            event.preventDefault();
            var codigo_caturado = $(this).val();
            var codigo_mejorado = codigo_caturado.replace(/['']+/g, '-');
            var codigo_accion = $('#codigo_accion').val();
            console.log("El codigo es: " + codigo_mejorado);
            /*************
             * Enviamos la orden al controlaor
             * @type {FormData}
             */
            var fd_scan = new FormData();

            fd_scan.append( 'hiddenCodigo', codigo_mejorado );
            fd_scan.append( 'accion', codigo_accion );

            $.ajax({
                data: fd_scan,
                processData: false,
                contentType: false,
                type: 'POST',
                url:"{{ route('operaciones.confirmaropbarras') }}",
                success:function(data)
                {
                    console.log(data);
                    $('#respuesta_barra').removeClass("text-danger");
                    $('#respuesta_barra').removeClass("text-success");
                    $('#respuesta_barra').addClass(data.class);
                    $('#respuesta_barra').html(data.html);
                }
            });

            $(this).val("");
            return false;
        });

        /***********
         * FIN ESCANEAR MOUSE
         */

      $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

      $('#modal-envio').on('show.bs.modal', function (event) {
        //cuando abre el form de anular pedido
        var button = $(event.relatedTarget)
        var idunico = button.data('recibir')
        var codigos = button.data('codigos')

        $(".textcode").html(codigos);
        $("#hiddenEnvio").val(idunico);

      });

      $(document).on("submit", "#formulariorecepcion", function (evento) {
        evento.preventDefault();
        var fd = new FormData();
        var data = new FormData(document.getElementById("formulariorecepcion"));

        fd.append( 'hiddenEnvio', $("#hiddenEnvio").val() );

        $.ajax({
           data: data,
           processData: false,
           contentType: false,
           type: 'POST',
           url:"{{ route('envios.recepcionarmotorizado') }}",
           success:function(data)
           {
            console.log(data);
            $("#modal-envio .textcode").text('');
            $("#modal-envio").modal("hide");
            $('#tablaPrincipal').DataTable().ajax.reload();
           }
        });
      });


      $(document).on("submit", "#formulario", function (evento) {
        evento.preventDefault();
        var fd = new FormData();

      });

      $('#modal-enviar').on('show.bs.modal', function (event) {
        //cuando abre el form de anular pedido
        var button = $(event.relatedTarget)
        var idunico = button.data('enviar')//pedido
        $("#hiddenEnviar").val(idunico)
        if(idunico<10){
          idunico='PED000'+idunico;
        }else if(idunico<100){
          idunico= 'PED00'+idunico;
        }else if(idunico<1000){
          idunico='PED0'+idunico;
        }else{
          idunico='PED'+idunico;
        }
        $("#modal-enviar .textcode").html(idunico);

      });

      $('#modal-recibir').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget)
        var idunico = button.data('recibir')//pedido
        $("#hiddenRecibir").val(idunico)
        if(idunico<10){
          idunico='PED000'+idunico;
        }else if(idunico<100){
          idunico= 'PED00'+idunico;
        }else if(idunico<1000){
          idunico='PED0'+idunico;
        }else{
          idunico='PED'+idunico;
        }
        $("#modal-recibir .textcode").html(idunico);


      });

      $(document).on("submit", "#formularioenviar", function (evento) {
        evento.preventDefault();
      });

      $(document).on("submit", "#formulariorecibir", function (evento) {
        evento.preventDefault();
        var formData=$("#formulariorecibir").serialize();

        $.ajax({
            type:'POST',
            url:"{{ route('envios.recibirid') }}",
            data:formData,
        }).done(function (data) {
            if(data.html!=0)
            {
                $("#modal-recibir").modal("hide");
                $('#tablaPrincipal').DataTable().ajax.reload();
            }else{

            }
            /*
            //resetearcamposdelete();
             */
        });

      });


      /*$('#modal-atender').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget)
        var idunico = button.data('atender')
        $(".textcode").html("PED"+idunico);
        $("#hiddenAtender").val(idunico);
      });*/

      $('#tablaPrincipal').DataTable({
        processing: true,
        stateSave:true,
		serverSide: true,
        searching: true,
        "order": [[ 0, "desc" ]],
        ajax: "{{ route('envios.recepcionmotorizadotabla') }}",
        createdRow: function( row, data, dataIndex){
          //console.log(row);
        },
        rowCallback: function (row, data, index) {
        },
        columns: [
          {
              data: 'correlativo',
              name: 'correlativo',
          },
          {data: 'codigos', name: 'codigos', },
          {data: 'user_id', name: 'user_id', },
          {
            data: 'celulares',
            name: 'celulares',
            render: function ( data, type, row, meta ) {
              return row.celulares+' - '+row.nombres
            },
            "visible":false
            //searchable: true
        },
          {data: 'producto', name: 'producto'},
          {data: 'fecha_formato', name: 'fecha_formato'},
          {
            data:'direccion',
            name:'direccion',"visible":false,
            render: function ( data, type, row, meta ) {
              //console.log(data);
              datas='';
              if(data!=null)
              {
                return data;
                /*if(data=='0')
                {
                  return '<span class="badge badge-danger">REGISTRE DIRECCION</span>';
                }else if(data=='LIMA')
                {
                  var urlshow = '{{ route("pedidos.show", ":id") }}';
                  urlshow = urlshow.replace(':id', row.id);

                  return '<a href="" data-target="#modal-verdireccion" data-toggle="modal" data-dirreccion="'+row.id+'"><button class="btn btn-primary btn-sm"><i class="fas fa-eye"></i> Ver</button></a>';
                }
                else if(data=='PROVINCIA')
                {
                  return '<span class="badge badge-info">ENVIO A PROVINCIA</span>';
                }else{
                  return '<span class="badge badge-info">PROBLEMAS CON REGISTRO DE DESTINO</span>';
                }
*/
                //return datas;

              }else{
                return 'REGISTRE DIRECCION';
              }
              //return 'REGISTRE DIRECCION';
            },
          },
          {
              data: 'condicion_envio',
              name: 'condicion_envio',
              render: function (data, type, row, meta) {
                  if (row.pendiente_anulacion == 1) {
                      return '<span class="badge badge-success">' + '{{\App\Models\Pedido::PENDIENTE_ANULACION }}' + '</span>';
                  }
                  var badge_estado=''
                  if (row.estado_sobre == 1) {
                      badge_estado += '<span class="badge badge-dark p-8" style="color: #fff; background-color: #347cc4; font-weight: 600; margin-bottom: -2px;border-radius: 4px 4px 0px 0px; font-size:8px;  padding:6px;">Direccion agregada</span>';
                  }
                  badge_estado+='<span class="badge badge-success" style="background-color: '+row.condicion_envio_color+'!important;">'+row.condicion_envio+'</span>';
                  return badge_estado;
              }
          },
          {
            data: 'envio',
            name: 'envio',
            render: function ( data, type, row, meta ) {

                return '<span class="badge badge-danger">Enviar a Courier</span>';

              /*
              El estado del sobre cambia a 1 y luego cambia el estado del pedido
              else if (row.envio=='1' && row.estado_sobre=='1'){
                return '<span class="badge badge-info">Sobre por Enviar</span>';
              }
              */


            },"visible":false,
          },
          {
            data: 'action',
            name: 'action',
            orderable: false,
            searchable: false,
            sWidth:'20%',

          },
        ],
        language: {
          "decimal": "",
          "emptyTable": "No hay información",
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

  @if (session('info') == 'registrado' || session('info') == 'actualizado' || session('info') == 'eliminado')
    <script>
      Swal.fire(
        'Pedido {{ session('info') }} correctamente',
        '',
        'success'
      )
    </script>
  @endif

  <script>

    //VALIDAR CAMPO CELULAR
    function maxLengthCheck(object)
    {
      if (object.value.length > object.maxLength)
        object.value = object.value.slice(0, object.maxLength)
    }

    //VALIDAR ANTES DE ENVIAR
    /*document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("formulario").addEventListener('submit', validarFormulario);
    });*/

    function validarFormulario(evento) {
      evento.preventDefault();
      var condicion = document.getElementById('condicion').value;
      var foto1 = document.getElementById('foto1').value;
      var pfoto1 = document.getElementById('pfoto1').value;
      var foto2 = document.getElementById('foto2').value;
      var pfoto2 = document.getElementById('pfoto2').value;

      if (condicion == 3) {
        if (foto1 == '' && pfoto1 == '') {
          Swal.fire(
            'Error',
            'Para dar por ENTREGADO debe registrar la foto 1',
            'warning'
          )
        }
        else if (foto2 == '' && pfoto2 == ''){
          Swal.fire(
            'Error',
            'Para dar por ENTREGADO debe registrar la foto 2',
            'warning'
          )
        }
        else {
        this.submit();
        }
      }
      else {
        this.submit();
      }
    }
  </script>

  <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>

  <script>
    /* Custom filtering function which will search data in column four between two values */
        $(document).ready(function () {


            $("#destino", this).on( 'keyup change', function () {
              if ( table.column(i).search() !== this.value ) {
                  table
                      .column(8)
                      .search( this.value )
                      .draw();
                }
            } );

        });
  </script>

@stop
