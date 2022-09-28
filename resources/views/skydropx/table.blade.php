<table class="table table-hover table-sm data-table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Noº Guía </th>
            <th>Ver Guía</th>
            <th>Fecha de Creación</th>
        </tr>
    </thead>
    <tbody>
        @foreach($labels as $label)
            <tr>
                <td class="align-middle">{{$label->id}}</td>
                <td class="align-middle">{{$label->attributes->tracking_number}}</td>
                <td class="align-middle">{{ \Carbon\Carbon::parse($label->attributes->created_at)->format('d-m-Y h:i') }}</td>
                <td class="align-middle">
                    <a class="btn btn-md btn-primary" target="_blank" href="{{ $label->attributes->label_url }}">Descargar Guía</a>
                </td>
                {{-- <td class="text-center align-middle">
                    <a class="btn btn-dark btn-sm" href="{{url('formulario-de-contacto/form/'.$label->id)}}" data-toggle="tooltip" data-placement="top" title="Editar"><i class="mdi mdi-square-edit-outline"></i></a>
                    <button class="btn btn-danger btn-sm delete-row" data-row-id="{{$label->id}}" data-toggle="tooltip" data-placement="top" title="Eliminar"><i class="mdi mdi-trash-can"></i></button>
                </td> --}}
            </tr>
        @endforeach
    </tbody>
</table>