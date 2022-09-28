<form id="form-data" action="{{url('mi-perfil/update')}}" onsubmit="return false;" enctype="multipart/form-data" method="POST" autocomplete="off" data-ajax-type="ajax-form" data-column="0" data-refresh="" data-redirect="" data-table_id="example3" data-container_id="table-container">
    <h3 class="">Información personal</h3>
    <p class="text-muted">Usa este formulario para cambiar tu información actual y cambiar tu contraseña.</p>
    {{-- Foto de perfil --}}
    <div class="text-center">
        <label class="avatar-input">
            <span class="avatar avatar-xxl">
                <img src="{{asset($item->photo)}}" alt="..." class="avatar-img avatar-profile-img rounded-circle">
                <span class="avatar-input-icon rounded-circle"><i class="mdi mdi-upload mdi-24px"></i></span>
            </span>
            <input type="file" name="avatar" class="avatar-file-picker file image" data-target="avatar-profile-img" data-msg="Foto de perfil">
        </label>
    </div>
    {{-- Inputs --}}
    <div class="form-row">
       <div class="form-group col-md-12">
           <label for="fullname">Nombre completo</label>
           <input type="text" class="form-control not-empty" value="{{$item->fullname}}" name="fullname" placeholder="Nombre completo" data-msg="Nombre completo">
       </div>
    </div>
    <div class="form-row">
       <div class="form-group col-md-12">
           <label for="fullname">Rol de usuario</label>
           <input type="text" class="form-control" readonly value="{{$item->role->descripcion}}">
       </div>
    </div>
    <div class="form-row">
       <div class="form-group col-md-6">
           <label for="email">Correo</label>
           <input type="email" class="form-control not-empty" readonly value="{{$item->email}}" name="email" placeholder="Correo" data-msg="Correo">
       </div>
       <div class="form-group col-md-6">
           <label for="phone">Teléfono</label>
           <input type="text" class="form-control" value="{{$item->phone}}" name="phone" placeholder="Teléfono" data-msg="Teléfono">
       </div>
    </div>
        <div class="form-group">
           <label for="password">Contraseña actual</label>
           <input type="text" class="form-control pass-font not-empty" name="current_password" placeholder="Contraseña" data-msg="Contraseña actual">
        </div>
        <div class="form-group">
           <label for="password">Contraseña nueva</label>
           <input type="text" class="form-control pass-font" name="new_password" placeholder="Contraseña">
        </div>
    <button type="submit" class="btn btn-success save">Guardar</button>
</form>