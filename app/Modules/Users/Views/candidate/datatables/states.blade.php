 @php $contadorPago = 0; $contadorIf=0; $contadorItem=0; $tam = sizeof($cursos);@endphp
@foreach ($cursos as $item)
    @php $contadorItem++; @endphp
    @if ($item->usuario_id == $state->id)
            @php $contadorIf++; @endphp
            @if ($item->state == 'total')
                <span class='bg-success p-1 text-white'>PAGO</span>
                @php $contadorPago++; @endphp 
            @elseif($item->state == 'pending')
                <span class='bg-info p-1'>EM ESPERA</span>
            @elseif($item->state == 'partial')
                <span class='bg-warning p-1'>PARCIAL</span>
            @endif
    @endif
    @if($contadorItem == $tam && $contadorPago >= 2)
        @if($contadorIf==$contadorPago)
            <span class="page-status"></span>
        @endif
    @endif
@endforeach 
{{--
@php 
    use App\Modules\Users\Controllers\CandidatesController;
    $emolumentos = CandidatesController::get_emolumentos($state->ano_lectivo,$state->id);
@endphp



@foreach ($emolumentos as $item)
        @if ($item->status == 'total')
        <span class='bg-success p-1 text-white'>PAGO</span>

        @elseif($item->status == 'pending')
        <span class='bg-info p-1'>EM ESPERA</span>
        @elseif($item->status == 'partial')
        <span class='bg-warning p-1'>PARCIAL</span>
        @endif
@endforeach
--}}