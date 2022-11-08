@extends('adminlte::page')

@section('title', 'Lista de Pagos')

@section('content_header')
  <h1>Lista de Voucher POR REVISAR
    @can('pagos.create')
      {{--<a href="{{ route('pagos.create') }}" class="btn btn-info"><i class="fas fa-plus-circle"></i> Agregar</a>--}}
    @endcan
    <div class="float-right btn-group dropleft">
      <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        Exportar
      </button>
       <div class="dropdown-menu">
        <a href="{{ route('porrevisarExcel') }}" class="dropdown-item"><img src="{{ asset('imagenes/icon-excel.png') }}"> EXCEL</a>
      </div> 
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
      <div class="form-group col-lg-6">
      
        <select name="asesores_pago" class="border form-control selectpicker border-secondary" id="asesores_pago" data-live-search="true">
          <option value="">---- SELECCIONE ASESOR ----</option>         
        </select>
      </div>

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
      </table>
      <br>
      <table id="tablaPrincipal" class="table table-striped">
        <thead>
          <tr>
          <th scope="col">COD.</th>
            <th scope="col">COD.</th>
            <th scope="col">Cliente</th>
            <th scope="col">Codigo pedido</th>
            <th scope="col">Fecha Voucher</th>
            <th scope="col">Asesor</th>
            <th scope="col">Observacion</th>
            {{--<th scope="col">Total cobro</th>--}}
            <th scope="col">Total pagado</th>
            <th scope="col">Estado</th>
            <th scope="col">Acciones</th>
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
      @include('pagos.modals.modalDeleteId')
    </div>
  </div>

@stop

@section('css')
  {{-- <link rel="stylesheet" href="../css/admin_custom.css">--}}
  <link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">  
  <style>
    .yellow {
      color:#fcd00e !important;
    }
    .red {
      background-color: red !important;
    }
      
    .white {
      background-color: white !important;
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

  <!--<script src="{{ asset('js/datatables.js') }}"></script>--> 
  <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>

  <script>
    function clickformdelete()
    {
      console.log("action delete action")
      var formData = $("#formdelete").serialize();
      console.log(formData);
      $.ajax({
        type:'POST',
        url:"{{ route('pagodeleteRequest.post') }}",
        data:formData,
      }).done(function (data) {
        $("#modal-delete").modal("hide");
        resetearcamposdelete();          
        $('#tablaPrincipal').DataTable().ajax.reload();      
      });
    }
  </script>

<script src="//cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js" type="text/javascript"></script>
<script src="//cdn.datatables.net/plug-ins/1.10.15/sorting/datetime-moment.js" type="text/javascript"></script>

<script>
  $(document).ready(function () {

    $.fn.dataTable.moment( 'DD/MM/YYYY' );
    

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    //$('#asesorespago').change(function(){

      $.ajax({
        url: "{{ route('asesorespago') }}",
        method: 'GET',
        success: function(data) {
          console.log(data.html);
          $('#asesores_pago').html(data.html);
          $('#asesores_pago').selectpicker('refresh');
        }
      });

      $(document).on("change","#asesores_pago",function(){

        $('#tablaPrincipal').DataTable().ajax.reload();

      });

    //});

    //para opcion eliminar  pagos
    $('#modal-delete').on('show.bs.modal', function (event) {     
      var button = $(event.relatedTarget) 
      var idunico = button.data('delete')      
      $("#hiddenId").val(idunico);
      if(idunico<10){
        idunico='PAG000'+idunico;
      }else if(idunico<100){
        idunico= 'PAG00'+idunico;
      }else if(idunico<1000){
        idunico='PAG0'+idunico;
      }else{
        idunico='PAG'+idunico;
      }
      $(".textcode").html(idunico);
    });

    $(document).on("submit", "#formdelete", function (evento) {
      evento.preventDefault();
      console.log("validar delete");
      clickformdelete();
    })

    //$.fn.dataTable.ext
    

    //administracion.porrevisartabla
    $('#tablaPrincipal').DataTable({
        processing: true,
        serverSide: true,
        searching: true,
        "order": [[ 0, "desc" ]],
        ajax: {
          url: "{{ route('administracion.porrevisartabla') }}",
          data: function (d) {
            d.asesores = $("#asesores_pago").val();
            d.min = $("#min").val();
            d.max = $("#max").val();
            // d.custom = $('#myInput').val();
            // etc
          },
        },
        /*createdRow: function( row, data, dataIndex){           
        },*/
        /*rowCallback: function (row, data, index) {           
        },*/
        //"columnDefs": [{"targets":3,"type":"date-eu"}],
        columns: [
          {
              data:'fecha_timestamp',
              name:'fecha_timestamp',
              //"visible": false
          },
          {
              data: 'id', 
              name: 'id',
              render: function ( data, type, row, meta ) {  
                var cantidadvoucher=row.cantidad_voucher;
                var cantidadpedido=row.cantidad_pedido;
                var unido= ( (cantidadvoucher>1)? 'V':'I' )+''+( (cantidadpedido>1)? 'V':'I' );
                if(row.id<10){
                  return 'PAG'+row.users+unido+'000'+row.id;
                }else if(row.id<100){
                  return 'PAG00'+row.users+unido+''+row.id;
                }else if(row.id<1000){
                  return 'PAG0'+row.users+unido+''+row.id;
                }else{
                  return 'PAG'+row.users+unido+''+row.id;
                } 
              }
          },
          {data: 'celular', name: 'celular'},
          {
            data: 'codigos'
            , name: 'codigos' 
            , render: function ( data, type, row, meta ) {
              /*var jsonArray = JSON.parse(JSON.stringify(data));*/
              var returndata='';
              if(data==null)
              {
                return "SIN PEDIDOS";
              }else{
                var jsonArray=data.split(",");
                $.each(jsonArray, function(i, item) {
                    returndata+=item+'<br>';
                });
              }
              
              return returndata;
              //return data;
            }
          },
          { data: 'fecha', name: 'fecha' },////asesor
          { data: 'users', name: 'users' },////asesor
          { data: 'observacion', name: 'observacion'},//observacion
          //{ data: 'total_deuda', name: 'total_deuda'},//total_deuda
          { data: 'total_pago', name: 'total_pago'},//total_pago
          {
            data: 'condicion', 
            name: 'condicion', 
            render: function ( data, type, row, meta ) {            
              return data;             
            }
          },//estado
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
{{--<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.8.4/moment.min.js"></script>--}}
{{--<script src="https://cdn.datatables.net/plug-ins/1.10.25/sorting/datetime-moment.js"></script>--}}



<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>

<script>
  $(document).ready(function () { 

    //$.fn.dataTable.moment( 'DD/MM/YYYY' );

    $("#min").datepicker({ onSelect: function () { /*table.draw();*/ }, changeMonth: true, changeYear: true , dateFormat:"dd/mm/yy"});
      
    $("#max").datepicker({ onSelect: function () { /*table.draw();*/ }, changeMonth: true, changeYear: true, dateFormat:"dd/mm/yy" });
  });
</script>

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
