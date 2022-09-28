<div class="modal fade data-fill" tabindex="-1" role="dialog" aria-labelledby="label-title" id="modal-info">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h5 class="modal-title" id="label-title">Detalles del producto</h5>
            </div>
            <div class="modal-body">
                <div class="row text-left">
                    <div class="col-md-12">
                        <ul class="list-group">
                            <li class="list-group-item active">Datos generales</li>
                            <li class="list-group-item text-center">
                                {{-- <span class="label_show">Foto: </span> --}}
                                <img style="max-width: 15%; border-radius: 50%;" src="" class="producto_photo">
                            </li>
                            <li class="list-group-item fill-container"><span class="label_show">ID artículo: <span class="producto_art_id"> </span></span></li>
                            <li class="list-group-item fill-container"><span class="label_show">Clave: <span class="producto_clave"> </span></span></li>
                            <li class="list-group-item fill-container"><span class="label_show">Descripción: <span class="producto_descripcion"> </span></span></li>
                            <li class="list-group-item fill-container"><span class="label_show">Precio (Con impuestos): <span class="suffix">$</span><span class="producto_precio1"> </span> <span class="suffix"> MXN</span> </span></li>
                        </ul>

                        <ul class="list-group ul-sucursales d-none">
                            <li class="list-group-item active">Sucursales y existencia</li>
                            <li class="list-group-item">
                                <table class="table table-hover table-md sucursales">
                                    <thead>
                                        <th>Sucursal</th>
                                        <th>Existencias</th>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                </table>
                            </li>
                        </ul>

                        <ul class="list-group ul-imagenes d-none">
                            <li class="list-group-item active">Galería</li>
                            <li class="list-group-item">
                                <div class="row image-list">
                                    {{-- Contenido de fotos --}}
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade data-fill" tabindex="-1" role="dialog" aria-labelledby="label-title" id="change-stock">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header text-center">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="label-title">Modificar stock</h4>
            </div>
            <form id="form-change-stock-product" action="{{url('productos/fastfood/change-stock')}}" onsubmit="return false;" enctype="multipart/form-data" method="POST" autocomplete="off" data-ajax-type="ajax-form-modal" data-column="0" data-refresh="table" data-redirect="0" data-table_id="example3" data-container_id="table-container">
                <div class="modal-body">
                    <div class="alert alert-secondary alert-dismissible fade show" role="alert">
                        <strong>Nota!</strong> 
                        - Si deja el campo de stock vacío o pone un valor no numérico, en automático
                        el sistema pondrá como 0 el stock del producto en tal sucursal.
                    </div>
                    <div class="form-group d-none">
                        <label for="id">ID</label>
                        <input type="text" class="form-control" value="" name="id">
                    </div>
                    <div class="form-group">
                        <label for="id_sucursales">Sucursal</label>
                        <select name="id_sucursales" class="form-control not-empty" data-msg="Sucursal">
                            <option value="0" disabled selected>Seleccione una opción</option>
                            {{-- Add new available status --}}
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="stock">Stock</label>
                        <input type="text" class="form-control numeric" name="stock" data-msg="Stock">
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
