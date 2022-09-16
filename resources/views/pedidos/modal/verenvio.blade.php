  <!-- Modal -->
  <div class="modal fade" id="modal-verenvio-{{ $pedido->id }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" style="max-width: 800px!important;">
      <div class="modal-content">
        <div class="modal-header bg-primary">
          <h5 class="modal-title" id="exampleModalLabel">Detalle de envío</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        {{ Form::Open(['route' => ['envios.enviar', $pedido],'enctype'=>'multipart/form-data', 'id'=>'formulario','files'=>true]) }}
        <div class="modal-body">
          {{-- <p>Complete los siguientes datos para pasar a estado <strong>ATENDIDO</strong> el pedido: <strong>PED00{{ $pedido->id }}</strong></p> --}}
        </div>
        <div style="margin: 10px">
          <div class="card">
            <div class="border rounded card-body border-secondary">
              <div class="card-body">
                <div class="form-row">
                  <div class="form-group col-lg-12">
                    <div class="row">
                      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <h5>Información:</h5>
                      </div><br><br>
                      <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                        <div class="row">
                          <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                            {!! Form::label('fecha_envio_doc_fis', 'Fecha de envío') !!}
                            {!! Form::date('fecha_envio_doc_fis', $pedido->fecha_envio_doc_fis, ['class' => 'form-control', 'id' => 'fecha_envio_doc_fis', 'disabled']) !!}
                          </div>
                          <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                            {!! Form::label('fecha_recepcion', 'Fecha de entrega') !!}
                            {!! Form::date('fecha_recepcion', $pedido->fecha_recepcion, ['class' => 'form-control', 'id' => 'fecha_recepcion', 'disabled']) !!}
                          </div>                          
                          <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                            {!! Form::label('foto1', 'Foto 1') !!}
                            @if($pedido->foto1 != null)
                              <p><a href="{{ route('envios.descargarimagen', $pedido->foto1) }}">Descargar</a></p>
                            @endif
                          </div>
                          <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                            {!! Form::label('foto2', 'Foto 2') !!}
                            {{-- <p><a href="{{ route('envios.descargarimagen', $pedido->foto2) }}">{{ $pedido->foto2 }}</a></p> --}}
                            @if($pedido->foto2 != null)
                              <p><a href="{{ route('envios.descargarimagen', $pedido->foto2) }}">{{ $pedido->foto2 }}</a></p>
                            @endif
                          </div>
                          <div class="col-lg-6 col-md-6 col-sm-6 col-xs-6">
                            {!! Form::label('condicion', 'Estado') !!}                    
                            {!! Form::text('condicion', $pedido->condicion_envio, ['class' => 'form-control', 'disabled']) !!}                              
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-info" id="atender">Confirmar</button>
        </div>
        {{ Form::Close() }}
      </div>
    </div>
  </div>
