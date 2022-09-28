<table class="table table-hover table-sm data-table">
    <thead>
        <tr>
            <th class="d-none">ID</th>
            <th>Autor</th>
            <th>Pregunta</th>
            <th>Respuesta</th>
            {{-- <th>Status</th> --}}
            <th class="text-center">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($items as $item)
            <tr>
                <td class="align-middle d-none">{{$item->id}}</td>
                <td class="align-middle">{!!$item->user ? '<span class="badge badge-soft-info">'.$item->user->fullname.'</span>' : '<span class="badge badge-soft-danger">Desconocido o eliminado</span>' !!}</td>
                <td class="align-middle">{{$item->pregunta}}</td>
                <td class="align-middle">{{$item->respuesta}}</td>
                <td class="align-middle">{!!$item->status == 1 ? '<span class="badge badge-soft-info">Activa</span>' : '<span class="badge badge-soft-danger">Inactiva</span>' !!}</td>
                <td class="text-center align-middle">
                    <a class="btn btn-dark btn-sm" href="{{url('formulario-de-contacto/form/'.$item->id)}}" data-toggle="tooltip" data-placement="top" title="Editar"><i class="mdi mdi-square-edit-outline"></i></a>
                    <button class="btn btn-danger btn-sm delete-row" data-row-id="{{$item->id}}" data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="mdi mdi-trash-can"></i></button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>