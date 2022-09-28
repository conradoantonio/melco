<table class="table table-hover table-md data-table">
    <thead>
        <tr>
            <th class="d-none">ID</th>
            <th>Clave</th>
            <th>Descripcion</th>
            <th>Categoría</th>
            <th>Precio</th>
            <th>Stock</th>
            <th>Peso</th>
            <th class="text-center">Acciones</th>
        </tr>
    </thead>
    <tbody>
        @foreach($items as $item)
            <tr>
                <td class="align-middle d-none">{{$item->id}}</td>
                <td class="align-middle">
                    <div class="avatar avatar-sm">
                        <img src="{{ asset($item->imagen )}}" class="avatar-img avatar-sm rounded-circle" alt="Producto">
                    </div>
                    <span class="ml-2">{{$item->clave}}</span>
                </td>
                <td class="align-middle">{{$item->descripcion}}</td>
                <td class="align-middle">{!!$item->tipo ? "<span class='badge badge-soft-success'>".$item->tipo->nombre."</span>" : "<span class='badge badge-soft-danger'>No especificado</span>" !!}</td>
                <td class="align-middle">${{$item->precio}}</td>
                <td class="align-middle">
                    {!! (
                        $item->weight > 0 && $item->weight <= 10 ? "<span class='badge badge-soft-warning'>".$item->stock."</span>" : 
                            ( $item->stock > 10 ? "<span class='badge badge-soft-success'>".$item->stock."</span>" : "<span class='badge badge-soft-danger'>".$item->stock."</span>" )
                    ) !!}
                </td>
                <td class="align-middle">
                    {!! (
                        $item->weight > 0 && $item->weight <= 10 ? "<span class='badge badge-soft-info'>".$item->weight."</span>" : 
                            ( $item->weight > 10 ? "<span class='badge badge-soft-info'>".$item->weight."</span>" : "<span class='badge badge-soft-danger'>".$item->weight."</span>" )
                    ) !!}
                    Kg.
                </td>
                <td class="text-center align-middle">
                    <a class="btn btn-dark btn-sm" href="{{url('articulos/form/'.$item->id)}}" data-toggle="tooltip" data-placement="top" title="Editar"><i class="mdi mdi-square-edit-outline"></i></a>
                    <button class="btn btn-danger btn-sm delete-row" data-row-id="{{$item->id}}" data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="mdi mdi-trash-can"></i></button>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>