<!-- Modal -->
<div class="modal fade" id="modal-cancell-order" tabindex="-1" role="dialog" aria-labelledby="BottomRightLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="BottomRightLabel">Cancelar pedido</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form id="form-data-cancel" action="{{url('pedidos/supermarket/cancel')}}" method="POST" class="" onsubmit="return false;" enctype="multipart/form-data" autocomplete="off" data-ajax-type="ajax-form-modal" data-column="0" {{(Route::currentRouteName() == 'pedidos.supermarket.info' ? 'data-callback=getPrintable' : 'data-refresh=table' )}} data-table_id="data-table" data-container_class="printable-area">
                <div class="modal-body">
                    <div class="form-group d-none">
                        <input type="text" class="form-control" name="row_id">
                    </div>
                    <div class="form-group">
                        <label>Describe el motivo de la cancelación</label>
                        <textarea class="form-control not-empty" name="comment" placeholder="Ej. No hay stock suficiente..." data-msg="Motivo de cancelación"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary save" data-dismiss="modal">Enviar</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="modal-refund-order" tabindex="-1" role="dialog" aria-labelledby="BottomRightLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="BottomRightLabel">Reembolsar pedido</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form id="form-data-refund" action="{{url('pedidos/supermarket/refund')}}" method="POST" class="" onsubmit="return false;" enctype="multipart/form-data" autocomplete="off" data-ajax-type="ajax-form-modal" data-column="0" {{(Route::currentRouteName() == 'pedidos.supermarket.info' ? 'data-callback=getPrintable' : 'data-refresh=table' )}} data-table_id="data-table" data-container_class="printable-area">
                <div class="modal-body">
                    <div class="form-group d-none">
                        <input type="text" class="form-control" name="row_id">
                    </div>
                    <div class="form-group">
                        <label>Acción</label>
                        <select class="form-control not-empty" name="refund" data-msg="Acción">
                            <option value="0" disabled selected>Seleccione una opción</option>
                            <option value="1">Reembolsar pedido</option>
                            <option value="2">Denegar reembolso</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Comentarios adicionales</label>
                        <textarea class="form-control not-empty" name="comment" placeholder="Ej. Reembolsado otorgado..." data-msg="Comentarios adicionales"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary save" data-dismiss="modal">Aceptar</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade data-fill" tabindex="-1" role="dialog" aria-labelledby="label-title" id="modal-change-order-status">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="label-title">Cambiar status de pedido</h4>
            </div>
            <form id="form-change-order-status" action="{{url('pedidos/supermarket/change-status')}}" onsubmit="return false;" enctype="multipart/form-data" method="POST" autocomplete="off" data-ajax-type="ajax-form-modal" data-column="0" data-refresh="table" data-redirect="0" data-table_id="example3" data-container_id="table-container">
                <div class="modal-body">
                    <div class="form-group d-none">
                        <label for="id">ID</label>
                        <input type="text" class="form-control" value="" name="id">
                    </div>
                    <div class="form-group">
                        <label for="status_id">Nuevo status</label>
                        <select name="status_id" class="form-control not-empty" data-msg="Status">
                            <option value="0" disabled selected>Seleccione una opción</option>
                            {{-- Add new available status --}}
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary save">Cambiar</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->