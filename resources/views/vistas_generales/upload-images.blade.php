<!-- Modal -->
<div class="modal fade" id="modal-upload-content" tabindex="-1" role="dialog" aria-labelledby="BottomRightLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="BottomRightLabel">Cargar imágenes</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <form id="dropzone-form" method="POST" class="dropzone myDropzone" action="{{url('system/upload-content')}}" enctype="multipart/form-data">
                    {{csrf_field()}}
                    <div class="col-lg-12 d-none">
                        <div class="form-group">
                            <input class="form-controls" type="text" value="" id="row_id" name="row_id">
                        </div>
                    </div>
                    <div class="col-lg-12 d-none">
                        <div class="form-group">
                            <input class="form-controls" type="text" value="" id="path" name="path">
                        </div>
                    </div>
                    <div class="col-lg-12 d-none">
                        <div class="form-group">
                            <input class="form-controls" type="text" value="" id="rename" name="rename">
                        </div>
                    </div>
                    <div class="col-lg-12 d-none">
                        <div class="form-group">
                            <input class="form-controls" type="text" value="" id="resize" name="resize">
                        </div>
                    </div>
                    <div class="dz-message">
                        <h1 class="display-4"><i class=" mdi mdi-progress-upload"></i></h1>
                        Arrastra las imágenes aquí o haz clic.<BR>
                        <SPAN class="note needsclick">(Solo archivos <STRONG>jpg, jpeg, png, o gif</STRONG>)</SPAN>
                        <div class="p-t-5">
                            <a href="#" class="btn btn-lg btn-primary">Subir archivos</a>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>