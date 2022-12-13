  <!-- Modal -->
  <div class="modal fade" id="modal-editar-atencion" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 800px!important;">
      <div class="modal-content">
        <div class="modal-header bg-success">
          <h5 class="modal-title" id="exampleModalLabel">Editar Atención</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        {{-- Form::Open(['route' => ['pedidos.atender', $pedido],'enctype'=>'multipart/form-data', 'id'=>'formulario','files'=>true]) --}}

          
        <div class="modal-body">
          <p>Detalles del pedido: <strong class="textcode">PED00</strong></p>
        </div>
        <div style="margin: 10px">
          <div class="card">
            <div class="border rounded card-body border-secondary">
              <div class="card-body">
                <div class="form-row">
                  <div class="form-group col-lg-12">

                      

                      <div class="col-lg-12 col-md-6 col-sm-6 col-xs-6">
                        <form method="POST" id="formulario_adjuntos" name="formulario_adjuntos">
                        <input type="hidden" id="hiddenAtender" name="hiddenAtender">
                          <div class="row">
                            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
                                <label for="envio_doc">Documento enviado</label>
                                <input class="form-control-file" id="adjunto" multiple="true" name="adjunto[]" type="file">
                            </div>
                            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
                                <label for="fecha_envio_doc">Fecha de envío</label>
                                <input class="form-control" id="fecha_envio_doc" name="fecha_envio_doc" type="text" value="">
                            </div>

                            <div class="col-lg-4 col-md-12 col-sm-12 col-xs-12">
                                <label for="cant_compro">Cantidad de comprobantes enviados</label>
                                <input class="form-control" id="cant_compro" step="1" min="0" name="cant_compro" type="number" value="0">
                            </div>

                          </div>
                          <div class="row">
                            <div class="col-md-12 col-sm-12 col-xs-12">
                              
                              {{-- <a href="#" class="btn btn-info" id="confirmar_atender">Confirmar</a>--}}
                              <button type="submit" class="btn btn-primary" id="cargar_adjunto">Subir Informacion</button>
                            </div>
                          </div>
                        </form>
                        <hr>
                      </div>

                      <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12">
                        <h5><b>Archivos adjuntos:</b></h5>
                      </div>
                      <div class="col-lg-6 col-md-12 col-sm-12 col-xs-12" id="listado_adjuntos">

                      </div>

                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="container">
        </div>

        <div class="modal-footer">
          <form method="POST" id="formulario_adjuntos_confirmar" name="formulario_adjuntos_confirmar">
            <button type="submit" class="btn btn-info" id="confirmar_atender">Confirmar</button>
          </form>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
        </div>

      </div>
    </div>
  </div>
