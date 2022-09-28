<table class="table table-hover table-sm data-table">
    <thead>
        <tr>
            <th class="d-none">ID</th>
            <th>Clave</th>
            <th>Descripcion</th>
            <th>Categoría</th>
            <th>Precio</th>
            <th class="text-center">Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($items as $item)
            <tr>
                <td class="align-middle d-none">{{$item->art_id}}</td>
                <td class="align-middle">
                    <div class="avatar avatar-sm">
                        <img src="{{ asset($item->imagen_principal)}}" class="avatar-img avatar-sm rounded-circle" alt="Producto">
                    </div>
                    <span class="ml-2">{{$item->clave}}</span>
                </td>
                <td class="align-middle">{{$item->descripcion}}</td>
                <td class="align-middle">{!!$item->categoria ? "<span class='badge badge-soft-success'>".$item->categoria->nombre_categoria."</span>" : "<span class='badge badge-soft-danger'>No especificado</span>" !!}</td>
                <td class="align-middle">${{$item->precio1}}</td>
                <td class="text-center align-middle">
                    <a class="btn btn-dark btn-sm" href="{{url('productos/supermarket/form/'.$item->art_id)}}" data-toggle="tooltip" data-placement="top" title="Editar"><i class="mdi mdi-square-edit-outline"></i></a>
                    <button class="btn btn-danger btn-sm delete-row" data-row-id="{{$item->art_id}}" data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="mdi mdi-trash-can"></i></button>
                    <button class="btn btn-info btn-sm info-row" data-row-id="{{$item->art_id}}" data-toggle="tooltip" data-placement="top" title="Detalles"><i class="mdi mdi-eye-outline"></i></button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>