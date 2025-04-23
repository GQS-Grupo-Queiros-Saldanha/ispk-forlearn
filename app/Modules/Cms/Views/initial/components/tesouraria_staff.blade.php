<?php
use Carbon\Carbon;
?>
<style>
    .table-resumo table tbody td{
        font-weight: normal!important;
    }
</style>
{{-- @if (isset($matriculation)) --}}
<div class="row">
    <div class="col-md-6 table-resumo">
        <table class="table  table-striped table-hover mb-0">
            <thead>
                <tr class="bg0">
                    <th colspan="6" class="text-white">RESUMO DA SEMANA</th>
                </tr>
                <tr class="bg2">
                    <th scope="col">#</th>
                    <th scope="col">Dia</th>
                    <th scope="col" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $count_articles = 1;
                    $total_week = 1;
                    $current = null;
                @endphp
                @if (isset($articles['date']['week']))
                    @foreach ($articles['date']['week'] as $key => $item)
                        <tr class='{{ $item['date'] == date('Y-m-d') ? 'bg-green' : '' }}'>
                            <td>{{ $count_articles++ }}</td>
                            <td>
                                @switch($key)
                                    @case('seg')
                                        Segunda-feira
                                    @break

                                    @case('ter')
                                        Terça-feira
                                    @break

                                    @case('qua')
                                        Quarta-feira
                                    @break

                                    @case('qui')
                                        Quinta-feira
                                    @break

                                    @case('sex')
                                        Sexta-feira
                                    @break
                                     @case('sab')
                                        Sábado
                                    @break

                                    @default
                                @endswitch
                                ({{ date('d/m/Y', strtotime($item['date'])) }})
                            </td>
                            @if($current)
                                <td class="text-right"> - </td>  
                            @else
                            <td class="text-right"> 
                                {{ isset($articles['days'][$item['date']]) ? $articles['days'][$item['date']] : "0,00" }} kz
                            </td>
                            @endif
                            @if(date("Y-m-d")==$item['date'])
                                @php $current=1 @endphp
                            @endif
                        </tr>
                    @endforeach
                    <tr class="bg1">
                        <th class="text-left" colspan="2">TOTAL</th>
                        <th class="bg1 text-right">{{ number_format($articles['days']['total'], 2, ',', '.') }} kz</th>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div class="col-6 table-resumo">
        <table class="table table-striped table-hover mb-0">
            <thead>
                <tr class="bg0">
                    <th colspan="3"  class="text-white">RESUMO ANUAL</th>
                </tr>
                <tr class="bg2">
                    <th scope="col">#</th>
                    <th scope="col">Mês</th>
                    <th scope="col" class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>

                
                 @php 
                    $i = 1;
                    $current = null;
                 @endphp
                 @foreach ($articles["month"] as  $key=>$item)
                   
                    <tr class="{{("m".date("m")==$key) ?"bg-green":"" }}">
                        <td>{{$i++}}</td>
                        <td>{{$item["nome"]}}</td>
                        @if($current)
                            <td class="text-right"> - </td>  
                        @else
                            <td class="text-right">{{$item["money"]}} kz</td>  
                        @endif                      
                    </tr>
                    @if(("m".date("m")==$key))
                        @php $current=1; @endphp
                    @endif

                    @if($i==13)
                        @break
                    @endif
                 @endforeach
                 <tr class="bg1">
                    <th class="text-left" colspan="2">TOTAL</th>
                    <th class="bg1 text-right">{{$articles["month"]["total"]}} kz</th>
                </tr>
            </tbody>
        </table>
    </div>

</div>

{{-- @else  --}}
{{-- <div class="alert alert-warning text-dark font-bold">Nenhuma matrícula encontrada neste ano lectivo!  </div> --}}
{{-- @endif --}}
