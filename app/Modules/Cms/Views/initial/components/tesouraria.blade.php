<?php
use Carbon\Carbon;
?>


@if(isset($articles['payments']) && (count($articles['payments'])>0))
    <table class="table table-striped table-hover">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Emolumento/Propina</th>
            <th scope="col">Valor</th> 
            <th class="text-center" scope="col">Estado</th>
        </tr>
    </thead>
    <tbody>
        @php
            $count_articles = 1; 
        @endphp
        @if(isset($articles['payments']))
            @foreach ($articles['payments'] as $item)
                <tr>
                    <th scope="row">{{ $count_articles++ }}</th>
                    <td>{{ $item->display_name }}
                        {{ isset($item->mes) ? $item->mes : '' }} 
                        {{ isset($item->disciplina) ?" ( #".$item->code_disciplina." - ".$item->disciplina." ) ": '' }}
                    </td>
                    <td>{{ number_format($item->base_value, 2, ',', '.') }} kz
                    </td>
                    <td>
                        <center>
                            @if ($item->status == 'total')
                                <span
                                    class='bg-success p-1 text-white'>PAGO</span>
                            @elseif($item->status == 'pending')
                                @if (isset($item->year))
                                    @php
                                        $hoje = Carbon::create(date('Y-m-d'));
                                        $limite = Carbon::create($item->year . '-' . $item->month . '-10');
                                    @endphp
                                    @if ($hoje >= $limite)
                                        <span class='bg-info p-1'>ESPERA</span>
                                    @else
                                        <span class='p-1'><i
                                                class="fa fa-clock"></i></span>
                                    @endif
                                @else
                                    <span class='bg-info p-1'>ESPERA</span>
                                @endif
                            @elseif($item->status == 'partial')
                                <span class='bg-warning p-1'>PARCIAL</span>
                            @elseif($item->status == null)
                                <span class='bg-info p-1'>PARCIAL</span>
                            @endif
                        </center>
                    </td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>
@else 
    <div class="alert alert-warning text-dark font-bold">Nenhuma matrícula encontrada neste ano lectivo!  </div>
@endif