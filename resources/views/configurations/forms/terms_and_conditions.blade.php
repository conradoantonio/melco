<form id="form-data-terms" action="{{url('configuracion/save/terms-and-conditions')}}" onsubmit="return false;" enctype="multipart/form-data" method="POST" autocomplete="off" data-ajax-type="ajax-form" data-column="0" data-refresh="" data-redirect="1" data-table_id="example3" data-container_id="table-container">
    <input type="text" class="form-control d-none" name="id" value="{{$item->id}}">
    <textarea id="summernote" name="content" class="form-control not-empty" placeholder="Escriba cualquier cosa..." data-msg="Políticas de privacidad">{{$item->terms_conditions}}</textarea>
    <div class="form-group m-t-15">
        <button type="submit" class="btn btn-primary save">Guardar</button>
    </div>
</form>