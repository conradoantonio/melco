<table class="table table-hover table-sm data-table">
    <thead>
        <tr>
            <th class="d-none">ID</th>
            <th>Nombre</th>
            <th>Correo</th>
            <th>Sucursal</th>
            <th>Status</th>
            <th>Fecha de creación</th>
            <th class="text-center">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($items as $item)
            <tr>
                <td class="d-none">{{$item->id}}</td>
                <td class="align-middle">
                    <div class="avatar avatar-sm">
                        <img src="{{ asset($item->photo)}}" class="avatar-img avatar-sm rounded-circle" alt="user-image">
                    </div>
                    <span class="ml-2">{{$item->fullname}}</span></td>
                <td class="align-middle"><span class="badge badge-soft-primary">{{$item->email}}</span></td>
                <td class="align-middle">{!!$item->sucursal ? '<span class="badge badge-soft-primary">'.$item->sucursal->nombre_sucursal.'</span>' : '<span class="badge badge-soft-danger">No asignada</span>' !!}</td>
                <td class="align-middle">{!!$item->status == 1 ? '<span class="badge badge-soft-success">Activo</span>' : '<span class="badge badge-soft-danger">Deshabilitado</span>' !!}</td>
                <td class="align-middle">{{strftime('%d', strtotime($item->created_at)).' de '.strftime('%B', strtotime($item->created_at)). ' del '.strftime('%Y', strtotime($item->created_at))}}</td>
                <td class="text-center align-middle">
                    <a class="btn btn-dark btn-sm" href="{{url('usuarios/franquiciatarios/form/'.$item->id)}}" data-toggle="tooltip" data-placement="top" title="Editar"><i class="mdi mdi-square-edit-outline"></i></a>
                    @if( $item->status == 1 )
                    <button class="btn btn-orange btn-sm disable-row" data-row-id="{{$item->id}}" data-change-to="0" data-toggle="tooltip" data-placement="top" title="Deshabilitar"><i class="mdi mdi-close"></i></button>
                    @else
                    <button class="btn btn-success btn-sm enable-row" data-row-id="{{$item->id}}" data-change-to="1" data-toggle="tooltip" data-placement="top" title="Habilitar"><i class="mdi mdi-check"></i></button>
                    @endif
                    <button class="btn btn-danger btn-sm delete-row" data-row-id="{{$item->id}}" data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="mdi mdi-trash-can"></i></button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>