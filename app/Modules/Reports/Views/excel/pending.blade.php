@php use App\Modules\Reports\Controllers\DocsReportsController; @endphp

<table>
    <thead>
        <tr>
            <th colspan="6" rowspan="2" style="text-align: center!important;">{{$titulo_documento}}
                <br> DATA: {{$date1}} - {{$date2}}
            </th>
            <th colspan="2" rowspan="2">{{$institution->nome}}</th>
        </tr>

    </thead>
    <tbody>
        <tr>
            <td></td>
        </tr>

    </tbody>
</table>

@php
$total = 0;
$linha = 0;
@endphp
<table>
    <thead class="">
        <tr>

            <th> # </th>
            <th>Nº Matricula </th>
            <th>Estudante </th>
            <th>Curso </th>
            <th>Turma </th>
            <th>Emolumento / Propina</th>
            <th>Valor</th>
            <th>Valor a pagar</th>
        </tr>
    </thead>

    <tbody>
        @foreach ($emoluments as $matriculation_number => $emolument)
        @foreach ($emolument as $course_name => $emolumen)
        @foreach ($emolumen as $user_name => $emolumentos)
        @php
        $subtotal = 0;
        $linha++;
        @endphp


        @php
        $k = 1;
        $i = 0;
        $user = null;
        $valorApagar = null;
        @endphp
        @foreach ($emolumentos as $item)
        @foreach ($item as $emolument)

        @if(in_array($emolument->id_article_requests,$out_art_requests))
        @continue;
        @endif


        @php $cor= $k++ % 2 === 0 ? 'cor_linha' : ''; @endphp
        @if ($user == null)
        @php
        $i++;
        $user = $emolument->user_id;
        @endphp
        @elseif($user == $emolument->user_id)
        @php
        $i++;
        $user = $emolument->user_id;
        @endphp
        @else
        @php
        $i = 1;
        $i++;
        $user = $emolument->user_id; @endphp
        @endif
        <tr class="{{ $cor }}">
            <td>{{ $i }}</td>
            <td>{{ $matriculation_number }}</td>
            <td>{{ $user_name }}</td>
            <td>{{ $course_name }}</td>
            <td>{{ DocsReportsController::getTurma($emolument->id, $emolument->lective_year, $emolument->year) }}
            </td>
            <td class="td-parameter-column">{{ $emolument->article_name }} -
                {{ $emolument->discplina_display_name }}
                @if ($emolument->article_month == 1)
                (Janeiro {{ $emolument->article_year }})
                @elseif($emolument->article_month == 2)
                ( Fevereiro {{ $emolument->article_year }} )
                @elseif ($emolument->article_month == 3)
                ( Março {{ $emolument->article_year }} )
                @elseif ($emolument->article_month == 4)
                ( Abril {{ $emolument->article_year }} )
                @elseif ($emolument->article_month == 5)
                ( Maio {{ $emolument->article_year }} )
                @elseif ($emolument->article_month == 6)
                ( Junho {{ $emolument->article_year }} )
                @elseif ($emolument->article_month == 7)
                ( Julho {{ $emolument->article_year }} )
                @elseif ($emolument->article_month == 8)
                ( Agosto {{ $emolument->article_year }} )
                @elseif ($emolument->article_month == 9)
                ( Setembro {{ $emolument->article_year }} )
                @elseif ($emolument->article_month == 10)
                ( Outubro {{ $emolument->article_year }} )
                @elseif ($emolument->article_month == 11)
                ( Novembro {{ $emolument->article_year }} )
                @elseif ($emolument->article_month == 12)
                ( Dezembro {{ $emolument->article_year }} )
                @endif
            </td>
            <td style="text-align: right" class="td-parameter-column">
                @if(isset($emolument->rule_value))
                {{ number_format($emolument->rule_value, 2, ",", ".") }} -
                <s>{{ number_format($emolument->value, 2, ",", ".") }} Kz</s>
                @else
                {{ number_format($emolument->value, 2, ",", ".") }} Kz
                @endif

            <td style="text-align: right" class="td-parameter-column">
                @if ($emolument->balance < 0)
                    @php $valorApagar=-1*($emolument->balance) @endphp
                    {{ number_format($valorApagar, 2, ',', '.') }} Kz
                    @else
                    @php $valorApagar=$emolument->balance @endphp
                    {{ number_format($valorApagar, 2, ',', '.') }} Kz
                    @endif

                    @php
                    $subtotal += $valorApagar;
                    $total += $valorApagar;
                    @endphp
            </td>

        </tr>
        @endforeach
        @endforeach


        <tr>
            <td class="tfoot"></td>
            <td class="tfoot"></td>
            <td class="tfoot"></td>
            <td class="tfoot"></td>
            <td class="tfoot"></td>
            <td class="tfoot"></td>
            <td style="text-align: right!important;"><b>Subtotal</b></td>
            <td class="tfoot"><b>{{ number_format($subtotal, 2, ',', '.') }} </b> Kz</td>
        </tr>
        @endforeach
        @endforeach
        @endforeach
    </tbody>
</table>
<table cellspacing="2">
    <thead>

    </thead>
    <tbody>
        <tr>

            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td></td>
            <td style="text-align: right!important;"><b>Total</b></td>
            <td> <b> {{ number_format($total, 2, ',', '.') }}</b> Kz</td>

        </tr>
    </tbody>
</table>