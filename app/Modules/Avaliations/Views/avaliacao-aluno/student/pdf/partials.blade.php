<div class="container-fluid" style="font-family:calibri light;">
    @php
        $anoletivo=date("Y");
    @endphp

    <div class="row">
        <table class="table_te" style="margin-bottom:5px;">
            <thead style="" class="tabela_cima">
                 <style>
                 .table_te   {background-color: #F5F3F3;!important;width:100%;text-align:left;font-family:calibri light;}
                 .table_te th{ border-left:1px solid #fff;border-bottom: 1px solid #fff;}
                 .table_te td{border-left:1px solid #fff;background-color:#F9F2F4;} 
                 </style>
                 
            <th style="padding: 4px; !important">
               Nº de matrícula 
            </th>
            
            <th style="padding: 4px; !important">
                 Nome completo
            </th>
            
            <th style="padding: 4px; !important">
                  Curso 
            </th>
            
            <th style="padding: 4px; !important">
                  Turma 
            </th>
            
            <th style="padding: 4px; !important">
                  Ano
            </th>
            
            <th style="padding: 4px; !important">          
              Ano lectivo:
            </th>
            
            </thead>
            <tbody style="text-align:center; margin-bottom:4px;">
                <td> {{ $student->meca_number}}</td>
                <td> {{ $student->student }} </td>
                <td> {{ $student->course }} </td>
                <td> {{ $classes }} </td>    
                <td> {{ $student->course_year }}º </td>
                <td> {{ $anoletivo -1 }}/{{ $anoletivo }}</td>
            </tbody>
        </table>
 
    </div>


    <div class="row">

        <table class="table tabela_main" style="border:1px solid #fff;">
            <style>
                 .tabela_main thead{background-color:#F5F3F3; !important ; margin-bottom:5px; border-bottom:4px solid #fff;  }
                 .tabela_main thead th{ border-left:1px solid #fff;}
                 .tabela_main tbody td{ border-left:1px solid #fff; border-bottom:1px solid #fff;}
             </style>    
            <thead style=" text-align:center;">
                <th style="padding: 4px; !important">Disciplinas</th>
                @foreach ($avaliacaos as $avaliacao)
                @foreach ($metricas as $metrica)
                @if ($avaliacao->avaliacaos_id == $metrica->avaliacao_id)
                @if ($metrica->nome != "Exame")
                <th style="padding: 4px; !important">
                    {{ $metrica->nome }}
                </th>
                @endif
                @endif
                @endforeach
                <th style="padding: 4px; !important">
                    {{$avaliacao->nome}} 
                </th>
                @endforeach
                <th style="padding: 4px; !important">Classificação final  </th>
                <th style="padding: 4px; !important">Observações</th>
            </thead>
            <tbody>
                @foreach ($disciplines as $discipline)
                {{-- caso a disciplina NAO tiver exame obrigatorio --}}
                @if($discipline->has_mandatory_exam == 0)
                 <tr>
                    <td class="disciplina_class" style="padding: 5px;  !important;">{{$discipline->display_name}}</td>
                    @php
                    $flag = true;
                    $discipline_id = $discipline->discipline_id;
                    @endphp
                    @foreach ($metricas as $metrica)
                    @php
                    $flag = true;
                    $metrica_id = $metrica->metrica_id;
                    @endphp
                    @foreach ($grades as $grade)
                    @if ($grade->metricas_id == $metrica_id
                    && $grade->disciplines_id == $discipline_id)
                    @if ($grade->metricas_id != 55)
                    @php $flag = false; @endphp
                    <td style="padding:5px; !important;background-color:#F9F2F4;">
                     {{ $grade->nota ??  "F" }}
                    </td>
                    
                    @endif
                    @endif
                    @endforeach
                    @if ($metrica_id != 55)
                    @if ($flag)
                    <td style="padding: 5px; !important;background-color:#F9F2F4;"> - </td>
                    @endif
                    @endif
                    @endforeach

                    @foreach ($finalGrades as $finalGrade)
                    @foreach ($avaliacaos as $avaliacao)
                    @php $avaliacao_id = $avaliacao->avaliacaos_id @endphp

                    @if ($finalGrade->avaliacaos_id == $avaliacao_id
                    && $finalGrade->disciplines_id == $discipline_id)
                    <td style="padding: 5px; !important; background-color:#F9F2F4;">
                        {{round($finalGrade->nota_final)}}
                    </td>

                    @if ($avaliacao_id == 21 && $finalGrade->nota_final >= 0
                    && $finalGrade->nota_final <= 6)<td style="padding: 5px; !important; background-color:#F9F2F4;"> - </td>
                        <td style="padding: 5px; !important; background-color:#F9F2F4;"> {{round($finalGrade->nota_final)}} </td>
                        <td style="padding: 5px; !important; background-color:#F9F2F4;"> Recurso </td>
                        @elseif($avaliacao_id == 21 && $finalGrade->nota_final >= 14
                        && $finalGrade->nota_final <= 20) <td style="padding: 5px; !important; background-color:#F9F2F4;"> - </td>
                            <td style="padding: 5px; !important; background-color:#F9F2F4;"> {{round($finalGrade->nota_final)}} </td>
                            <td style="padding: 5px; !important; background-color:#F9F2F4;"> Aprovado (a) </td>
                            @elseif($avaliacao_id == 23)
                            
                            @foreach ($gradesWithPercentage as $result)
                            @if ($result->discipline_id == $discipline_id)
                            {{$result->grade ?? ''}}

                            @if ($result->grade >= 10)
                            <td style="padding: 5px; !important">Aprovado (a)</td>
                            @else
                            <td style="padding: 5px; !important">Recurso</td>
                            @endif

                            @endif
                            @endforeach
                
                            @endif

                            @endif
                            @endforeach
                            @endforeach
                </tr>
                @else
                {{-- caso for exame obrigatorio --}}
                <tr>
                   <td style="padding: 5px; !important ;" class="disciplina_class">{{ $discipline->display_name}}</td>
                    <style>.disciplina_class{ background-color:#F5F3F3;color:#444; } </style>
                    @php
                    $flag = true;
                    $discipline_id = $discipline->discipline_id;
                    @endphp
                    @foreach ($metricas as $metrica)
                    @php
                    $flag = true;
                    $metrica_id = $metrica->metrica_id;
                    @endphp
                    @foreach ($grades as $grade)
                    @if ($grade->metricas_id == $metrica_id
                    && $grade->disciplines_id == $discipline_id)
                    @if ($grade->metricas_id != 55)
                    @php $flag = false; @endphp
                    <td style="padding: 5px; !important; background-color:#F9F2F4;">
                        {{$grade->nota ??  "F" }}
                    </td>
                    @endif
                    @endif
                    @endforeach
                    @if($metrica_id != 55)
                    @if($flag)
                    <td style="padding: 5px; !important; background-color:#F9F2F4;"> - </td>
                    @endif
                    @endif
                    @endforeach

                    @foreach($finalGrades as $finalGrade)
                    @foreach($avaliacaos as $avaliacao)
                    @php $avaliacao_id = $avaliacao->avaliacaos_id @endphp
                    @if ($finalGrade->avaliacaos_id == $avaliacao_id
                    && $finalGrade->disciplines_id == $discipline_id)
                    <td style="padding: 5px; !important; background-color:#F9F2F4;">
                        {{round($finalGrade->nota_final)}}
                    </td>

                    @if ($avaliacao_id == 21 && $finalGrade->nota_final < 6.5)
                        <td style="padding: 5px; !important; background-color:#F9F2F4;"> -
                        </td>
                        @endif
                        {{--Aqui vem um if poque so pode aparecer se tiver o EXAME--}}
                        @if ($avaliacao->avaliacaos_id != 21)
                        @foreach ($gradesWithPercentage as $percentage)
                       
                        @if ($finalGrade->disciplines_id == $percentage->discipline_id)
                        @if ($percentage->grade != null)
                        <td style="padding: 5px; !important; background-color:#F9F2F4;">{{ round($percentage->grade)}}</td>
                        @endif

                        @if ($percentage->grade >= 10)
                        <td style="padding: 5px; !important;background-color:#F9F2F4;">Aprovado (a) </td>
                        @else
                        <td style="padding: 5px; !important;background-color:#F9F2F4;">Recurso</td>
                        @endif


                        @endif
                        @endforeach

                        @endif
                        @endif
                        @endforeach
                        @endforeach
                </tr>
                @endif
                @endforeach
            </tbody>
        </table>


    </div>

</div>
