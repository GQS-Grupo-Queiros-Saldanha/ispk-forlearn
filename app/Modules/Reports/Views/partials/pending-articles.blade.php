@php use App\Modules\Reports\Controllers\DocsReportsController; @endphp
<style>
    .bg2 {
        font-weight: normal !important;
    }
</style>
<div class="container-fluid" style="padding:0;">
    <div class="row">
        <div class="col-md-12">

            <br><br>
            @php $total = 0; $linha=0; @endphp
            <table class="table  tabela_principal" style="width:100%;">
                <thead class="bg1">
                    <th style="font-size:1pc; text-align: center;width:4pc;">#</th>
                    <th style="font-size:1pc; text-align: center;width: 130px!important;">Nº Matricula </th>
                    <th style="font-size:1pc; text-align: center;">Estudante </th>
                    <th style="font-size:1pc; text-align: center;">Curso </th>
                    <th style="font-size:1pc; text-align: center;width: 100px!important;">Turma </th>
                    <th style="font-size:1pc; text-align: center;">Emolumento / Propina</th>
                    <th style="font-size:1pc; text-align: center;width: 150px!important;">Valor</th>
                    {{-- <th style="font-size:1pc;width:20pc;">Estado do Pagamento</th> --}}
                    <th style="font-size:1pc; text-align: center;width: 110px!important;">Valor a pagar</th>
                </thead>
                <tbody class="tbody-parameter-group">
                    @php $p = 0; @endphp
                    @foreach ($emoluments as $matriculation_number => $emolument)
                    @foreach ($emolument as $course_name => $emolumen)
                    @foreach ($emolumen as $user_name => $emolumentos)
                    @php $subtotal = 0; $linha++; @endphp



                    @php $k=1; $i=0; $user=null; $valorApagar=null; @endphp
                    @foreach ($emolumentos as $item)
                    @foreach ($item as $emolument)

                    @if(in_array($emolument->id_article_requests,$out_art_requests))
                    @continue;
                    @endif

                    @php

                    $cor= $k++ % 2 === 0 ? 'cor_linha' : '';

                    @endphp
                    @if ($user==null)
                    @php $i++; $user=$emolument->user_id; @endphp
                    @elseif($user==$emolument->user_id)
                    @php $i++; $user=$emolument->user_id; @endphp
                    @else
                    @php $i=1;
                    $i++; $user=$emolument->user_id; @endphp

                    @endif
                    <tr class="bg2">
                        <td style="text-align: center;">{{++$p}}</td>
                        <td style="text-align: center;">{{ $matriculation_number }}</td>
                        <td>{{$user_name}}</td>
                        <td>{{ $course_name }}</td>
                        <td style="text-align: center;">{{DocsReportsController::getTurma($emolument->id,$emolument->lective_year,$emolument->year)}}</td>
                        <td class="td-parameter-column">{{ $emolument->article_name}} - {{ $emolument->discplina_display_name}}
                            @if($emolument->article_month == 1)
                            ( Janeiro {{ $emolument->article_year}} )
                            @elseif($emolument->article_month == 2)
                            ( Fevereiro {{ $emolument->article_year}} )
                            @elseif ( $emolument->article_month == 3)
                            ( Março {{ $emolument->article_year}} )
                            @elseif ($emolument->article_month == 4)
                            ( Abril {{ $emolument->article_year}} )
                            @elseif ($emolument->article_month == 5)
                            ( Maio {{ $emolument->article_year}} )
                            @elseif ($emolument->article_month == 6)
                            ( Junho {{ $emolument->article_year}} )
                            @elseif ($emolument->article_month == 7)
                            ( Julho {{ $emolument->article_year}} )
                            @elseif ($emolument->article_month == 8)
                            ( Agosto {{ $emolument->article_year}} )
                            @elseif ($emolument->article_month == 9)
                            ( Setembro {{ $emolument->article_year}} )
                            @elseif ($emolument->article_month == 10)
                            ( Outubro {{ $emolument->article_year}} )
                            @elseif ($emolument->article_month == 11)
                            ( Novembro {{ $emolument->article_year}} )
                            @elseif ($emolument->article_month == 12)
                            ( Dezembro {{ $emolument->article_year}} )
                            @endif
                        </td>
                        <td style="text-align: right;" class="td-parameter-column">
                            @if(isset($emolument->rule_value))
                            {{ number_format($emolument->rule_value, 2, ",", ".") }} -
                            <s>{{ number_format($emolument->value, 2, ",", ".") }} Kz</s>
                            @else
                            {{ number_format($emolument->value, 2, ",", ".") }} Kz
                            @endif
                        </td>
                        <td style="text-align: right" class="td-parameter-column">
                            @if ($emolument->balance<0)
                                @php $valorApagar=-1*($emolument->balance) @endphp
                                @if($emolument->status=="partial")
                                {{ number_format($valorApagar/2, 2, ",", ".") }} Kz
                                @else
                                {{ number_format($valorApagar, 2, ",", ".") }} Kz
                                @endif
                                @else

                                @php $valorApagar=$emolument->balance @endphp
                                {{ number_format($valorApagar, 2, ",", ".") }} Kz
                                @endif



                                @if($emolument->status=="partial")
                                @php $subtotal += $valorApagar/2; $total += $valorApagar/2; @endphp
                                @else
                                @php $subtotal += $valorApagar; $total += $valorApagar; @endphp
                                @endif
                        </td>

                    </tr>
                    @endforeach
                    @endforeach
                </tbody>

                <tr class="">
                    <td class="tfoot"></td>
                    <td class="tfoot"></td>
                    <td class="tfoot"></td>
                    <td class="tfoot"></td>
                    <td class="tfoot"></td>
                    <td class="tfoot"></td>
                    <td class="tfoot"><b></b></td>
                    <td class="tfoot"><b>Subtotal: &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                            &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                            &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                            {{ number_format($subtotal, 2, ",", ".") }} </b>Kz</td>
                </tr>


                @endforeach
                @endforeach
                @endforeach
            </table>
            <div style="width: 300px; float: right;">
                </br>
                <table class="table table-parameter-group" style="width:250px;" width="10px" cellspacing="2">
                    <thead class="">
                        <th style="text-align: center;" class="th-parameter-group bg1">Total</th>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="font-size:13pt !important; text-align: right; border-bottom:1px solid !important;"> <b> {{ number_format($total, 2, ",", ".") }}</b> Kz</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>