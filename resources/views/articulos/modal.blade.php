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
