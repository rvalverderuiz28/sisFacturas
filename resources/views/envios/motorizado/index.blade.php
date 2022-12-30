@extends('adminlte::page')

@section('title', 'Motorizado')

@section('content_header')
    <h1>
        Motorizado
    </h1>
@stop

@section('content')

    @include('envios.motorizado.modal.entregado')

    <div class="card">
        <div class="card-body">
            <table id="tablaPrincipal" style="width:100%;" class="table table-striped">
                <thead>
                <tr>
                    <th scope="col">Item</th>
                    <th scope="col">Código</th>
                    <th scope="col">Asesor</th>
                    <th scope="col">Cliente</th>
                    <th scope="col">Fecha de Envio</th>
                    <th scope="col">Razón social</th>
                    <th scope="col">Destino</th>
                    <th scope="col">Dirección de envío</th>
                    <th scope="col">Referencia</th>
                    <th scope="col">Estado de envio</th><!--ENTREGADO - RECIBIDO-->
                    <th scope="col">Acciones</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>

@stop

@push('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.css">
@endpush

@section('js')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-confirm/3.3.2/jquery-confirm.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.10.19/js/dataTables.bootstrap4.min.js"></script>

    <script src="https://momentjs.com/downloads/moment.js"></script>
    <script src="https://cdn.datatables.net/plug-ins/1.11.4/dataRender/datetime.js"></script>

    <script>
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $('#tablaPrincipal').DataTable({
                processing: true,
                stateSave: true,
                serverSide: true,
                searching: true,
                order: [[0, "desc"]],
                ajax: "{{ route('envios.motorizados.index',['datatable'=>1]) }}",
                createdRow: function (row, data, dataIndex) {

                },
                rowCallback: function (row, data, index) {
                    if (data.destino2 == 'PROVINCIA') {
                        $('td', row).css('color', 'red')
                    }
                    $('[data-jqconfirm]', row).click(function () {
                        $.dialog({
                            title: 'Entregas de motorizado',
                            type: 'green',
                            columnClass: 'large',
                            content: `<div>
    <form enctype="multipart/form-data" class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <h5>Información:</h5>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                   <div class="form-group">
                     <label for="fecha_envio_doc_fis">Fecha de Envio</label>
                     <input class="form-control" id="fecha_envio_doc_fis" disabled="" name="fecha_envio_doc_fis" type="date" value="${data.fecha}">
                    </div>
                </div>
                <div class="col-6">
                   <div class="form-group">
                        <label for="fecha_recepcion">Fecha de Entrega</label>
                        <input class="form-control" id="fecha_recepcion" name="fecha_recepcion" type="date" value="">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <label for="foto1">Foto recibido 1</label>
                        <input class="form-control-file" id="adjunto1" name="adjunto1" type="file">
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <label for="foto2">Foto recibido 2</label>
                        <input class="form-control-file" id="adjunto2" name="adjunto2" type="file">
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-6">
                    <div class="form-group">
                        <div class="image-wrapper">
                            <img id="picture1" src="https://sisfactura.dev/imagenes/logo_facturas.png"
                                 alt="Imagen del pago" class="w-100" style="display: none">
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="form-group">
                        <div class="image-wrapper">
                            <img id="picture2" src="https://sisfactura.dev/imagenes/logo_facturas.png"
                                 alt="Imagen del pago" class="w-100" style="display: none">
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button type="submit" class="btn btn-info" id="atender">Confirmar</button>
        </div>
    </form>
</div>`,
                            onContentReady: function () {
                                const self = this
                                self.$content.find("#adjunto1").change(function (e) {
                                    const [file] = e.target.files
                                    if (file) {
                                        self.$content.find("#picture1").show();
                                        self.$content.find("#picture1").attr('src', URL.createObjectURL(file))
                                    }
                                })
                                self.$content.find("#adjunto2").change(function (e) {
                                    const [file] = e.target.files
                                    if (file) {
                                        self.$content.find("#picture2").show();
                                        self.$content.find("#picture2").attr('src', URL.createObjectURL(file))
                                    }
                                })
                                self.$content.find("form").on('submit', function (e) {
                                    e.preventDefault()
                                    if (!e.target.fecha_recepcion.value) {
                                        $.confirm({
                                            title: '¡Advertencia!',
                                            content: '<b>Ingresa la fecha de Entrega</b>',
                                            type: 'orange'
                                        })
                                        return false;
                                    }
                                    if (e.target.adjunto1.files.length === 0) {
                                        $.confirm({
                                            title: '¡Advertencia!',
                                            content: '<b>Adjunta la foto 1</b>',
                                            type: 'orange'
                                        })
                                        return false;
                                    }
                                    if (e.target.adjunto2.files.length === 0) {
                                        $.confirm({
                                            title: '¡Advertencia!',
                                            content: '<b>Adjunta la foto 2</b>',
                                            type: 'orange'
                                        })
                                        return false;
                                    }
                                    var fd2=new FormData(e.target);
                                    fd2.set('envio_id',data.id)
                                    self.showLoading(true)
                                    $.ajax({
                                        data: fd2,
                                        processData: false,
                                        contentType: false,
                                        type: 'POST',
                                        url: "{{ route('operaciones.confirmarmotorizado') }}"
                                    }).done(function () {
                                        self.close()
                                        $('#tablaPrincipal').DataTable().ajax.reload();
                                    }).always(function () {
                                        self.hideLoading(true)
                                    });
                                })
                            },
                        });
                    })
                },
                columns: [
                    {
                        data: 'correlativo',
                        name: 'correlativo',
                    },
                    {
                        data: 'codigos',
                        name: 'codigos',
                        render: function (data, type, row, meta) {
                            if (data == null) {
                                return 'SIN PEDIDOS';
                            } else {
                                var returndata = '';
                                var jsonArray = data.split(",");
                                $.each(jsonArray, function (i, item) {
                                    returndata += item + '<br>';
                                });
                                return returndata;
                            }
                        },
                    },
                    {data: 'identificador', name: 'identificador',},
                    {
                        data: 'celular',
                        name: 'celular',
                        render: function (data, type, row, meta) {
                            return row.celular + '<br>' + row.nombre
                        },
                    },
                    {
                        data: 'fecha',
                        name: 'fecha',
                        render: $.fn.dataTable.render.moment('DD/MM/YYYY')
                    },
                    {
                        data: 'producto',
                        name: 'producto',
                        render: function (data, type, row, meta) {
                            if (data == null) {
                                return 'SIN RUCS';
                            } else {
                                var numm = 0;
                                var returndata = '';
                                var jsonArray = data.split(",");
                                $.each(jsonArray, function (i, item) {
                                    numm++;
                                    returndata += numm + ": " + item + '<br>';

                                });
                                return returndata;
                            }
                        }
                    },
                    {data: 'destino', name: 'destino',},
                    {
                        data: 'direccion',
                        name: 'direccion',
                        render: function (data, type, row, meta) {
                            if (data != null) {
                                return data;
                            } else {
                                return '<span class="badge badge-info">REGISTRE DIRECCION</span>';
                            }
                        },
                    },
                    {
                        data: 'referencia',
                        name: 'referencia',
                        sWidth: '10%',
                        render: function (data, type, row, meta) {
                            var datal = "";
                            if (row.destino == 'LIMA') {
                                return data;
                            } else if (row.destino == 'PROVINCIA') {
                                var urladjunto = '{{ route("pedidos.descargargastos", ":id") }}'.replace(':id', data);
                                datal = datal + '<p><a href="' + urladjunto + '">' + data + '</a><p>';
                                return datal;
                            }
                        }
                    },
                    {data: 'condicion_envio', name: 'condicion_envio',},
                    {
                        data: 'action',
                        name: 'action',
                        orderable: false,
                        searchable: false,
                        sWidth: '10%',
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

@stop
