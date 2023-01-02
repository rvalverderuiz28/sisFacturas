@extends('adminlte::page')

@section('title', 'Lista de Clientes')

@section('style')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css">
@endsection

@section('content_header')
    <h1>Lista de clientes
        @can('clientes.create')
            <a href="{{ route('clientes.create') }}" class="btn btn-info"><i class="fas fa-plus-circle"></i> Agregar</a>
        @endcan
        @can('clientes.exportar')
            <div class="float-right btn-group dropleft">
                <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown"
                        aria-haspopup="true" aria-expanded="false">
                    Exportar
                </button>
                <div class="dropdown-menu">
                    <a href="" data-target="#modal-exportar2" data-toggle="modal" class="dropdown-item" target="blank_"><img
                            src="{{ asset('imagenes/icon-excel.png') }}"> Clientes - Pedidos</a>
                    {{--<a href="" data-target="#modal-exportar-v2" data-toggle="modal" class="dropdown-item" target="blank_"><img src="{{ asset('imagenes/icon-excel.png') }}"> Clientes - Situacion</a>--}}


                </div>
            </div>
            @include('clientes.modal.exportar')
            {{--@include('clientes.modal.exportarv2')--}}
        @endcan
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
            <table id="tablaPrincipal" style="width:100%;" class="table table-striped">
                <thead>
                <tr>
                    <th scope="col">COD.</th>
                    <th scope="col">Nombre</th>
                    <th scope="col">Celular</th>
                    <th scope="col">Direccion</th>
                    <th scope="col">Asesor asignado</th>
                    <th scope="col">Situacion</th>
                    {{--<th scope="col">Cantidad</th>--}}
                    {{--<th scope="col">Año actual</th>
                    <th scope="col">Mes actual</th>
                    <th scope="col">anio pedido</th>
                    <th scope="col">mes pedido</th>
                    <th scope="col">Deuda</th>--}}
                    <th scope="col">Cod Ult. Pedido</th>
                    
                    <th scope="col">Acciones</th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
            @include('clientes.modal.historialsituacion')
            @include('clientes.modal.modal_clientes_deudas')
        </div>
    </div>

@stop

@section('css')
    <!--<link rel="stylesheet" href="../css/admin_custom.css">-->
    <style>

        .red {
            background-color: red !important;
        }

        .white {
            background-color: white !important;
        }

        .lighblue {
            background-color: #4ac4e2 !important;
        }

        .bg-4 {
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

        .modal-lg {
            max-width: 80% !important;
        }


    </style>
    <script>
        window.copyElement=function (el) {
            $(el).select();
            window.document.execCommand("copy");
        }
    </script>
@stop

@section('js')

    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>
    <script>

        var tabla_historial_cliente = null;

        $(document).ready(function () {

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            tabla_historial_cliente = $('#tabla_pedidos').DataTable({
                "bPaginate": false,
                "bFilter": false,
                "bInfo": false,
                columns:
                    [
                        {data: null}
                        , {data: null}
                        , {data: null}
                        , {data: null}
                        , {data: null}
                        , {data: null}
                        , {data: null}
                        , {data: null}
                        , {data: null}
                        , {data: null}
                        , {data: null}
                        , {data: null}
                        , {data: null}
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
                }
            });

            $(document).on("click", "#delete", function () {

                console.log("action delete action")
                var formData = $("#formdelete").serialize();
                console.log(formData);
                $.ajax({
                    type: 'POST',
                    url: "{{ route('clientedeleteRequest.post') }}",
                    data: formData,
                }).done(function (data) {
                    $("#modal-delete").modal("hide");
                    resetearcamposdelete();
                    $('#tablaPrincipal').DataTable().ajax.reload();
                });

            });

            $('#modal-delete').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget)
                var idunico = button.data('delete')
                $("#hiddenClienteId").val(idunico);
                if (idunico < 10) {
                    idunico = 'PAG000' + idunico;
                } else if (idunico < 100) {
                    idunico = 'PAG00' + idunico;
                } else if (idunico < 1000) {
                    idunico = 'PAG0' + idunico;
                } else {
                    idunico = 'PAG' + idunico;
                }
                $(".textcode").html(idunico);

            });

            $('#modal-historial-situacion-cliente').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget)
                var idcliente = button.data('cliente')


                $('#tablaPrincipalHistorialSituacion').DataTable().clear().destroy();

                $('#tablaPrincipalHistorialSituacion').DataTable({
                    processing: true,
                    serverSide: true,
                    searching: true,
                    "order": [
                        [0, "desc"]
                    ],
                    ajax: {
                        url: "{{ route('clientestablasituacion') }}",
                        data: function (d) {
                            d.cliente = idcliente;

                        },
                    },
                    "createdRow": function (row, data, dataIndex) {
                    },
                    "autoWidth": false,
                    rowCallback: function (row, data, index) {
                    },
                    columns: [
                        {
                            data: 'id',
                            name: 'id',
                            sWidth: '10%'
                        },
                        {
                            data: 'a_2021_11',
                            name: 'a_2021_11',
                            sWidth: '20%',
                        },
                        {
                            data: 's_2021_11',
                            name: 's_2021_11',
                            sWidth: '20%',
                        },
                        {
                            data: 'a_2021_12',
                            name: 'a_2021_12',
                            sWidth: '20%',
                        },
                        {
                            data: 's_2021_12',
                            name: 's_2021_12',
                            sWidth: '20%',
                        },
                        {
                            data: 'a_2022_01',
                            name: 'a_2022_01',
                            sWidth: '20%',
                        },
                        {
                            data: 's_2022_01',
                            name: 's_2022_01',
                            sWidth: '20%',
                        },
                        {
                            data: 'a_2022_02',
                            name: 'a_2022_02',
                            sWidth: '20%',
                        },
                        {
                            data: 's_2022_02',
                            name: 's_2022_02',
                            sWidth: '20%',
                        },
                        {
                            data: 'a_2022_03',
                            name: 'a_2022_03',
                            sWidth: '20%',
                        },
                        {
                            data: 's_2022_03',
                            name: 's_2022_03',
                            sWidth: '20%',
                        },
                        {
                            data: 'a_2022_04',
                            name: 'a_2022_04',
                            sWidth: '20%',
                        },
                        {
                            data: 's_2022_04',
                            name: 's_2022_04',
                            sWidth: '20%',
                        },
                        {
                            data: 'a_2022_05',
                            name: 'a_2022_05',
                            sWidth: '20%',
                        },
                        {
                            data: 's_2022_05',
                            name: 's_2022_05',
                            sWidth: '20%',
                        },
                        {
                            data: 'a_2022_06',
                            name: 'a_2022_06',
                            sWidth: '20%',
                        },
                        {
                            data: 's_2022_06',
                            name: 's_2022_06',
                            sWidth: '20%',
                        },
                        {
                            data: 'a_2022_07',
                            name: 'a_2022_07',
                            sWidth: '20%',
                        },
                        {
                            data: 's_2022_07',
                            name: 's_2022_07',
                            sWidth: '20%',
                        },
                        {
                            data: 'a_2022_08',
                            name: 'a_2022_08',
                            sWidth: '20%',
                        },
                        {
                            data: 's_2022_08',
                            name: 's_2022_08',
                            sWidth: '20%',
                        },
                        {
                            data: 'a_2022_09',
                            name: 'a_2022_09',
                            sWidth: '20%',
                        },
                        {
                            data: 's_2022_09',
                            name: 's_2022_09',
                            sWidth: '20%',
                        },
                        {
                            data: 'a_2022_10',
                            name: 'a_2022_10',
                            sWidth: '20%',
                        },
                        {
                            data: 's_2022_10',
                            name: 's_2022_10',
                            sWidth: '20%',
                        },
                        {
                            data: 'a_2022_11',
                            name: 'a_2022_11',
                            sWidth: '20%',
                        },
                        {
                            data: 's_2022_11',
                            name: 's_2022_11',
                            sWidth: '20%',
                        },
                        {
                            data: 'a_2022_12',
                            name: 'a_2022_12',
                            sWidth: '20%',
                        },
                        {
                            data: 's_2022_12',
                            name: 's_2022_12',
                            sWidth: '20%',
                        },
                        {
                            data: 'a_2023_01',
                            name: 'a_2023_01',
                            sWidth: '20%',
                        },
                        {
                            data: 's_2023_01',
                            name: 's_2023_01',
                            sWidth: '20%',
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


            $('#tablaPrincipal').DataTable({
                processing: true,
                responsive: true,
                autowidth: true,
                serverSide: true,
                ajax: "{{ route('clientestabla') }}",
                initComplete: function (settings, json) {

                },
                columns: [
                    {
                        data: 'id',
                        name: 'id',
                        render: function (data, type, row, meta) {
                            if (row.id < 10) {
                                return 'CL' + row.identificador + '000' + row.id;
                            } else if (row.id < 100) {
                                return 'CL' + row.identificador + '00' + row.id;
                            } else if (row.id < 1000) {
                                return 'CL' + row.identificador + '00' + row.id;
                            } else {
                                return 'CL' + row.identificador + '' + row.id;
                            }
                        }
                    },
                    {data: 'nombre', name: 'nombre'},
                    {
                        data: 'celular',
                        name: 'celular',
                        render: function (data, type, row, meta) {
                            if (row.icelular != null) {
                                return row.celular + '-' + row.icelular;
                            } else {
                                return row.celular;
                            }
                        }
                    },
                    //{data: 'estado', name: 'estado'},
                    //{data: 'user', name: 'user'},
                    //{data: 'identificador', name: 'identificador'},
                    //{data: 'provincia', name: 'provincia'},
                    {
                        data: 'direccion',
                        name: 'direccion',
                        render: function (data, type, row, meta) {
                            return row.direccion + ' - ' + row.provincia + ' (' + row.distrito + ')';
                        }
                    },
                    //{data: 'direccion', name: 'direccion'},
                    {data: 'identificador', name: 'identificador'},
                    {data: 'situacion', name: 'situacion'},


                    {data: 'ultimo_pedido', name: 'ultimo_pedido'},
                    
                    //{data: 'cantidad', name: 'cantidad'},
                    //{data: 'dateY', name: 'dateY'},
                    //{data: 'dateM', name: 'dateM'},
                    //{data: 'anio', name: 'anio'},
                    //{data: 'mes', name: 'mes'},
                    //{data: 'deuda', name: 'deuda'},
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        sWidth: '20%',
                        render: function (data, type, row, meta) {
                            var urledit = '{{ route("clientes.edit", ":id") }}';
                            urledit = urledit.replace(':id', row.id);

                            var urlshow = '{{ route("clientes.show", ":id") }}';
                            urlshow = urlshow.replace(':id', row.id);

                            @can('clientes.edit')
                                data = data + '<a href="' + urledit + '" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i> Editar</a>';
                            @endcan

                                @if($mirol !='Administradorsdsd')
                                data = data + '<a href="' + urlshow + '" class="btn btn-info btn-sm"><i class="fas fa-eye"></i> Ver</a>';
                            @endif

                                @can('clientes.destroy')
                                data = data + '<a href="" data-target="#modal-delete" data-toggle="modal" data-opcion="' + row.id + '"><button class="btn btn-danger btn-sm"><i class="fas fa-trash-alt"></i> Eliminar</button></a>';

                            @endcan

                                data = data + '<a href="" data-target="#modal-historial-situacion-cliente" data-toggle="modal" data-cliente="' + row.id + '"><button class="btn btn-success btn-sm"><i class="fas fa-trash-alt"></i> Historico</button></a>';
                                if (
                                    (row.pedidos_mes_deuda == 0 && row.pedidos_mes_deuda_antes > 0)||
                                    (row.pedidos_mes_deuda > 0 && row.pedidos_mes_deuda_antes > 0)||
                                    (row.pedidos_mes_deuda > 0 && row.pedidos_mes_deuda_antes == 0)
                                ) {
                                data = data + '<a href="" data-target="#modal_clientes_deudas_model" data-toggle="modal" data-cliente="' + row.id + '"><button class="btn btn-dark btn-sm"><i class="fas fa-money"></i> Deudas</button></a>';
                            }

                            return data;
                        }
                    },
                ],
                "createdRow": function (row, data, dataIndex) {
                    if (data["pedidos_mes_deuda"] > 0 && data["pedidos_mes_deuda_antes"] == 0) {
                        $(row).addClass('lighblue');
                    } else if (data["pedidos_mes_deuda"] > 0 && data["pedidos_mes_deuda_antes"] > 0) {
                        $(row).addClass('red');
                    } else if (data["pedidos_mes_deuda"] == 0 && data["pedidos_mes_deuda_antes"] > 0) {
                        $(row).addClass('red');
                    }
                    /*if(data["deuda"] == "0")
                    {
                        $(row).addClass('white');
                    }else{
                      if ( (data["dateY"] - data["anio"]) == 0 )
                      {
                        if(   (data["dateM"] - data["mes"]) >= 0 &&  (data["dateM"] - data["mes"]) <2 )
                        {
                          $(row).addClass('lighblue');
                        }else{
                          $(row).addClass('red');
                        }
                      }else{
                        $(row).addClass('red');
                      }
                    }*/
                },
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

            $(document).on("keypress", '#tablaPrincipal_filter label input', function () {
                console.log("aaaaa")

                localStorage.setItem("search_tabla", $(this).val());
                console.log("search_tabla es " + localStorage.getItem("search_tabla"));

            });

            $('#tablaPrincipal_filter label input').on('paste', function (e) {
                var pasteData = e.originalEvent.clipboardData.getData('text')
                localStorage.setItem("search_tabla", pasteData);
            });
            $('#tablaPrincipal_filter label input').on('paste', function (e) {
                var pasteData = e.originalEvent.clipboardData.getData('text')
                localStorage.setItem("search_tabla", pasteData);
            });
            $(document).on("keypress", '#tablaPrincipal_filter label input', function () {
                localStorage.setItem("search_tabla", $(this).val());
                console.log("search_tabla es " + localStorage.getItem("search_tabla"));
            });
        });


    </script>

    <!--<script src="{{ asset('js/datatables.js') }}"></script>-->

    @if (session('info') == 'registrado' || session('info') == 'actualizado' || session('info') == 'eliminado')
        <script>
            Swal.fire(
                'Cliente {{ session('info') }} correctamente',
                '',
                'success'
            )
        </script>
    @endif
    <script>
        (function () {
            $(document).ready(function () {
                $("#modal_clientes_deudas_model").on('shown.bs.modal', function (e) {
                    var button = $(e.relatedTarget)
                    $("#modal_clientes_deudas_content").html("")
                    $("#modal_clientes_deudas_content_loading").show()
                    $.get('{{route('clientes.deudas_copy')}}', {'cliente_id': button.data('cliente')})
                        .done(function (data) {
                            console.log(arguments)
                            $("#modal_clientes_deudas_content").html(data.html)
                        })
                        .fail(function () {
                            console.log(arguments)
                        })
                        .always(function () {
                            $("#modal_clientes_deudas_content_loading").hide()
                            console.log(arguments)
                        });
                })

                $("#modal_clientes_deudas_model").on('hide.bs.modal', function (e) {
                    $("#modal_clientes_deudas_content").html("")
                });
            })
        })()
    </script>
@stop
