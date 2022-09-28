<table class="table table-hover table-sm data-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Dirección</th>
            <th>Productos asociados</th>
            <th>Pedidos asociados</th>
            <th class="text-center">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($items as $item)
            <tr>
                <td class="align-middle">{{$item->id}}</td>
                <td class="align-middle">{{$item->nombre_sucursal}}</td>
                <td class="align-middle">{{$item->direccion}}</td>
                <td class="align-middle"><span class="badge badge-soft-dark">{{$item->productos_fastfood->count()}}</span></td>
                <td class="align-middle"><span class="badge badge-soft-dark">{{$item->pedidos->count()}}</span></td>
                <td class="text-center align-middle">
                    <a class="btn btn-dark btn-sm" href="{{url('sucursales/form/'.$item->id)}}" data-toggle="tooltip" data-placement="top" title="Editar"><i class="mdi mdi-square-edit-outline"></i></a>
                    <button class="btn btn-danger btn-sm delete-row" data-row-id="{{$item->id}}" data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="mdi mdi-trash-can"></i></button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>