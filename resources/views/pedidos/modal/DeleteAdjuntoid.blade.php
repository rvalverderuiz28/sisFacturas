  <!-- Modal -->
  <div class="modal fade" id="modal-delete-adjunto" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-danger">
          <h5 class="modal-title" id="exampleModalLabel">Eliminar Adjunto</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        {{--{{ Form::Open(['route' => ['pedidos.eliminarAdjunto', $img->id]]) }}--}}
        <Form id="formdeleteadjunto" name ="formdeleteadjunto">
          <input type="text" id="eliminar_pedido_id" name="eliminar_pedido_id">
          <input type="text" id="eliminar_pedido_id_imagen" name="eliminar_pedido_id_imagen">
        <div class="modal-body">
          <p>Confirme si desea <strong>ELIMINAR</strong> archivo adjunto</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-danger">Confirmar</button>
        </div>
        {{ Form::Close() }}
      </div>
    </div>
  </div>
