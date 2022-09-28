<form id="form-data" action="{{url('notificaciones-push/send')}}" onsubmit="return false;" enctype="multipart/form-data" method="POST" autocomplete="off" data-ajax-type="ajax-form" data-column="0" data-refresh="" data-redirect="" data-table_id="example3" data-container_id="table-container">
    <div class="form-row">
        <div class="form-group col-md-12">
            <label for="type">Tipo notificación</label>
            <select name="type" id="type" name="type" class="form-control not-empty" data-msg="Tipo notificación" style="width: 100%">
                <option value="0" selected>Seleccionar una opción</option>
                <option value="1">General</option>
                <option value="2">Individual</option>
            </select>
        </div>

        <div class="form-group col-md-12">
            <label>Título</label>
            <input type="text" class="form-control not-empty" name="title" maxlength="30" placeholder="Escriba un máximo de 30 caracteres..." data-msg="Título de notificación">
        </div>
        <div class="form-group col-md-12">
            <label>Mensaje</label>
            <textarea type="text" class="form-control not-empty" name="content" maxlength="140" placeholder="Escriba un máximo de 140 caracteres..." data-msg="Mensaje"></textarea>
        </div>
        <div class="form-group col-md-6">
            <label>Fecha</label>
            <input class="form-control date-picker" name="time"  type="text">
        </div>
        <div class="form-group col-md-6">
            <label>Hora</label>
            <input class="form-control timepicker" name="date" type="text">
        </div>
        <div class="form-group col-md-12">
            <label for="filter">Filtro de usuarios</label>
            <select name="filter" id="filter" class="form-control select2" data-msg="Usuarios" style="width: 100%">
                <option value="all">Todos los usuarios</option>
                <option value="top_users">Top 10 de usuarios</option>
                <option value="enabled_users">Usuarios habilitados</option>
                <option value="disabled_users">Usuarios deshabilitados</option>
            </select>
        </div>
        <div class="form-group col-md-12 users-content d-none">
            <label for="users_id">Usuarios</label>
            <select name="users_id[]" id="users_id" class="select-users select2" data-placeholder="Seleccione uno o más usuarios" multiple="multiple" data-msg="Usuarios" style="width: 100%">
                <option value="0" disabled>Seleccionar usuarios</option>
                @foreach($customers as $customer)
                    <option value="{{$customer->id}}">{{$customer->fullname}}</option>
                @endforeach
            </select>
        </div>
    </div>
    <div class="form-group m-t-15">
        <button type="submit" class="btn btn-success save">Enviar</button>
    </div>
</form>