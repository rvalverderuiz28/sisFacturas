@extends('adminlte::page')

@section('title', 'Agregar Pago')

@section('content_header')
  <h1>Agregar pago</h1>
@stop

@section('content')
  <div class="card">
     {!! Form::open(['route' => 'pagos.store','enctype'=>'multipart/form-data', 'id'=>'formulario','files'=>true]) !!} 
   
      <div class="border rounded card-body border-secondary" style="margin: 1%">
        <div class="form-row">
          <div class="form-group col-lg-6">
            {!! Form::label('user_id', 'Asesor') !!}
          
            <select name="user_id" class="border form-control  border-secondary selectpicker" id="user_id" data-live-search="true" >
                <option value="">---- SELECCIONE ASESOR ----</option> 
              </select>


              
          </div>
          <div class="form-group col-lg-6">

            {!! Form::label('cliente_id', 'Cliente*') !!}{!! Form::hidden('cliente_id', '',['id' => 'cliente_id']) !!}
              <div class="pr-2 btn border-0 rounded text-right">
                <small class="rounded mb-2 bg-danger text-white" style="font-size: 16px">Sin Deuda</small>
                <small class="rounded mb-2 bg-info text-white" style="font-size: 16px">Deuda reciente</small>
                <small class="rounded mb-2 bg-dark text-white" style="font-size: 16px">Deudas</small>
              </div>

              <select name="pcliente_id" class="border form-control selectpicker border-secondary" id="pcliente_id" data-live-search="true">
                
              </select>
          </div>
        </div>
      </div>
      <div class="card-body">
        <div class="form-row">



        <div class="form-group col-lg-6">
            <div class="form-row" >
              <div class="form-group col-lg-2">
                <h2>PAGOS  <b style="font-size:20px"> {!! Form::label('', '') !!}</b></h2>

                <input type="hidden" id="accion_perdonar" name="accion_perdonar" value="">

              </div>

              <div class="form-group col-lg-3">
                @if (Auth::user()->rol == "Administrador")
                <button class="btn btn-info" id="btn-perdonar-deuda" type="button">PERDONAR DEUDA</button>
                @endif
              </div>

              <div class="form-group col-lg-3">
                <button class="btn btn-warning" id="btn-accion-perdonar-currier" type="button">AGREGAR IMPORTE</button>
              </div>
              
              <div class="form-group col-lg-4 text-center">
                <a data-target="#modal-add-pagos" id="addpago" data-toggle="modal"><button class="btn btn-primary"><i class="fas fa-plus-circle"></i></button></a>
              </div>
            </div>          
              @error('imagen')
                <small class="text-danger">{{$message}}</small>
              @enderror
            <div class="table-responsive">
              <table id="tabla_pagos" class="table table-striped">
                <thead class="bg-primary">
                  <tr>
                    <th scope="col">ACCIÓN</th>
                    <th scope="col">#</th>
                    <th scope="col">T.MOV.</th>
                    <th scope="col">TITULAR</th>               
                    <th scope="col">BANCO</th>
                    <th scope="col">BANCO P</th>
                    <th scope="col">O BANCO</th>
                    <th scope="col">FECHA</th>
                    <th scope="col">IMAGEN</th>
                    <th scope="col">MONTO</th>
                    <th scope="col">OPERACION</th>
                    <th scope="col">NOTA</th>
                  </tr>
                </thead>
                <tfoot>
                  <th style="text-align: center">TOTAL</th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th colspan="2" style="text-align: right"><h4 id="total_pago">S/. 0.00</h4></th>
                  <th><input type="text" name="total_pago_pagar" requerid value="" id="total_pago_pagar" class="form-control"></th>  
                </tfoot>
                <tbody>
                </tbody>
              </table>
            </div>
          </div>
          
          <div class="form-group col-lg-6">
            <div class="form-row">
              <div class="form-group col-lg-6">
                <h2>PEDIDOS A PAGAR</h2>
              </div>
              <div class="form-group col-lg-6">
               
              </div>
            </div>
            <div class="table-responsive">
              <table id="tabla_pedidos" class="table table-striped" style="text-align: center">
                <thead class="bg-info">
                  <tr>
                    <th scope="col">ITEM</th>
                   
                    <th scope="col">CODIGO</th>
                  
                    <th scope="col">SALDO</th>
                    <th scope="col">DIFERENCIA</th>
                   
                      <th>TOTAL</th>
                      <th>ADELANTO</th>
                  </tr>
                </thead>
                <tfoot>
                  <tr>
                    <td></td>
                    <td></td>
                   
                    <td>TOTAL SALDO</td>
                    <td>TOTAL DIFERENCIA</td>
                    
                    <td></td>
                    <td></td>
                  </tr>
                </tfoot>
                <tbody style="text-align: center">
                </tbody>
                
              </table>
            </div>
          </div>

          


        </div>
      {{-- MODALS --}}
      @include('pagos.modals.AddPedidos')
      @include('pagos.modals.AddPagos')
      @include('pagos.modals.AddPerdonarDeuda')
    </div>
    <div class="card-footer">
      <div class="form-row">
        
        <div class="form-group col-lg-1">
          <div id="guardar">
            <button id="registrar_pagos" type="submit" class="btn btn-success"><i class="fas fa-save"></i> Guardar</button>
          </div>
        </div>

        <div class="form-group col-lg-1">
          @if (Auth::user()->rol == "Asesor")
            <a class="btn btn-danger" href="{{ route('pagos.mispagos') }}"><i class="fas fa-times-circle"></i> ATRAS</a>
          @else
            <a class="btn btn-danger" href="{{ route('pagos.index') }}"><i class="fas fa-times-circle"></i> ATRAS</a>
          @endif
        </div>

        <div class="form-group col-lg-2">
        </div>

        <div class="form-group col-lg-1 text-right">
          <span style="color: red; font-weight:bold; font-weight: 900; font-size:21px">SALDO S/:</span>
        </div>

        <div class="form-group col-lg-1 text-left">
          <input type="text" name="diferencia" value="" disabled id="diferencia" class="form-control" style="color: red; font-weight:bold;  font-size:21px" readonly="readonly"> 

          <input type="hidden" name="diferenciaantes" value="" disabled id="diferenciaantes" class="form-control" style="color: blue; font-weight:bold;  font-size:21px"> 
        </div>
        
        <div class="form-group col-lg-1">
          <input type="hidden" name="saldo" id="saldo" class="form-control number" placeholder="Saldo a favor...">
        </div>

        <div class="form-group col-lg-3">

        </div>

        <div class="form-group col-lg-1" style="text-align:center;">
          <div id="considerasaldo" class="d-none">
            <a class="btn btn-danger" disabled href="#"><i class="fas fa-times-circle"></i> Saldo</a>
          </div>
        </div>

        <div class="form-group col-lg-1" style="text-align:center;">
          <div id="consideradevolucion" class="d-none">
            <a class="btn btn-danger" disabled href="#"><i class="fas fa-times-circle"></i> Devolucion</a>
          </div>
        </div>

      </div>
    </div>
    {!! Form::close() !!}
  </div>
@stop

@section('css')
<style>
tfoot tr, thead tr {
	background: lightblue;
}
tfoot td {
	font-weight:bold;
}
</style>
@stop

@section('js')
  <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
  <script>  

    $("#guardar").hide();
    $("#addpedido").hide();
    $("#addpago").hide();
    $("#btn-accion-perdonar-currier").hide();
    $("#btn-perdonar-deuda").hide();
    $("#pcliente_id").change(mostrarBotones);

    function mostrarBotones() {
      $("#addpedido").show();
      $("#addpago").show();

      $("#btn-perdonar-currier").show();
      $("#btn-perdonar-deuda").show();

    }    

    // CARGAR PEDIDOS DE CLIENTE SELECCIONADO

    var tabla_pedidos=null;
    var total_pedido=0.00;
    var total_pago=0.00;
    

      function eliminarPa(index) {
        total_pago = total_pago - subtotal_pago[index];
        $("#total_pago").html("S/. " + total_pago.toLocaleString("en-US"));
        $("#total_pago_pagar").val(total_pago);
        $("#filasPa" + index).remove();
        evaluarPa();
      }

      function limpiarPa() {
        $("#pmonto").val("");
        $("#pbanco").val('').change();
        $("#pfecha").val("");
        $("#pimagen").val("");
      }

      function limpiarPaPerdonar() {
        $("#pmontoperdonar").val("");
        $("#pfechaperdonar").val("");
        $("#pimagen1").val("");
        $("#pimagen2").val("");
        $("#pimagen3").val("");
      }


      $(document).ready(function() {


        $(document).on("change","#pbanco",function(){
          if($(this).val()!='')
          $.ajax({
              type:'POST',
              url:"{{ route('titulares.banco') }}",
              data: {"banco" : $(this).val()},
          }).done(function (data) {
            console.log(data)

            
          });


        });
        $(document).on("click","#btn-perdonar-deuda",function(){

          $("#btn-accion-perdonar-currier").show();
          $("#addpago").hide();

          console.log("btn-perdonar-deuda")
          tabla_pedidos.destroy();

          tabla_pedidos=$('#tabla_pedidos').DataTable({
            "bPaginate": false,
            "bFilter": false,
            "bInfo": false,
            'ajax': {
              url:"{{ route('cargar.pedidosclientetabla') }}",					
              'data': { "cliente_id": $("#pcliente_id").val(),"diferencia":$("#diferencia").val(),"perdonar_deuda":1}, 
              "type": "get",
            },
            "fnCreatedRow": function( nRow, aData, iDataIndex ) {
                $(nRow).attr('id', aData["id"]);
            },
            columns: [
              {
                data: 'id', 
                name: 'id',
                render:function(data,type,row,meta){
                  if(row.id<10){
                    return '<input type="hidden" name="pedido_id['+data+']" value="' + data + '">PED000' + data + '</td>';
                  }else if(row.id<100){
                    return '<input type="hidden" name="pedido_id['+data+']" value="' + data + '">PED00' + data + '</td>';
                  }else if(row.id<1000){
                    return '<input type="hidden" name="pedido_id['+data+']" value="' + data + '">PED0' + data + '</td>';
                  }else{
                    return '<input type="hidden" name="pedido_id['+data+']" value="' + data + '">PED' + data + '</td>';
                  } 
                }
              },
              {data: 'codigo', name: 'codigo',},
              
              {
                data: 'saldo', 
                name: 'saldo',
                render:function(data,type,row,meta){
                    return '<input type="hidden" name="numbersaldo['+row.id+']" value="' + data + '"><span class="numbersaldo">' + data + '</span></td>';
                },
                "visible": true
              },
              {
                data: 'diferencia', 
                name: 'diferencia',
                render:function(data,type,row,meta){
                    return '<input type="hidden" name="numberdiferencia['+row.id+']" value="' + data + '"><span class="numberdiferencia">' + data + '</span></td>'+
                      '<input type="hidden" name="numbertotal['+row.id+']" value="' + data + '"><span class="numbertotal"></span></td>';
                },
                "visible": true
              },
              {
                  "data": null,
                  "render": function ( data, type, row, meta ) {                      
                      return '<input type="checkbox" disabled class="form-control radiototal" name="checktotal['+row.id+']" value="0">';
                  }
              },
              {
                  "data": null,
                  "render": function ( data, type, row, meta ) {                    
                    return '<input type="checkbox" disabled class="form-control radioadelanto" name="checkadelanto['+row.id+']" value="0">';
                  }
              }
            ],
            "footerCallback": function ( row, data, start, end, display ) {
              var api = this.api();
              nb_cols = 4;api.columns().nodes().length;
              var j = 2;

              var pageTotal = api
                    .column( 2, { page: 'current'} )
                    .data()
                    .reduce( function (a, b) {
                        return Number(a) + Number(b);
                    }, 0 );

              $( api.column( 2 ).footer() ).html('<input type="hidden" name="total_pedido" id="total_pedido" value="'+pageTotal.toFixed(2)+'"/>'+
                    'S/. '+separateComma(pageTotal.toFixed(2)).toLocaleString("en-US")  );

              var pageSaldo = api
                    .column( 3, { page: 'current'} )
                    .data()
                    .reduce( function (a, b) {
                        return Number(a) + Number(b);
                    }, 0 );
              // Update footer
              $( api.column( 3 ).footer() ).html('<input type="hidden" name="total_pedido_pagar" id="total_pedido_pagar" value="'+pageSaldo.toFixed(2)+'" />'+
                  'S/.'+separateComma(pageSaldo.toFixed(2)).toLocaleString("en-US")  );

            },
            "initComplete": function(settings, json) {
              total_pedido=sumatotalpedidos();
              console.log("total_pedido "+total_pedido);
            },
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
            
          });//fin datatable

        });



        $(document).on("click","#btn-accion-perdonar-currier",function(){
          console.log("btn-perdonar-currier")

          $("#modal-add-perdonar-deuda").modal("show");

          
         

        });

        $(".banco_procedencia").hide();
        $(".banco_procedencia_otro").hide();

        $(document).on("change","#tipotransferencia",function(event){
          console.log($(this).val());
          if($(this).val()=='INTERBANCARIO'){
            $("#pbancoprocedencia").val("").selectpicker("refresh");
            $("#otro_bancoprocedencia").val("");
            $(".banco_procedencia").show();
            $(".banco_procedencia_otro").hide();           
          }else{
            $(".banco_procedencia").hide();
            $(".banco_procedencia_otro").hide();
          }
        });

        $(document).on("change","#pbancoprocedencia",function(event){
          console.log($(this).val());
          if($(this).val()=='OTROS'){
            $("#otro_bancoprocedencia").val("");
            $(".banco_procedencia_otro").show();           
          }else{
            $(".banco_procedencia_otro").hide();
          }
        });

        /* inicio mi carga de table  sin datos*/
        $('#tabla_pagos').DataTable().clear().destroy();

        $('#modal-add-pagos').on('show.bs.modal', function (event) {
          //resetear campos
          $("#pbanco").val("").selectpicker("refresh");
          $("#tipotransferencia").val("").selectpicker("refresh");
          $("#titulares").val("").selectpicker("refresh");
          $("#pmonto").val("");
        });

        $(document).on("submit","#formulario",function(event){
          event.preventDefault();
          console.log("nuevo formulario")
          
          var accion_perdonar=$("#accion_perdonar").val();

          var total_pedido_pagar = document.getElementById('total_pedido_pagar').value.replace(",", "");
          var total_pedido = document.getElementById('total_pedido').value.replace(",", "");
          var total_pago_pagar = document.getElementById('total_pago_pagar').value.replace(",", "");
          var total_pago = document.getElementById('total_pago').value.replace(",", "");
          var falta = total_pedido_pagar - total_pago_pagar;
          falta = falta.toFixed(2);

          var faltainput=document.getElementById("diferencia").value.replace(",","");
          console.log("faltainput "+faltainput)

          var items_pedidos = $('#tabla_pedidos').DataTable().data().count();
          var items_pagos = $('#tabla_pagos').DataTable().data().count();

          //pedidos marcados
          var total_check_count=$(".radiototal").length;
          var saldo_check_count=$(".radioadelanto").length;
          console.log("total_check_count "+total_check_count)
          console.log("saldo_check_count "+saldo_check_count)

          

          var total_check_si = $('.radiototal').filter(':checked').length;
          var saldo_check_si = $('.radioadelanto').filter(':checked').length;
          console.log("total_check_si "+total_check_si);
          console.log("saldo_check_si "+saldo_check_si);

          var marcados=total_check_si+saldo_check_si;
          console.log("marcados "+marcados)

          console.log("items_pedidos "+items_pedidos)
          console.log("items_pagos "+items_pagos)

          console.log(total_pago_pagar+" "+total_pedido_pagar  )
          console.log(total_pago_pagar-total_pedido_pagar)

          var difdiftotales=total_pago_pagar-total_pedido_pagar;
          console.log("falta "+falta);

          faltainput=parseFloat(faltainput);

          console.log(" faltainput2 "+faltainput)
          

          if(items_pedidos==0)
          {
            Swal.fire(
                'Error',
                'No se puede ingresar un pago sin cargar pedidos',
                'warning'
              )
              return false;
          }else if(marcados==0)
          {
            Swal.fire(
                'Error',
                'No se puede ingresar un pago sin marcar algun pedido',
                'warning'
              )
              return false;
          }else if(items_pagos==0){
            Swal.fire(
                'Error',
                'No se puede ingresar un pago sin voucher',
                'warning'
              )
              return false;
          }else if(faltainput > 3) {
              Swal.fire(
                'Error',
                'No se puede ingresar un pago mayor a la deuda (diferencia mayor a 3) que tiene el cliente',
                'warning'
              )
              return false;
          }
          else {
              this.submit();
              
          }  
          //aqui
        });

        

        tabla_pagos=$('#tabla_pagos').DataTable({
            "bPaginate": false,
              "bFilter": false,
              "bInfo": false,
              columns: 
              [
                {
                  data: 'accion', 
                  name: 'accion',
                  sWidth:'10%', 
                  render: function ( data, type, row, meta ) {
                    return '<button type="button" class="btn btn-danger btn-sm remove" item="'+row.item+'"><i class="fas fa-trash-alt"></i>'+row.item+'</button>';
                  }
                },
                {
                  data: 'item', 
                  name: 'item',
                  sWidth:'10%', 
                },
                {
                  data: 'movimiento', 
                  name: 'movimiento',
                  sWidth:'10%', 
                  render: function ( data, type, row, meta ) {
                    return '<input type="hidden" name="tipomovimiento['+row.item+']" value="' + data + '"><span class="tipomovimiento">' + data + '</span></td>';
                  }
                },
                {
                  data: 'titular', 
                  name: 'titular',
                  sWidth:'10%',
                  render: function ( data, type, row, meta ) {
                    return '<input type="hidden" name="titular['+row.item+']" value="' + data + '"><span class="titular">' + data + '</span></td>';
                  }
                },
                {
                  data: 'banco', 
                  name: 'banco',
                  sWidth:'5%', 
                  render: function ( data, type, row, meta ) {
                    return '<input type="hidden" name="banco['+row.item+']" value="' + data + '"><span class="banco">' + data + '</span></td>';
                  }
                },
                {
                  data: 'bancop', 
                  name: 'bancop',
                  sWidth:'10%', 
                  render: function ( data, type, row, meta ) {
                    return '<input type="hidden" name="bancop['+row.item+']" value="' + data + '"><span class="bancop">' + data + '</span></td>';
                  },
                  "visible": false,
                },
                {
                  data: 'obanco', 
                  name: 'obanco',
                  sWidth:'10%', 
                  render: function ( data, type, row, meta ) {
                    return '<input type="hidden" name="obanco['+row.item+']" value="' + data + '"><span class="obanco">' + data + '</span></td>';
                  },
                  "visible": false,
                },
                {
                  data: 'fecha', 
                  name: 'fecha',
                  sWidth:'10%', 
                  render: function ( data, type, row, meta ) {
                    return '<input type="hidden" name="fecha['+row.item+']" value="' + data + '"><span class="fecha">' + data + '</span></td>';
                  }
                },                
                {
                  data: 'imagen', 
                  name: 'imagen',
                  sWidth:'10%', 
                  render: function ( data, type, row, meta ) {
                   
                    var str="storage/pagos/"+data;
                    var urlimage = '{{ asset(":id") }}';

                    urlimage = urlimage.replace(':id', str);
                    data = '<input type="hidden" name="imagen['+row.item+']" value="' + data + '"></td><img src="'+urlimage+'" alt="'+urlimage+'" height="200px" width="200px" class="img-thumbnail">';
                          
                    return data

                  }
                },
                {
                  data: 'monto', 
                  name: 'monto',
                  sWidth:'10%', 
                  render: function ( data, type, row, meta ) {


                    return '<input type="hidden" name="monto['+row.item+']" value="' + data + '"><span class="monto">' + data + '</span></td>';
                  }
                },  
                {
                  data: 'operacion', 
                  name: 'operacion',
                  sWidth:'10%', 
                  "visible": false,
                  render: function ( data, type, row, meta ) {
                    return '<input type="hidden" name="operacion['+row.item+']" value="' + data + '"><span class="operacion">' + data + '</span></td>';
                  }
                },
                {
                  data: 'nota', 
                  name: 'nota',
                  sWidth:'10%', 
                  "visible": false,
                  render: function ( data, type, row, meta ) {
                    return '<input type="hidden" name="nota['+row.item+']" value="' + data + '"><span class="nota">' + data + '</span></td>';
                  }
                },                  
              ],
              "footerCallback": function ( row, data, start, end, display ) {
                var api = this.api();
                nb_cols = 4;api.columns().nodes().length;
                var j = 2;

                //para footer  monto
                var pageTotal = api
                      .column( 9, { page: 'current'} )
                      .data()
                      .reduce( function (a, b) {
                          return Number(a) + Number(b);
                      }, 0 );
                // Update footer

                $( api.column( 9 ).footer() ).html('<input type="hidden" name="total_pago" id="total_pago" value="'+pageTotal.toFixed(2)+'"/>'+
                    'S/. '+separateComma(pageTotal.toFixed(2)).toLocaleString("en-US") );

                $( api.column( 9 ).footer() ).html('<input type="hidden" name="total_pago_pagar" id="total_pago_pagar" value="'+pageTotal.toFixed(2)+'" />'+
                    'S/.'+separateComma(pageTotal.toFixed(2)).toLocaleString("en-US") );

                $("#diferencia").val(pageTotal);
                $("#saldo").val(0.00);
                let uncliente=$("#pcliente_id").val();
                if(uncliente!='')
                {
                
                }


                diferenciaFaltante();

              },
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

        function sumatotalpagos()
        {
          var sisuma=parseFloat(0.00);
          var nuevosuma=parseFloat(0.00);

          $('#tabla_pagos > tbody  > tr').each(function(index,tr) {
              
              console.log(  $.trim( $(this).find("td").eq(6).find(".monto").html())  )
          
              nuevosuma =  parseFloat( $.trim( $(this).find("td").eq(6).find(".monto").html()) );

              console.log("nuevosuma1  "+nuevosuma)
              sisuma=(sisuma)+(nuevosuma);
             

          });
          return sisuma

        }

        function sumatotalpedidos()
        {
          var sisumapedido=parseFloat(0.00);
          var nuevosumapedido=parseFloat(0.00);
          $('#tabla_pedidos > tbody  > tr').each(function(index,tr) {
              console.log(index+" posicion");
              console.log(tr+" tr");
              console.log( $(this).find("td").eq(2).html()) ;
              nuevosumapedido=parseFloat( $(this).find("td").eq(2).find(".numbersaldo").text() );
              console.log("nuevosuma "+nuevosumapedido);
              sisumapedido=(sisumapedido)+(nuevosumapedido);
              console.log("sumo "+sisumapedido)
          });
          return sisumapedido
        }

        function evaluarPa() {
          console.log("sumatotalpagos >1")
          var total_pago=sumatotalpagos();
          console.log("evaluarPa "+total_pago)
          if (total_pago > 0) { 
            $("#guardar").show();
          } else {
            $("#guardar").hide();
          }
        }


        $('#tabla_pagos').on('click', '.remove', function() {
          //si elimino pago, recargo datatable pedidos y diferencia vuelvo a calcular con la suma de pagos
          var table = $('#tabla_pagos').DataTable();
          var row = $(this).parents('tr');
          var item = $(this).attr('item');
          var subtotal=row.find("td").eq("9").find("span.monto").text();
          console.log(subtotal);
          console.log("item es "+item)
          let diff=$("#diferencia").val();
      
          if ($(row).hasClass('child')) {
            console.log("eliminar")
            table.row($(row).prev('tr')).remove().draw();
            console.log("sumatotalpagos >2")
            var sumapago=sumatotalpagos();
            console.log("sumapagos v1 "+sumapago)
          } else {
            console.log("eliminar eliminar")
            table
              .row($(this).parents('tr'))
              .remove()
              .draw();
            console.log("sumatotalpagos <3")
            var sumapago=sumatotalpagos();
            
            console.log("sumapagos v2 "+sumapago)

            

            $("#total_pago").html("S/. " + separateComma(sumapago).toLocaleString("en-US"));
            $("#total_pago_pagar").val(sumapago);
            $("#diferencia").val(sumapago);
            $("#pcliente_id").trigger("change");
            console.log("b")

              
              
              evaluarPa();
            
          }
      
        });
              

        tabla_pedidos=$('#tabla_pedidos').DataTable({
          "bPaginate": false,
              "bFilter": false,
              "bInfo": false,
          columns: 
          [
            {
              data: 'id'
            },
            {
              data: 'codigo'
            },
            {
              data: 'saldo'
            },
            {
              data: 'diferencia'
            },
            {
              data: null
            },{
              data: null
            }
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
              }
        });

        $(document).on("change","#user_id",function(){
          console.log("123");
          var uid=$(this).val();
          console.log({{ $idcliente_request }});
          $.ajax({
              async:true,
                url: "{{ route('clientescreatepago') }}?user_id=" + uid,
                method: 'GET',
                success: function(data) {
                  console.log(data.html);
                  $('#pcliente_id').html(data.html);
                  $("#pcliente_id").selectpicker("refresh");
                  console.log("c");
                  

                  if (!localStorage.getItem('clickpagar')) 
                  {
                    $("#pcliente_id").val(localStorage.getItem('clickpagar'));
                    $("#pcliente_id").selectpicker("refresh");
                  }

                }
              });

        });

        $(document).on("click",".radiototal",function(event){
          event.preventDefault();
        });

        $(document).on("click",".radioadelanto",function(event){
          event.preventDefault();
        });

        $(document).on("mousedown",".radiototal",function(event){
          event.preventDefault();
          if($(this).prop("checked") == true){
            console.log("estuvo marcado total")
            $(this).prop("checked",false).val("0")////
            //revertir
                let montopagos=parseFloat($("#diferencia").val().replace(",", ""));
                if(montopagos==null || isNaN(montopagos)){
                  console.log("no hay pagos ingresados");
                  return;
                }

                let filedata=tabla_pedidos.row($(this).closest('tr')).data();
                console.log(filedata)
                let pedidosaldo=parseFloat(filedata.saldo);
                if(pedidosaldo==null || isNaN(pedidosaldo)){
                  console.log("no hay saldo ingresado");
                  return;
                }

                console.log("desmarco saldo (1) "+pedidosaldo+" monto "+montopagos)

                montopagos=parseFloat(montopagos+pedidosaldo);
                console.log("diferencia "+montopagos);
                $("#diferencia").val(montopagos);
                
                $(this).closest('tr').find("td").eq(3).find(":input").val(pedidosaldo.toFixed(2));
                $(this).closest('tr').find("td").eq(3).find(".numberdiferencia").text(pedidosaldo.toFixed(2));
                let totalafterdifer1=$(this).closest('tr').find("td").eq(2).find(".numbersaldo").val();
                let totalafterdifer2=$(this).closest('tr').find("td").eq(3).find(".numberdiferencia").val();
                let totalafterdifer=parseFloat(totalafterdifer1-totalafterdifer2);
                $(this).closest('tr').find("td").eq(3).find(".numbertotal").val(totalafterdifer);

                $(this).closest('tr').find(".radioadelanto").prop("disabled",false);
                var idfila=$(this).closest('tr').find("td").eq(0).find(":input").val();
                $('#tabla_pedidos > tbody  > tr').each(function(index,tr) {
                    var index1=$(this).find("td").eq(0).find(":input").val();                      
                    console.log("idfila "+idfila+" index "+index1)

                    if(montopagos==0)
                    {
                      if(idfila!=index1)
                      {
                        $(this).find("td").eq(4).find("input").prop("disabled",true);
                        $(this).find("td").eq(5).find("input").prop("disabled",true);
                      }
                    }else{
                      console.log("sige aqui")
                      //if(idfila!=index1)
                      {
                        var saldofila=parseFloat($(this).find("td").eq(2).find(":input").val());
                        var radiototalfila=$(this).find("td").eq(4).find("input").prop("checked");
                        var radiosaldofila=$(this).find("td").eq(5).find("input").prop("checked");

                        if(montopagos>=saldofila)
                        {
                          if(!radiototalfila && !radiosaldofila)
                          {
                            $(this).find("td").eq(4).find("input").prop("disabled",false);
                            $(this).find("td").eq(5).find("input").prop("disabled",true);
                          }

                          if(radiototalfila || radiosaldofila)
                          {                             
                          }
                          
                        }else{
                          if(!radiototalfila && !radiosaldofila)
                          {
                            $(this).find("td").eq(4).find("input").prop("disabled",true);
                            $(this).find("td").eq(5).find("input").prop("disabled",false);
                          }

                          if(radiototalfila || radiosaldofila)
                          {                            
                          }
                          
                        }
                      }

                    }
                    
                  });
            //fin revertir

          }else if($(this).prop("checked") == false){
            console.log("no estuvo marcado total");///aca falla cuando el monto es menor que el saldo
            
            //validar si sumar depende el saldo y monto
                let montopagos=parseFloat($("#diferencia").val().replace(",", ""));
                if(montopagos==null || isNaN(montopagos)){
                  console.log("no hay pagos ingresados");
                  return;
                }

                console.log("monto es "+montopagos)

                let filedata=tabla_pedidos.row($(this).closest('tr')).data();
                console.log(filedata)
                let pedidosaldo=parseFloat(filedata.saldo);
                if(pedidosaldo==null || isNaN(pedidosaldo)){
                  console.log("no hay saldo ingresado");
                  return;
                }

                console.log("montopagos 1: "+montopagos+"  saldo es "+pedidosaldo)

                if(montopagos>=pedidosaldo)
                {
                  //acaqueda el codigo
                  $(this).prop("checked",true).val("1")
                  montopagos=parseFloat(montopagos-pedidosaldo).toFixed(2);
                  console.log("diferencia 1: "+montopagos);
                  $("#diferencia").val(montopagos);
                  
                  console.log("aqui debo cambiar el valor de input y span de columna diferencia - radiototal");
                  $(this).closest('tr').find("td").eq(3).find(":input").val("0.00");
                  $(this).closest('tr').find("td").eq(3).find(".numberdiferencia").text("0.00");
                  let totalafterdifer1=$(this).closest('tr').find("td").eq(2).find(".numbersaldo").val();
                  let totalafterdifer2=$(this).closest('tr').find("td").eq(3).find(".numberdiferencia").val();
                  let totalafterdifer=parseFloat(totalafterdifer1-totalafterdifer2);
                  $(this).closest('tr').find("td").eq(3).find(".numbertotal").val(totalafterdifer);                   
                  
                  $(this).closest('tr').find(".radioadelanto").prop("disabled",true);
                  console.log("nuevo montogeneral "+$("#diferencia").val());
                  var idfila=$(this).closest('tr').find("td").eq(0).find(":input").val(); 
                  $('#tabla_pedidos > tbody  > tr').each(function(index,tr) {
                    var index1=$(this).find("td").eq(0).find(":input").val();                      
                    console.log("idfila "+idfila+" index "+index1)
                    var saldofila=parseFloat($(this).find("td").eq(2).find(":input").val());
                    var radiototalfila=$(this).find("td").eq(4).find("input").prop("checked");
                    var radiosaldofila=$(this).find("td").eq(5).find("input").prop("checked");
                    if(montopagos==0)
                    {
                      if(idfila!=index1)
                      {
                        if(!radiototalfila && !radiosaldofila)
                        {
                          $(this).find("td").eq(4).find("input").prop("disabled",true);
                          $(this).find("td").eq(5).find("input").prop("disabled",true);
                        }
                      }
                    }else{
                      //analizo si queda en total o adelanto dependiente la diferencia
                      if(idfila!=index1)
                      {
                        
                        if(montopagos>=saldofila)
                        {
                          if(!radiototalfila && !radiosaldofila)
                          {
                            $(this).find("td").eq(4).find("input").prop("disabled",false);
                            $(this).find("td").eq(5).find("input").prop("disabled",true);
                          }

                          if(radiototalfila || radiosaldofila)
                          {                             
                          }
                          
                        }else{
                          if(!radiototalfila && !radiosaldofila)
                          {
                            $(this).find("td").eq(4).find("input").prop("disabled",true);
                            $(this).find("td").eq(5).find("input").prop("disabled",false);
                          }

                          if(radiototalfila || radiosaldofila)
                          {                            
                          }
                          
                        }

                      }

                    }
                    
                    
                  });
                  
                }else{
                  console.log("si monto es menor a pedidos")
                  $(this).prop("checked",true).val("1");
                }
            //fin validar

          }
          return;
           
        });

        $(document).on("mousedown",".radioadelanto",function(event){
          event.preventDefault();
          console.log("radioadelanto mousedown")
          //var esteadelanto=$(this);
          if($(this).prop("checked") == true){
            console.log("marcado")
            $(this).prop("checked",false).val("0")//////revertir              
                let montopagos=parseFloat($("#diferencia").val().replace(",", ""));//0
               
                let filedata=tabla_pedidos.row($(this).closest('tr')).data();
                let pedidosaldo=parseFloat(filedata.saldo);
                if(pedidosaldo==null || isNaN(pedidosaldo)){
                  console.log("no hay saldo ingresado");
                  return;
                }

                console.log("desmarco saldo (2) "+pedidosaldo+" monto "+montopagos);

                let diferenciaantes=parseFloat($("#diferenciaantes").val().replace(",", ""));
                let diferencia1=parseFloat($("#diferencia").val().replace(",", ""));
                $("#diferencia").val(diferenciaantes+diferencia1);
                
                $(this).closest('tr').find("td").eq(3).find(":input").val(pedidosaldo.toFixed(2));
                $(this).closest('tr').find("td").eq(3).find(".numberdiferencia").text(pedidosaldo.toFixed(2));
                let totalafterdifer1=$(this).closest('tr').find("td").eq(2).find(".numbersaldo").val();
                let totalafterdifer2=$(this).closest('tr').find("td").eq(3).find(".numberdiferencia").val();
                let totalafterdifer=parseFloat(totalafterdifer1-totalafterdifer2);
                $(this).closest('tr').find("td").eq(3).find(".numbertotal").val(totalafterdifer);
                
                var idfila=$(this).closest('tr').find("td").eq(0).find(":input").val();
                //revertir pago reviso todo otra vez
                $('#tabla_pedidos > tbody  > tr').each(function(index,tr) {
                    var index1=$(this).find("td").eq(0).find(":input").val();                      
                    console.log("idfila "+idfila+" index "+index1)
                    var saldofila=parseFloat($(this).find("td").eq(2).find(":input").val());
                    var radiototalfila=$(this).find("td").eq(4).find("input").prop("checked");
                    var radiosaldofila=$(this).find("td").eq(5).find("input").prop("checked");
                    if(montopagos==0)
                    {
                      if(idfila!=index1)
                      {
                        if(!radiototalfila && !radiosaldofila)
                        {
                          $(this).find("td").eq(4).find("input").prop("disabled",true);
                          $(this).find("td").eq(5).find("input").prop("disabled",true);
                        }
                      }
                    }else{
                      //analizo si queda en total o adelanto dependiente la diferencia
                      if(idfila!=index1)
                      {
                        
                        if(montopagos>=saldofila)
                        {
                          if(!radiototalfila && !radiosaldofila)
                          {
                            $(this).find("td").eq(4).find("input").prop("disabled",false);
                            $(this).find("td").eq(5).find("input").prop("disabled",true);
                          }

                          if(radiototalfila || radiosaldofila)
                          {                             
                          }
                          
                        }else{
                          if(!radiototalfila && !radiosaldofila)
                          {
                            $(this).find("td").eq(4).find("input").prop("disabled",true);
                            $(this).find("td").eq(5).find("input").prop("disabled",false);
                          }

                          if(radiototalfila || radiosaldofila)
                          {                            
                          }
                          
                        }

                      }

                    }
                    
                    
                  });
            //fin revertir

          }else if($(this).prop("checked") == false){
            console.log("no marcado adelanto");///aca falla cuando el monto es menor que el saldo
                let montopagos=parseFloat($("#diferencia").val().replace(",", ""));
                if(montopagos==null || isNaN(montopagos)){
                  console.log("no hay pagos ingresados");
                  return;
                }
                console.log("montopagos 2: "+montopagos);
                let filedata=tabla_pedidos.row($(this).closest('tr')).data();
                let pedidosaldo=parseFloat(filedata.saldo);
                if(pedidosaldo==null || isNaN(pedidosaldo)){
                  console.log("no hay saldo ingresado");
                  return;
                }
                //sol si saldo es mayor al monto
                console.log(montopagos +" < "+pedidosaldo )
                if(montopagos<pedidosaldo)
                {
                  
                  console.log("marco el check adelanto")
                 
                  let montopagosante=montopagos;
                  montopagos=(0).toFixed(2);
                  console.log("diferencia "+montopagos);
                  $("#diferencia").val(montopagos);
                  $("#diferenciaantes").val(montopagosante);

                  console.log("aqui cuando es adelanto y la diferencia debe ser 0 y guardarlo");
                  console.log("pedidosaldo "+pedidosaldo+" montopagos saldo "+montopagosante);
                
                  let montoqueda=parseFloat(pedidosaldo-montopagosante);
                  console.log("montoqueda "+montoqueda)
                  $(this).closest('tr').find("td").eq(3).find(":input").val(montoqueda.toFixed(2));
                  $(this).closest('tr').find("td").eq(3).find(".numberdiferencia").text(montoqueda.toFixed(2));
                  let totalafterdifer1=$(this).closest('tr').find("td").eq(2).find(".numbersaldo").val();
                  let totalafterdifer2=$(this).closest('tr').find("td").eq(3).find(".numberdiferencia").val();
                  let totalafterdifer=parseFloat(totalafterdifer1-totalafterdifer2);
                  $(this).closest('tr').find("td").eq(3).find(".numbertotal").val(totalafterdifer);  
                  
                  var idfila=$(this).closest('tr').find("td").eq(0).find(":input").val();
                  console.log($(this).closest('tr').find("td").eq(5).find(".radioadelanto").html());
                  $(this).closest('tr').find("td").eq(5).find(".radioadelanto").prop("checked",true).val("1");
                  console.log("debe marcar adlanto aqui  eror");
               
                  $(this).prop("checked",true).val("1")
                  $('#tabla_pedidos > tbody  > tr').each(function(index,tr) {
                    var index1=$(this).find("td").eq(0).find(":input").val(); 
                    
                    console.log("idfila "+idfila+" index "+index1)
                    console.log(montopagos+" montopagos")
                    var saldofila=parseFloat($(this).find("td").eq(2).find(":input").val());
                    var radiototalfila=$(this).find("td").eq(4).find("input").prop("checked");
                    var radiosaldofila=$(this).find("td").eq(5).find("input").prop("checked");
                    if(montopagos==0)
                    {                        
                      if(idfila!=index1)
                      {
                        if(!radiototalfila && !radiosaldofila)
                        {
                          console.log("bloque total check 1")
                          $(this).find("td").eq(4).find("input").prop("disabled",true);
                          $(this).find("td").eq(5).find("input").prop("disabled",true);
                        }
                        
                      }
                    }else{                        
                     
                      {                          
                        if(montopagos>=saldofila)
                        {
                          if(!radiototalfila && !radiosaldofila)
                          {
                            $(this).find("td").eq(4).find("input").prop("disabled",false);
                            $(this).find("td").eq(5).find("input").prop("disabled",true);
                          }
                          
                        }else{
                          if(!radiototalfila && !radiosaldofila)
                          {
                            console.log("bloque total check 2")
                            $(this).find("td").eq(4).find("input").prop("disabled",true);
                            $(this).find("td").eq(5).find("input").prop("disabled",false);
                          }
                          
                        }

                      }

                    }
                    
                    
                  });
                  
                }
                
            //fin validar

          }
          return;
          
        });

        $(document).on("change","#pcliente_id",function(){
          console.log("d")

          $('#tabla_pagos').DataTable().clear().destroy();
          tabla_pagos=$('#tabla_pagos').DataTable({
            "bPaginate": false,
              "bFilter": false,
              "bInfo": false,
              columns: 
              [
                {
                  data: 'accion', 
                  name: 'accion',
                  sWidth:'10%', 
                  render: function ( data, type, row, meta ) {
                    return '<button type="button" class="btn btn-danger btn-sm remove" item="'+row.item+'"><i class="fas fa-trash-alt"></i>'+row.item+'</button>';
                  }
                },
                {
                  data: 'item', 
                  name: 'item',
                  sWidth:'10%', 
                  "visible": false,
                },
                {
                  data: 'movimiento', 
                  name: 'movimiento',
                  sWidth:'10%', 
                  render: function ( data, type, row, meta ) {
                    return '<input type="hidden" name="tipomovimiento['+row.item+']" value="' + data + '"><span class="tipomovimiento">' + data + '</span></td>';
                  }
                },
                {
                  data: 'titular', 
                  name: 'titular',
                  sWidth:'10%',
                  render: function ( data, type, row, meta ) {
                    return '<input type="hidden" name="titular['+row.item+']" value="' + data + '"><span class="titular">' + data + '</span></td>';
                  }
                },
                {
                  data: 'banco', 
                  name: 'banco',
                  sWidth:'5%', 
                  render: function ( data, type, row, meta ) {
                    return '<input type="hidden" name="banco['+row.item+']" value="' + data + '"><span class="banco">' + data + '</span></td>';
                  }
                },
                {
                  data: 'bancop', 
                  name: 'bancop',
                  sWidth:'10%', 
                  render: function ( data, type, row, meta ) {
                    return '<input type="hidden" name="bancop['+row.item+']" value="' + data + '"><span class="bancop">' + data + '</span></td>';
                  },
                  "visible": false,
                },
                {
                  data: 'obanco', 
                  name: 'obanco',
                  sWidth:'10%', 
                  render: function ( data, type, row, meta ) {
                    return '<input type="hidden" name="obanco['+row.item+']" value="' + data + '"><span class="obanco">' + data + '</span></td>';
                  },
                  "visible": false,
                },
                {
                  data: 'fecha', 
                  name: 'fecha',
                  sWidth:'10%', 
                  render: function ( data, type, row, meta ) {
                    return '<input type="hidden" name="fecha['+row.item+']" value="' + data + '"><span class="fecha">' + data + '</span></td>';
                  }
                },
                
                {
                  data: 'imagen', 
                  name: 'imagen',
                  sWidth:'10%', 
                  render: function ( data, type, row, meta ) {
                   
                    var str="storage/pagos/"+data;
                    var urlimage = '{{ asset(":id") }}';

                    urlimage = urlimage.replace(':id', str);
                    data = '<input type="hidden" name="imagen['+row.item+']" value="' + data + '"></td><img src="'+urlimage+'" alt="'+urlimage+'" height="200px" width="200px" class="img-thumbnail">';
                          
                    return data

                  }
                },
                {
                  data: 'monto', 
                  name: 'monto',
                  sWidth:'10%', 
                  render: function ( data, type, row, meta ) {
                    return '<input type="hidden" name="monto['+row.item+']" value="' + data + '"><span class="monto">' + data + '</span></td>';
                  }
                },
                {
                  data: 'operacion', 
                  name: 'operacion',
                  sWidth:'10%', 
                  render: function ( data, type, row, meta ) {
                    return '<input type="hidden" name="operacion['+row.item+']" value="' + data + '"><span class="operacion">' + data + '</span></td>';
                  },
                  "visible": false
                },
                {
                  data: 'nota', 
                  name: 'nota',
                  sWidth:'10%', 
                  "visible": false,
                  render: function ( data, type, row, meta ) {
                    return '<input type="hidden" name="nota['+row.item+']" value="' + data + '"><span class="nota">' + data + '</span></td>';
                  }
                },            
              ],
              "footerCallback": function ( row, data, start, end, display ) {
                var api = this.api();
                nb_cols = 4;api.columns().nodes().length;
                var j = 2;

                //para footer  monto
                var pageTotal = api
                      .column( 9, { page: 'current'} )
                      .data()
                      .reduce( function (a, b) {
                          return Number(a) + Number(b);
                      }, 0 );
                // Update footer

                
                $( api.column( 9 ).footer() ).html('<input type="hidden" name="total_pago" id="total_pago" value="'+pageTotal+'"/>'+'S/.'+separateComma(pageTotal) );

                $( api.column( 9 ).footer() ).html('<input type="hidden" name="total_pago_pagar" id="total_pago_pagar" value="'+pageTotal+'" />'+'S/.'+separateComma(pageTotal) ) ;

              },
              "initComplete": function(settings, json) {

                total_pago=sumatotalpagos();
                if(isNaN(total_pago) )
                {
                  total_pago=0.00;

                }else{
                  total_pago=total_pago;
                }

                console.log("reseteo diferencia "+total_pago);
                $("#diferencia").val(total_pago);
                $("#saldo").val(0.00);
                let uncliente=$("#pcliente_id").val();
                if(uncliente!='')
                {
                 
                }
                diferenciaFaltante();
              },
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
       
          tabla_pedidos.destroy();
        
          datosCliente = $(this).val().split('-');
          cliente_id = datosCliente[0];
          saldo = datosCliente[1];
         
          $("#diferencia").prop("disabled",false);
          let diferenciaval=$("#diferencia").val();
         
          $("#cliente_id").val(cliente_id);
          $("#saldo").val(saldo);
                    
          tabla_pedidos=$('#tabla_pedidos').DataTable({
            "bPaginate": false,
            "bFilter": false,
            "bInfo": false,
            'ajax': {
              url:"{{ route('cargar.pedidosclientetabla') }}",					
              'data': { "cliente_id": $(this).val(),"diferencia":$("#diferencia").val()}, 
              "type": "get",
            },
            "fnCreatedRow": function( nRow, aData, iDataIndex ) {
                $(nRow).attr('id', aData["id"]);
            },
            columns: [
              {
                data: 'id', 
                name: 'id',
                render:function(data,type,row,meta){
                  if(row.id<10){
                    return '<input type="hidden" name="pedido_id['+data+']" value="' + data + '">PED000' + data + '</td>';
                  }else if(row.id<100){
                    return '<input type="hidden" name="pedido_id['+data+']" value="' + data + '">PED00' + data + '</td>';
                  }else if(row.id<1000){
                    return '<input type="hidden" name="pedido_id['+data+']" value="' + data + '">PED0' + data + '</td>';
                  }else{
                    return '<input type="hidden" name="pedido_id['+data+']" value="' + data + '">PED' + data + '</td>';
                  } 
                }
              },
              {data: 'codigo', name: 'codigo',},
              
              {
                data: 'saldo', 
                name: 'saldo',
                render:function(data,type,row,meta){
                    return '<input type="hidden" name="numbersaldo['+row.id+']" value="' + data + '"><span class="numbersaldo">' + data + '</span></td>';
                },
                "visible": true
              },
              {
                data: 'diferencia', 
                name: 'diferencia',
                render:function(data,type,row,meta){
                    return '<input type="hidden" name="numberdiferencia['+row.id+']" value="' + data + '"><span class="numberdiferencia">' + data + '</span></td>'+
                      '<input type="hidden" name="numbertotal['+row.id+']" value="' + data + '"><span class="numbertotal"></span></td>';
                },
                "visible": true
              },
              {
                  "data": null,
                  "render": function ( data, type, row, meta ) {                      
                      return '<input type="checkbox" disabled class="form-control radiototal" name="checktotal['+row.id+']" value="0">';
                  }
              },
              {
                  "data": null,
                  "render": function ( data, type, row, meta ) {                    
                    return '<input type="checkbox" disabled class="form-control radioadelanto" name="checkadelanto['+row.id+']" value="0">';
                  }
              }
            ],
            "footerCallback": function ( row, data, start, end, display ) {
              var api = this.api();
              nb_cols = 4;api.columns().nodes().length;
              var j = 2;

              var pageTotal = api
                    .column( 2, { page: 'current'} )
                    .data()
                    .reduce( function (a, b) {
                        return Number(a) + Number(b);
                    }, 0 );
             

              $( api.column( 2 ).footer() ).html('<input type="hidden" name="total_pedido" id="total_pedido" value="'+pageTotal.toFixed(2)+'"/>'+
                    'S/. '+separateComma(pageTotal.toFixed(2)).toLocaleString("en-US")  );

              var pageSaldo = api
                    .column( 3, { page: 'current'} )
                    .data()
                    .reduce( function (a, b) {
                        return Number(a) + Number(b);
                    }, 0 );
              // Update footer
              $( api.column( 3 ).footer() ).html('<input type="hidden" name="total_pedido_pagar" id="total_pedido_pagar" value="'+pageSaldo.toFixed(2)+'" />'+
                  'S/.'+separateComma(pageSaldo.toFixed(2)).toLocaleString("en-US")  );

            },
            "initComplete": function(settings, json) {
              total_pedido=sumatotalpedidos();
              console.log("total_pedido "+total_pedido);
            },
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

       

        $(document).on("change",'#diferencia',function(e){
          console.log("logica de diferencia");
          console.log($(this).val());
          console.log("actualizar tabla de pedidos a pagar")
        });

        $(document).on("click",'#add_pedido',function(){
          agregarPedido();

        });

        function Remove_options(Pedido_delete)
        {
          $("#ppedido_id option[value='" + Pedido_delete +"']").remove();
        }

        function diferenciaFaltante() {
         
          console.log("total_pago "+total_pago+"  total_pedido "+total_pedido)
          diferencia = total_pago ;//- total_pedido;
          console.log('diferencia en fx diferenciaFaltante');
          console.log(diferencia);
         
          $('#tabla_pedidos > tbody  > tr').each(function(index,tr) {
            console.log(index+" posicion");
           
            console.log(  $(this).find("td").eq(2).html()  )
            var saldofila=parseFloat($(this).find("td").eq(2).find(":input").val());
            console.log(saldofila)
            
            console.log("resta1 "+diferencia);
            console.log("resta2 "+saldofila);
            let restogeneral=(parseFloat(diferencia)-parseFloat(saldofila)).toFixed(2);
            console.log("diferencia por fila "+restogeneral);
            if(saldofila<=total_pago)
            {
              $(this).find("td").eq(4).find("input").prop("disabled",false);
            }else{
              $(this).find("td").eq(5).find("input").prop("disabled",false);
            }

          });

          $("#diferencia").val(diferencia.toLocaleString("en-US"));
        }

        function eliminarPe(index) {
          total_pedido = total_pedido - subtotal_pedido[index];
          $("#total_pedido").html("S/. " + total_pedido.toLocaleString("en-US"));
          $("#total_pedido_pagar").val(total_pedido);
          $("#filasPe" + index).remove();
          evaluarPe();
        }


        $(document).on("keyup",'input.number',function(event){
          if(event.which >= 37 && event.which <= 40){
            event.preventDefault();
          }
          $(this).val(function(index, value) {
            return value
              .replace(/\D/g, "")
              .replace(/([0-9])([0-9]{2})$/, '$1.$2')  
              .replace(/\B(?=(\d{3})+(?!\d)\.?)/g, ",")
            ;
          });

        });

        function limpiarPe() {
          $("#ppedido_id").val("");
          $("#total_pedido").val("");
        }


      $(document).ready(function () {

        $(document).on("click","#add_pago_perdonar",function(){
          if ($('#pmontoperdonar').val() == '')
          {
            Swal.fire(
              'Error',
              'Ingrese monto a perdonar',
              'warning'
                )
          }else if ($('#pfechaperdonar').val() == '')
          {
            Swal.fire(
              'Error',
              'Seleccione la fecha a perdonar',
              'warning'
            )
          }else{

            $("#tabla_pedidos tbody tr .radiototal").prop("checked",false).trigger("change");
            $("#tabla_pedidos tbody tr .radioadelanto").prop("checked",false).trigger("change");


            let files1=$('input[name="pimagen1');//total de imagen 1
            let files2=$('input[name="pimagen2');//total de imagen 2
            let files3=$('input[name="pimagen3');//total de imagen 3
            var totalfilescarga1 = $('#pimagen1').get(0).files.length;
            var totalfilescarga2 = $('#pimagen2').get(0).files.length;
            var totalfilescarga3 = $('#pimagen3').get(0).files.length;
           
            
            if(totalfilescarga1>0 && totalfilescarga2>0   && totalfilescarga3>0 )
            {

              agregarPagoPerdonar();


              

            }else{
              Swal.fire(
                  'Error',
                  'Ingrese las imagenes por favor',
                  'warning'
                )
                return false;
            }
            


          }


        });

        
        $(document).on("click","#add_pago",function(){
          console.log("click addpago");
          if ($('#pbanco').val() == '')
          {
            Swal.fire(
              'Error',
              'Seleccione banco ',
              'warning'
            );
          }else if ($('#tipotransferencia').val() == '')
          {
            Swal.fire(
              'Error',
              'Seleccione tipo de transferencia',
              'warning'
            )
          }else if ($('#titulares').val() == '')
          {
            Swal.fire(
              'Error',
              'Seleccione titular',
              'warning'
            )
          }else if ($('#pmonto').val() == '')
          {
            Swal.fire(
              'Error',
              'Ingrese monto',
              'warning'
                )
          }else if ($('#pfecha').val() == '')
          {
            Swal.fire(
              'Error',
              'Seleccione la fecha',
              'warning'
            )
          }else {
            console.log("empieza logica 2");
            if ($('#tipotransferencia').val() == 'INTERBANCARIO')
            {
              console.log("INTERBANCARIO")
              if ($('#pbancoprocedencia').val() == '')
              {
                console.log("pbancoprocedencia vacio")
                Swal.fire(
                  'Error',
                  'Seleccione Banco de procedencia',
                  'warning'
                )
              }else if ($('#pbancoprocedencia').val() == 'OTROS')
              {
                console.log("pbancoprocedencia OTROS")
                if ($('#otro_bancoprocedencia').val() == '')
                {
                  Swal.fire(
                    'Error',
                    'Seleccione Banco de procedencia',
                    'warning'
                  )
                }else{
                  /**/
                  console.log("aca");
                  $("#tabla_pedidos tbody tr .radiototal").prop("checked",false).trigger("change");
                  $("#tabla_pedidos tbody tr .radioadelanto").prop("checked",false).trigger("change");
                  let files=$('#pimagen');
                  var totalfilescarga = $('#pimagen').get(0).files.length;
                  if(files.length!=totalfilescarga)
                  {
                    Swal.fire(
                      'Error',
                      'Debe ingresar la imagen adjunta',
                      'warning'
                    )
                    return false;
                  }else{
                    deuda = !isNaN($('#pcantidad').val()) ? parseInt($('#pcantidad').val(), 10) : 0;
                    pagado = !isNaN($('#pstock').val()) ? parseInt($('#pstock').val(), 10) : 0;
                    agregarPago();                  
                  }
                  /**/
                }
              }else{
                /**/
                console.log("aca");
                $("#tabla_pedidos tbody tr .radiototal").prop("checked",false).trigger("change");
                $("#tabla_pedidos tbody tr .radioadelanto").prop("checked",false).trigger("change");
                let files=$('#pimagen');
                var totalfilescarga = $('#pimagen').get(0).files.length;
                if(files.length!=totalfilescarga)
                {
                  Swal.fire(
                    'Error',
                    'Debe ingresar la imagen adjunta',
                    'warning'
                  )
                  return false;
                }else{
                  deuda = !isNaN($('#pcantidad').val()) ? parseInt($('#pcantidad').val(), 10) : 0;
                  pagado = !isNaN($('#pstock').val()) ? parseInt($('#pstock').val(), 10) : 0;
                  agregarPago();                  
                }
                /**/
              }
            }else
            {
              console.log("aca");
              $("#tabla_pedidos tbody tr .radiototal").prop("checked",false).trigger("change");
              $("#tabla_pedidos tbody tr .radioadelanto").prop("checked",false).trigger("change");
              let files=$('#pimagen');
              var totalfilescarga = $('#pimagen').get(0).files.length;
              if(files.length!=totalfilescarga)
              {
                Swal.fire(
                  'Error',
                  'Debe ingresar la imagen adjunta',
                  'warning'
                )
                return false;
              }else{
                deuda = !isNaN($('#pcantidad').val()) ? parseInt($('#pcantidad').val(), 10) : 0;
                pagado = !isNaN($('#pstock').val()) ? parseInt($('#pstock').val(), 10) : 0;
                agregarPago();
                
              }
            }
          }
        });

        
      });
        

        function separateComma(val) {
          // remove sign if negative
          var sign = 1;
          if (val < 0) {
            sign = -1;
            val = -val;
          }
          // trim the number decimal point if it exists
          let num = val.toString().includes('.') ? val.toString().split('.')[0] : val.toString();
          let len = num.toString().length;
          let result = '';
          let count = 1;

          for (let i = len - 1; i >= 0; i--) {
            result = num.toString()[i] + result;
            if (count % 3 === 0 && count !== 0 && i !== 0) {
              result = ',' + result;
            }
            count++;
          }

          // add number after decimal point
          if (val.toString().includes('.')) {
            result = result + '.' + val.toString().split('.')[1];
          }
          // return result with - sign if negative
          return sign < 0 ? '-' + result : result;
        }

        $(document).ready(function () {

          window.agregarPagoPerdonar = function(){
            var strExPerdonar = $("#pmontoperdonar").val();
            strExPerdonar = strExPerdonar.replace(",","");
            var numFinalPerdonar = parseFloat(strExPerdonar);
            monto = numFinalPerdonar;
            tipomovimiento='';
            titular=''
            banco=''
            bancop=''
            otherbanco=''
            operacion=''
            nota=''
            fecha = $("#pfechaperdonar").val();

            var fd4 = new FormData();

            for (let ii = 1; ii < 4; ii++) {
              fd4.append('adjunto'+ii, $('input[type=file][name="pimagen'+ii+'"]')[0].files[0]);
            }
            
            $.ajax({
              data: fd4,
              processData: false,
              contentType: false,
              type: 'POST',
              url:"{{ route('pagos.addImgTempPagoPerdonar') }}",
              success:function(data)
              {
                console.log(data);
              

                document.getElementById("picture1").setAttribute('src', "{{asset('imagenes/logo_facturas.png')}}");
                document.getElementById("picture2").setAttribute('src', "{{asset('imagenes/logo_facturas.png')}}");
                document.getElementById("picture3").setAttribute('src', "{{asset('imagenes/logo_facturas.png')}}");
                tabla_pagos.row.add( {
                    "accion":      (contPa + 1),
                    "item":       (contPa + 1),
                    "movimiento":   tipomovimiento,
                    "titular":     titular,
                    "banco": banco,
                    "bancop": bancop,
                    "obanco": otherbanco,
                    "fecha":     fecha,
                    "imagen":       data.html,
                    "monto":      monto,                    
                    "operacion": operacion,
                    "nota": nota,
                } ).draw();

                $("#modal-add-perdonar-deuda").modal("hide");

                //
                $("#btn-accion-perdonar-currier").hide();
                $("#addpago").show();

                //
                contPa++;

                console.log("sumatotalpagos <4")
                total_pago = sumatotalpagos();

                limpiarPaPerdonar();
                $("#total_pago").html("S/. " + separateComma(total_pago).toLocaleString("en-US"));
                $("#total_pago_pagar").val(total_pago.toLocaleString("en-US"));
                evaluarPa();
                console.log("total_pago 1 "+total_pago);
                diferenciaFaltante();

                $("#accion_perdonar").val("1");

              }
            });


          }

          window.agregarPago = function(){  
            $("#accion_perdonar").val("");
            console.log("en pagos")
            var strEx = $("#pmonto").val();
            strEx = strEx.replace(",","");
            var numFinal = parseFloat(strEx);
            monto = numFinal;
            tipomovimiento = $('#tipotransferencia option:selected').val();
            titular = $('#titulares option:selected').val();
            banco = $('#pbanco option:selected').val();
            bancop =$("#pbancoprocedencia option:selected").val();
            otherbanco  =$("#otro_bancoprocedencia").val();
            fecha = $("#pfecha").val();
            operacion = $("#operacion").val();
            nota = $("#nota").val();

            if (monto != ""  && banco != "" && fecha != "") {
              subtotal_pago[contPa] = monto*1;
              total_pago = parseFloat(total_pago*1 + subtotal_pago[contPa]*1).toFixed(2);
              

              var fd2 = new FormData();

              let files=$('input[name="pimagen')
              console.log(files.length);//1
             {
                var totalfilescarga = $('input[name="pimagen"]').get(0).files.length;
                console.log("totalfilescarga "+totalfilescarga);
                console.log(files.get(0).files[0]);
                {
                  console.log("cargo inputs y len "+files.length)
                  for (let i = 0; i < files.length; i++) {
                    fd2.append('adjunto', $('input[type=file][name="pimagen"]')[0].files[0]);
                  }
               
                  $.ajax({
                    data: fd2,
                    processData: false,
                    contentType: false,
                    type: 'POST',
                    url:"{{ route('pagos.addImgTemp') }}",
                    success:function(data){
                      console.log(data);
                      if(data.html=='0'){
                        }else{
                          document.getElementById("picture").setAttribute('src', "{{asset('imagenes/logo_facturas.png')}}");
                          tabla_pagos.row.add( {
                              "item":       (contPa + 1),
                              "movimiento":   tipomovimiento,
                              "titular":     titular,
                              "banco": banco,
                              "bancop": bancop,
                              "obanco": otherbanco,
                              "fecha":     fecha,                              
                              "imagen":       data.html,
                              "monto":      monto,
                              "accion":      (contPa + 1),
                              "operacion":     operacion,
                              "nota":     nota,
                          } ).draw();

                          const fileInput = $("#pimagen");
                          const fileInputdestino = $("#imagen"+(contPa + 1));//aca que se carga la imagen
                          const myFile = new File(['Hello World!'], 'myFile.txt', {
                              type: 'text/plain',
                              lastModified: new Date(),
                          });

                          const dataTransfer = new DataTransfer();
                          dataTransfer.items.add(myFile);
                          fileInputdestino.files = dataTransfer.files;

                          //ahora cerrar modal
                          $("#modal-add-pagos").modal("hide");


                          contPa++;

                          console.log("sumatotalpagos <4")
                          total_pago = sumatotalpagos();

                          limpiarPa();
                          $("#total_pago").html("S/. " + separateComma(total_pago).toLocaleString("en-US"));
                          $("#total_pago_pagar").val(total_pago.toLocaleString("en-US"));
                          evaluarPa();
                          console.log("total_pago 1 "+total_pago);
                          diferenciaFaltante();



                        }
                    }
                  });


                }

              }

              

            }else {
              Swal.fire(
                'Error!',
                'Información faltante del pago',
                'warning')
            }

          }

        });

        
        diferencia = 0;
        console.log("diferencia inicial 0")
        total_pedido = 0;
        subtotal_pedido = [];
        var contPe = 1;

        total_pago = 0;
        subtotal_pago = [];
        var contPa = 0;



        function validarFormulario() {
         
        }
        //////

        ////fin  document ready
      });


    $(document).ready(function() {

      $(document).on("change","#pimagen",function(event){
        console.log("cambe image")
        var file = event.target.files[0];
        var reader = new FileReader();
        reader.onload = (event) => {
            document.getElementById("picture").setAttribute('src', event.target.result);
        };
        reader.readAsDataURL(file);

      });


      $(document).on("change","#pimagen1",function(event){
        var file = event.target.files[0];
        var reader = new FileReader();
        reader.onload = (event) => {
            document.getElementById("picture1").setAttribute('src', event.target.result);
        };
        reader.readAsDataURL(file);
      });

      $(document).on("change","#pimagen2",function(event){
        var file = event.target.files[0];
        var reader = new FileReader();
        reader.onload = (event) => {
            document.getElementById("picture2").setAttribute('src', event.target.result);
        };
        reader.readAsDataURL(file);
      });

      $(document).on("change","#pimagen3",function(event){
        var file = event.target.files[0];
        var reader = new FileReader();
        reader.onload = (event) => {
            document.getElementById("picture3").setAttribute('src', event.target.result);
        };
        reader.readAsDataURL(file);
      });
        

    });
  

    
  </script>

<script>
  $(document).ready(function() {

    $.ajaxSetup({
          headers: {
              'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          }
      });

    $.ajax({
        type:'POST',
        url:"{{ route('asesorcombopago') }}",
    }).done(function (data) {
      $("#user_id").html('');
      $("#user_id").html(data.html);      

      $("#user_id").selectpicker("refresh").trigger("change");
      
      
    });

    

  });
  </script>

@stop