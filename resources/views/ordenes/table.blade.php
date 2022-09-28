<table class="table table-hover table-sm data-table">
    <thead>
        <tr>
            <th class="">ID</th>
            <th>Cliente</th>
            <th>Método de pago</th>
            <th>Total</th>
            <th>Status</th>
            <th>Fecha</th>
            <th class="text-center">Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach($items as $item)
            <tr>
                <td class="">{{$item->id}}</td>
                <td class="align-middle">
                    <div class="avatar avatar-sm">
                        <img src="{{ asset($item->user->photo)}}" class="avatar-img avatar-sm rounded-circle" alt="user-image">
                    </div>
                    <span class="ml-2">{{$item->user->fullname}}</span></td>
                <td class="align-middle">{!! $item->tipo_pago ? '<span class="badge badge-soft-'.$item->tipo_pago->clase.'">'.$item->tipo_pago->descripcion.'</span>' : '<span class="badge badge-soft-danger">No asignado</span>' !!}</td>
                <td class="align-middle">${{$item->total / 100}}</td>
                <td class="align-middle">{!!'<span class="badge badge-soft-'.$item->status->clase.'">'.$item->status->descripcion.'</span>'!!}</td>
                <td class="align-middle">{{strftime('%d', strtotime($item->fecha)).' de '.strftime('%B', strtotime($item->fecha)). ' del '.strftime('%Y', strtotime($item->fecha))}}</td>
                <td class="text-center align-middle">
                    <a class="btn btn-dark btn-sm" href="{{url('ordenes/detalles/'.$item->id)}}" data-toggle="tooltip" data-placement="top" title="Ver detalles"><i class="mdi mdi-account-details"></i></a>
                    {{-- Mostrar sólo si el pedido no ha sido cancelado o reembolsado --}}
                    @if ( $item->status->id < 3 )
                        <button class="btn btn-warning btn-sm change-order-status" data-row-id="{{$item->id}}" data-row-status="{{$item->status->id}}" data-reload-url="{{Request::url()}}" data-toggle="tooltip" data-placement="top" title="Cambiar status">
                            <i class="mdi mdi-format-list-bulleted" aria-hidden="true"></i>
                        </button>
                    @endif
                    {{-- Debe haber sido pagado con tarjeta y tener cancelación --}}
                    @if(! $item->cancelacion && $item->status->id < 3 )
                        <button class="btn btn-danger btn-sm cancel-row" data-row-id="{{$item->id}}" data-toggle="tooltip" data-placement="top" title="Cancelar"><i class="mdi mdi-cancel"></i></button>
                    @endif
                    {{-- Debe haber sido pagado con tarjeta, cancelado y permitir ser cancelado --}}
                    @if( $item->tipo_pago_id == 1 &&  $item->cancelacion && $item->cancelacion->status == 1 )
                        <button class="btn btn-info btn-sm refund-row" data-row-id="{{$item->id}}" data-toggle="tooltip" data-placement="top" title="Reembolsar"><i class="mdi mdi-cash-refund"></i></button>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
