


<tr class="{{$cor}}">
    <td style="text-align:center;">{{ $discipline->course_year}} º</td>

    @if (substr($discipline->code, -3, 1) == "A")
        <td style="text-align: center;">A</td>
    @elseif(substr($discipline->code, -3, 1) == "1")
        <td style="text-align: center;">1</td>
    @elseif(substr($discipline->code, -3, 1) == "2")
        <td style="text-align: center;">2</td>
    @endif
    <!--<td style="text-align: center;">{{ $discipline->code }}</td>-->

    <td style="text-align: left;">{{ $discipline->name}}
        @php $contaDisciplina++; @endphp
    </td>
    <td style="text-align: left;">{{ $discipline->area}} </td>



        @foreach($cargaHoraria as $carga)   
             @if($carga->id_disciplina===$discipline->id)
                <td style="text-align: center;">{{ $carga->hora}}</td>
             @endif
         @endforeach



    @foreach ($oldGrades as $year => $oldGradex)
    @php $flag = true @endphp
    @php $oFlag = true; @endphp
        @foreach ($oldGradex as $oldGrade)
            @if ($oldGrade->discipline_id == $discipline->id)
                @php $flag = false @endphp

                @if ($discipline->area_id == 13)
                    @php
                        $areaGeral += $oldGrade->grade;
                        $contaGeral++ ;
                    @endphp
                    @elseif($discipline->area_id == 14)
                    @php
                        $areaProfissional += $oldGrade->grade;
                        $contaProfissional++;
                    @endphp
                    @elseif($discipline->area_id == 15)
                    @php
                        $areaEspecifia += $oldGrade->grade;
                        $contaEspecifica++;
                    @endphp
                @endif
                <td style="text-align: center;background-color: #F9F2F4;">{{ round($oldGrade->grade) }}</td>
            @endif
    @endforeach
      
            @if ($flag)
                <td style="background-color: #F9F2F4;"></td>
            @endif
        @endforeach
        @if ($var == 1)
        @foreach ($grades as $grade)
           
            @php $oFlag = true @endphp
            {{-- aqui falta comparar o id do pl ano de edicao de estudo, caso a disciplina acarretar negativa --}}
            @if ($grade->discipline_id == $discipline->id)
                    @if ($discipline->area_id == 13)
                        @php
                            $areaGeral += round($grade->percentage_mac + $grade->percentage_neen);
                            $contaGeral++ ;
                            @endphp
                        @elseif($discipline->area_id == 14)
                        @php
                            $areaProfissional += round($grade->percentage_mac + $grade->percentage_neen);
                            $contaProfissional++;
                        @endphp
                        @elseif($discipline->area_id == 15)
                        @php
                            $areaEspecifia += round($grade->percentage_mac + $grade->percentage_neen);
                            $contaEspecifica++;
                        @endphp
                    @endif
                    @php $oFlag = false @endphp
                   <td style="text-align: center; background-color: #F9F2F4;">{{ round($grade->percentage_mac + $grade->percentage_neen) }}</td>
               @endif
        @endforeach
        @endif
</tr>





