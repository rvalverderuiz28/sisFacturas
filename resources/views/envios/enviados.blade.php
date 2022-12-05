@extends('adminlte::page')

@section('title', 'Lista de pedidos entregados')

@section('content_header')
  <h1>Lista de pedidos entregados - ENVIOS
       
    <div class="float-right btn-group dropleft">
      <?php if(Auth::user()->rol=='Administrador' || Auth::user()->rol=='Logística'){ ?>
      <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        Exportar
      </button>
      <?php } ?>
      <div class="dropdown-menu">
        <a href="" data-target="#modal-exportar" data-toggle="modal" class="dropdown-item" target="blank_"><img src="{{ asset('imagenes/icon-excel.png') }}"> Excel</a>
      </div>
    </div>
    @include('pedidos.modal.exportar', ['title' => 'Exportar pedidos ENTREGADOS', 'key' => '2'])
    
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
      <table cellspacing="5" cellpadding="5">
        <tbody>
          <tr>
            <td>Minimum date:</td>
            <td><input type="text" value={{ $dateMin }} id="min" name="min" class="form-control"></td>
            <td> </td>
            <td>Maximum date:</td>
            <td><input type="text" value={{ $dateMax }} id="max" name="max"  class="form-control"></td>
          </tr>
        </tbody>
      </table><br>
      <table id="tablaPrincipal" style="width:100%;" class="table table-striped">
        <thead>
          <tr>
            <th scope="col">Item</th>
            <th scope="col">Código</th>
            <th scope="col">Asesor</th>
            <th scope="col">Cliente</th>
            <th scope="col">Razón social</th>
            <th scope="col">Foto 1</th>
            <th scope="col">Foto 2</th>
            <th scope="col">Estado de envio</th>            
          </tr>
        </thead>
        <tbody>
        </tbody>
      </table>
      @include('pedidos.modal.editenviarid')
      @include('pedidos.modal.verenvioid')      
      @include('pedidos.modal.imagenid')
      @include('pedidos.modal.imagen2id')
      @include('pedidos.modal.atenderid')
      @include('pedidos.modal.DeleteFoto1id')
      @include('pedidos.modal.DeleteFoto2id')
      @include('envios.modal.CambiarImagen')
      @include('envios.modal.CambiarImagen2')
    </div>
  </div>

@stop

@section('css')
  <link rel="stylesheet" href="../css/admin_custom.css">
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
<script>
  $("#penvio_doc").change(mostrarValores1);

  function mostrarValores1() {
    $("#envio_doc").val($("#penvio_doc option:selected").text());
  }

  $("#pcondicion").change(mostrarValores2);

  function mostrarValores2() {
    $("#condicion").val($("#pcondicion option:selected").text());
  }
</script>

  
  <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>

  <script>
    $(document).ready(function () {

      $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
      });

      $('#modal-imagen').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget) 
        var idunico = button.data('imagen');
        var str="storage/entregas/"+idunico;
        var urlimage = '{{ asset(":id") }}';
        urlimage = urlimage.replace(':id', str);
        $("#modal-imagen .img-thumbnail").attr("src",urlimage);        
      });

      $('#modal-cambiar-imagen').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget) 
        var imagen = button.data('imagen');
        var pedido = button.data('pedido');
        var item = button.data('item');
        
        var str="storage/entregas/"+imagen;
        var urlimage = '{{ asset(":id") }}';
        urlimage = urlimage.replace(':id', str);
        urlimage = urlimage.replace(' ', '%20');
        console.log(urlimage)
        $("#picture").attr("src",urlimage); //cambiar imnagen
        //campos ocultos
        $("#cambiapedido").val(pedido);
        $("#cambiaitem").val(item);
      });


      

      $('#modal-imagen2').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget) 
        var idunico = button.data('imagen');
        var str="storage/entregas/"+idunico;
        var urlimage = '{{ asset(":id") }}';

        urlimage = urlimage.replace(':id', str);
        $("#modal-imagen2 .img-thumbnail").attr("src",urlimage);        
      });
      

      $(document).on("submit", "#formulario", function (evento) {
        evento.preventDefault();
        var fd = new FormData();
      });

      /*$('#modal-verenvio').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget) 
        var idunico = button.data('verenvio')
        $("#modal-verenvio .textcode").html("PED"+idunico);
        $("#hiddenVerenvio").val(idunico);
        $("#fecha_envio_doc_fis").val("");
        $("#fecha_recepcion").val("");
        $("#condicion").val("");
      });*/

      $('#modal-editenviar').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget) 
        var idunico = button.data('editenviar')
        $("#modal-editenviar .textcode").html("PED"+idunico);
        $("#hiddenEditenviar").val(idunico);

        //ajax para obtener los datos
      });

      $(document).on("submit", "#formularioVerenvio", function (evento) {
        evento.preventDefault();
        let item=$("#hiddenVerenvio").val();
        //console.log(item)
      });

      $(document).on("submit", "#formularioEditenviar", function (evento) {
        evento.preventDefault();
        let item=$("#hiddenEditenviar").val();
        //console.log(item);
      });

      $(document).on("click","#change_imagen",function(){
        var fd2 = new FormData();
        //agregados el id pago
        let files=$('input[name="pimagen')
        var cambiaitem=$("#cambiaitem").val();
        var cambiapedido=$("#cambiapedido").val();        

        fd2.append("item",cambiaitem )
        fd2.append("pedido",cambiapedido )
        for (let i = 0; i < files.length; i++) {
          fd2.append('adjunto', $('input[type=file][name="pimagen"]')[0].files[0]);
        }

        $.ajax({
          data: fd2,
          processData: false,
          contentType: false,
          type: 'POST',
          url:"{{ route('envios.changeImg') }}",
          success:function(data){
            console.log(data);
            if(data.html=='0')
            {
            }else{
              $("#modal-cambiar-imagen").modal("hide");
              var urlimg = "{{asset('imagenes/logo_facturas.png')}}";
              urlimg = urlimg.replace('imagenes/', 'storage/entregas/');
              urlimg = urlimg.replace('logo_facturas.png', data.html);
              urlimg = urlimg.replace(' ', '%20');
              console.log(urlimg);
              $("#imagen_"+cambiapedido+'-'+cambiaitem).attr("src", urlimg );
            }
          }
        });

      });

      $(document).on("change","#pimagen",function(event){
        console.log("cambe image")
        var file = event.target.files[0];
        var reader = new FileReader();
        reader.onload = (event) => {
          //$("#picture").attr("src",event.target.result);
            document.getElementById("picture").setAttribute('src', event.target.result);
        };
        reader.readAsDataURL(file);

      });

      $('#tablaPrincipal').DataTable({
        processing: true,
        serverSide: true,
        searching: true,
        "order": [[ 0, "desc" ]],
        ajax: "{{ route('envios.enviadostabla') }}",
        createdRow: function( row, data, dataIndex){
          //console.log(row);          
        },
        rowCallback: function (row, data, index) {
            console.log(data.destino2)
              if(data.destino2=='PROVINCIA'){
                $('td', row).css('color','red')                
              }else if(data.destino2=='LIMA'){
                if(data.distribucion!=null)
                {
                  if(data.distribucion=='NORTE')
                  {
                    //$('td', row).css('color','blue')
                  }else if(data.distribucion=='CENTRO')
                  {
                    //$('td', row).css('color','yellow')
                  }else if(data.distribucion=='SUR')
                  {
                    //$('td', row).css('color','green')
                  }                  
                }else{                  
                }
              }
        },
        columns: [
          {
              data: 'id', 
              name: 'id',
              render: function ( data, type, row, meta ) {
                if(row.id<10){
                  return 'ENV000'+row.id;
                }else if(row.id<100){
                  return 'ENV00'+row.id;
                }else if(row.id<1000){
                  return 'ENV0'+row.id;
                }else{
                  return 'ENV'+row.id;
                } 
              }
          },
          {
            data: 'codigos', 
            name: 'codigos', 
            render: function ( data, type, row, meta ) {    
              if(data==null){
                return 'SIN PEDIDOS';
              }else{
                var returndata='';
                var jsonArray=data.split(",");
                $.each(jsonArray, function(i, item) {
                    returndata+=item+'<br>';
                });
                return returndata;
              }  
            }
          },
          {
            data: 'identificador', 
            name: 'identificador',
          },
          {
            data: 'celular', 
            name: 'celular',
            render: function ( data, type, row, meta ) {
              return row.celular+' - '+row.nombre
            },
          },
          {
            data: 'producto', 
            name: 'producto',
            render: function ( data, type, row, meta ) {    
              if(data==null){
                return 'SIN RUCS';
              }else{
                var numm=0;
                var returndata='';
                var jsonArray=data.split(",");
                $.each(jsonArray, function(i, item) {
                    numm++;
                    returndata+=numm+": "+item+'<br>';
                    
                });
                return returndata;
              }  
            }
          },          
          {
            data: 'foto1', 
            name: 'foto1',
            render: function ( data, type, row, meta ) {
              datass=''
              if(data!=null)
              {
                var urlimagen1 = '{{ asset("storage/entregas/:id") }}';
                urlimagen1 = urlimagen1.replace(':id', data);
                datass=datass+'<a href="" data-target="#modal-imagen" data-toggle="modal" data-imagen="'+data+'">'+
                    '<img src="'+urlimagen1+'" alt="'+data+'" height="200px" width="200px" id="imagen_'+row.id+'-1" class="img-thumbnail">'+
                    '</a>';
                urldescargar = '{{ route("envios.descargarimagen", ":id") }}';
                urldescargar = urldescargar.replace(':id', data);

                datass=datass+'<a href="'+urldescargar+'" class="text-center"><button type="button" class="btn btn-secondary btn-md"> Descargar</button> </a>';

                datass=datass+'<a href="" data-target="#modal-cambiar-imagen" data-toggle="modal" data-item="1" data-imagen="'+data+'" data-pedido="'+row.id+'">'+
                                  '<button class="btn btn-danger btn-md">Cambiar</button>'+
                                  '</a>';

                @if (Auth::user()->rol == "Asesor")
                    datass=datass+'<a href="" data-target="#modal-delete-foto1" data-toggle="modal" data-deletefoto1="'+row.id+'">'+
                      '<button class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></button>'+
                      '</a>';
                @endif 
                return datass;

              }else if(row.envio == '3'){
                return '<span class="badge badge-dark">Sin envio</span>';
              }else{
                return '<span class="badge badge-dark">Sin envio</span>';
              }
            },
          },
          {
            data: 'foto2', 
            name: 'foto2',
            render: function ( data, type, row, meta ) {
              datass=''
              if(data!=null)
              {
                var urlimagen2 = '{{ asset("storage/entregas/:id") }}';
                urlimagen2 = urlimagen2.replace(':id', data);
                datass=datass+'<a href="" data-target="#modal-imagen2" data-toggle="modal" data-imagen="'+data+'">'+
                    '<img src="'+urlimagen2+'" alt="'+data+'" height="200px" width="200px" id="imagen_'+row.id+'-2" class="img-thumbnail">'+
                    '</a>';
                urldescargar = '{{ route("envios.descargarimagen", ":id") }}';
                urldescargar = urldescargar.replace(':id', data);

                datass=datass+'<a href="'+urldescargar+'" class="text-center"><button type="button" class="btn btn-secondary btn-md"> Descargar</button> </a>';

                datass=datass+'<a href="" data-target="#modal-cambiar-imagen" data-toggle="modal" data-item="2" data-imagen="'+data+'" data-pedido="'+row.id+'">'+
                                  '<button class="btn btn-danger btn-md">Cambiar</button>'+
                                  '</a>';

                @if (Auth::user()->rol == "Asesor")
                    datass=datass+'<a href="" data-target="#modal-delete-foto1" data-toggle="modal" data-deletefoto2="'+row.id+'">'+
                      '<button class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i></button>'+
                      '</a>';
                @endif 
                return datass;

              }else if(row.envio == '3'){
                return '<span class="badge badge-dark">Sin envio</span>';
              }else{
                return '<span class="badge badge-dark">Sin envio</span>';
              }
            },
          },
          {
            data: 'condicion_envio', 
            name: 'condicion_envio',
            render: function ( data, type, row, meta ) 
            {
              if(row.subcondicion_envio==null)
              {
                return row.condicion_envio;
              }else{
                return '('+row.subcondicion_envio+') '+ row.condicion_envio;
              }
              
            }
          },          
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
    /* Custom filtering function which will search data in column four between two values */
        $(document).ready(function () { 
        
            $.fn.dataTable.ext.search.push(
                function (settings, data, dataIndex) {
                    var min = $('#min').datepicker("getDate");
                    var max = $('#max').datepicker("getDate");
                    // need to change str order before making  date obect since it uses a new Date("mm/dd/yyyy") format for short date.
                    var d = data[6].split("/");
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
            var table = $('#tablaPrincipal').DataTable();

            // Event listener to the two range filtering inputs to redraw on input
            $('#min, #max').change(function () {
                table.draw();
            });
        });
  </script>
  
@stop