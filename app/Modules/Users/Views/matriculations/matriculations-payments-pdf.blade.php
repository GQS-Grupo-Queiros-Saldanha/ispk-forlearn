
@extends('layouts.print')
@section('content')

 
    <main>
        @include('Reports::pdf_model.pdf_header')

        @php
             $getMonthPendent=[];
             $somaPedente=0;
             $somaLiquidade=0;
             $getMonthLiquidado=[];
        @endphp
      
        <div class="mt-2 col">
            <table class="table table-striped">
                <thead>
                    <tr style="font-size: 1.1pc">
                    <th scope="col" id="dado">#</th>
                    <th scope="col">Matrícula</th>
                    <th scope="col">Nome do estudante</th>
                    <th scope="col">Curso</th>
                    @if ($anolectivoActivo[0]->display_name=='20/21')
                        <th scope="col"  id="mesAtivoMarco">Março<sup>2020</sup> </th>
                    @else   
                    @endif
                    <th scope="col">Outubro</th>
                    <th scope="col">Novembro</th>
                    <th scope="col">Dezembro</th>
                    <th scope="col">Janeiro</th>
                    <th scope="col">Fevereiro</th>
                    <th scope="col">Março</th>
                    <th scope="col">Abril</th>
                    <th scope="col">Maio</th>
                    <th scope="col">Junho</th>
                    <th scope="col">Julho</th>
                    </tr>
                </thead>
                <tbody class="p-3">
                    @php $i=0; $vetorArticl=[];$articlAtivo=null; @endphp
                    @foreach ($emoluments as  $matriculation_number => $emolument)
                        @php $i++; @endphp
                        @foreach ($emolument as $course_name=> $emolumen)
                            @foreach ($emolumen as $user_name=>$emolumentos)
                                <tr class="p-3">
                                    <td class="p-2" scope="row">{{$i}}</td>
                                    <td class="p-2">{{$matriculation_number}}</td>
                                    <td class="p-2">{{$user_name}}</td>
                                    <td class="p-2">{{$course_name}}</td>
                                    @foreach ($emolumentos as $articlMonth)
                                        @foreach ($articlMonth as $element)
                                            @if (empty($vetorArticl))
                                                @php $vetorArticl[]=$element->id_article_requests; $articlAtivo=true @endphp
                                            @else
                                                @if (in_array($element->id_article_requests, $vetorArticl))
                                                    @php $articlAtivo=false @endphp
                                                @else
                                                    @php $vetorArticl[]=$element->id_article_requests; $articlAtivo=true @endphp
                                                @endif   
                                            @endif
                                            
                                            @if ($articlAtivo==true)
                                                @if ($element->status == "total")
                                                    @php $getMonthLiquidado[]=(object)['month'=>$element->article_month, 'year'=>$element->article_year] @endphp
                                                    <th class="p-2"> {{$element->article_month}} - <span class='bg-success p-1 text-white'>PAGO</span></th>
                                                @elseif($element->status == "pending")
                                                    @php $getMonthPendent[]=(object)['month'=>$element->article_month, 'year'=>$element->article_year] @endphp
                                                    <th class="p-2">{{$element->article_month}} - <span class='bg-info p-1'>ESPERA</span></th>
                                                @elseif($element->status == "partial")
                                                    <th class="p-2">{{$element->article_month}} - <span class='bg-warning p-1'>PARCIAL</span></th>
                                                    @php $getMonthPendent[]=(object)['month'=>$element->article_month, 'year'=>$element->article_year] @endphp
                                                @elseif($element->status =='error')
                                                    <th class="p-2">{{$element->article_month}} - <span class='bg-danger p-1 text-white'>ERRO</span></th>
                                                @elseif($element->status ==null)
                                                    @php $getMonthPendent[]=(object)['month'=>$element->article_month, 'year'=>$element->article_year] @endphp
                                                    <th class="p-2">{{$element->article_month}} - <span class='bg-info p-1'>ESPERA</span></th>
                                                @endif    
                                            @endif 
                                        @endforeach
                                    @endforeach
                                </tr>
                            @endforeach
                        @endforeach    
                    @endforeach
                </tbody>
                </table>
        </div>







        <div style="margin-left:15%;page-break-before: always;">
            <br>
            <table class="table_te" style="width: 1000px;background-color: #F5F3F3;color:#000;">
                <tr style="">
                    <th class="text-center" style="font-size: 16pt;" colspan="2">QUADRO RESUMO</th>
                </tr>
            </table>
            <table class="table_te mt-1 mb-1" style="width:500px;float: left;margin-right: 10px;">
                <thead style="background-color: #F5F3F3">
                    <tr>
                        <th class="text-center" style="font-size: 16pt;" colspan="2">PROPINAS PENDENTES</th>
                    </tr>
                </thead>
                
                <thead>
                     <tr>
                         <th class="text-center" style="font-size: 16pt;">Mês</th>
                         <th class="text-center" style="font-size: 16pt;">Quantidade</th>
                     </tr>
                </thead>
                <tbody>
                    @if ($anolectivoActivo[0]->display_name=='20/21')
                        <tr class="" style="background-color: #F5F3F3;border-bottom: white 4px solid; padding: 6px">
                            <td class="text-center" style="font-weight: bold; border-right: white 1px solid;">Março</td>
                            <td class="text-center" style="font-weight: bold;">
                                @foreach ($getMonthPendent as $element)
                                    @if ($element->month==3 and $element->year==2020)
                                        @php $somaPedente+=1 @endphp   
                                    @endif
                                @endforeach
                                {{$somaPedente}}  
                            </td>
                        </tr>
                    @else   
                    @endif
                    
                    @foreach($ordem_Month as $item)
                        @php $somaPedente=0 @endphp
                        <tr class="" style="background-color: #F5F3F3;border-bottom: white 4px solid; padding: 6px">
                            <td class="text-center" style="font-weight: bold; border-right: white 1px solid;">{{$item['display_name']}}</td>
                            <td class="text-center" style="font-weight: bold;">
                                @foreach ($getMonthPendent as $element)
                                    @if ($item['id']==$element->month and $element->month!=3 and $element->year!=2020)
                                        @php $somaPedente+=1 @endphp   
                                    @elseif($item['id']==$element->month and $element->month==3 and $element->year!=2020)
                                        @php $somaPedente+=1 @endphp 
                                    @elseif($item['id']==$element->month and $element->month!=3 and $element->year==2020)
                                        @php $somaPedente+=1 @endphp  
                                    @endif
                                @endforeach
                               {{$somaPedente}}
                            </td>
                        </tr>
                    @endforeach
                     
                     
                     {{-- <tr style="margin-top: 5%">
                         <td class="text-right" style="font-weight: bold">TOTAL</td>
                         <td class="text-center" style="font-weight:bold"></td>
                     </tr> --}}
                </tbody>
            </table>



            <table class="table_te mt-1 mb-1" style="width:490px;">
                <thead style="background-color: #F5F3F3">
                    <tr>
                        <th class="text-center" style="font-size: 16pt;" colspan="2">PROPINAS LIQUIDADOS</th>
                    </tr>
                </thead>
                
                <thead >
                    <tr>
                        <th class="text-center" style="font-size: 16pt;">Mês</th>
                        <th class="text-center" style="font-size: 16pt;">Quantidade</th>
                    </tr>
                </thead>
                <tbody style="width:490px;" > 
                    @if ($anolectivoActivo[0]->display_name=='20/21')
                        <tr class="" style="background-color: #F5F3F3;border-bottom: white 4px solid; padding: 6px">
                            <td class="text-center" style="font-weight: bold; border-right: white 1px solid;">Março</td>
                            <td class="text-center" style="font-weight: bold;">
                                @foreach ($getMonthLiquidado as $element)
                                    @if ($element->month==3 and $element->year==2020)
                                        @php $somaLiquidade+=1 @endphp   
                                    @endif
                                @endforeach
                            {{$somaLiquidade}}
                            </td>
                        </tr>
                    @else   
                    @endif
                    @foreach($ordem_Month as $item)
                        @php $somaLiquidade=0 @endphp
                        <tr class="" style="background-color: #F5F3F3;border-bottom: white 4px solid; padding: 6px">
                            <td class="text-center" style="font-weight: bold; border-right: white 1px solid;">{{$item['display_name']}}</td>
                            <td class="text-center" style="font-weight: bold;">
                                @foreach ($getMonthLiquidado as $element)
                                    @if ($item['id']==$element->month and $element->month!=3 and $element->year!=2020)
                                        @php $somaLiquidade+=1 @endphp   
                                    @elseif($item['id']==$element->month and $element->month==3 and $element->year!=2020)
                                        @php $somaLiquidade+=1 @endphp 
                                    @elseif($item['id']==$element->month and $element->month!=3 and $element->year==2020)
                                        @php $somaLiquidade+=1 @endphp  
                                    @endif
                                @endforeach
                               {{$somaLiquidade}}
                            </td>
                        </tr>
                    @endforeach
                    {{-- <tr style="font-weight:bold">
                        <td class="text-right" style="">TOTAL</td>
                        <td class="text-center"></td>
                    </tr> --}}
                </tbody>
            </table>
        </div>
        </div>
        <br>
        <br>
        <br>
        <br>
        <div class="">
            <br>
            <br>
        </div>
        </div>
        </div>
        </div>
        </div>
    </main>
@endsection

<script>
    // window.print();
</script>  
