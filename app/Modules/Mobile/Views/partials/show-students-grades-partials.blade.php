
<table class="table table-parameter-group" border="1" width="100%" style="text-align:center;">
    <thead class="thead-parameter-group">
        <tr>
            <th class="th-parameter-group" style="font-size: 8pt; text-align: left; ">Licenciatura em </th>
            <th class="th-parameter-group" style="font-size: 8pt; text-align: center;" colspan="3">Vagas</th>
            <th class="th-parameter-group" style="font-size: 8pt; text-align: center;">Total</th>
        </tr>
    </thead>
    <tbody>
        <tr>
           <td style="text-align: left;">
               {{ $discipline_grades->course->currentTranslation->display_name }}
           </td>
           @foreach ($discipline_grades->course->classes as $item)
                @if ($item->year == 1)
                    @if (substr($item->code, -2) == "M1")
                        @php $manha = $item->vacancies; @endphp
                        <td> Manhã - {{$item->vacancies}}</td>

                    @elseif(substr($item->code, -2) == "T1")
                        @php $tarde = $item->vacancies; @endphp
                        <td> Tarde - {{$item->vacancies}}</td>

                    @elseif(substr($item->code, -2) == "N1")
                        @php $noite = $item->vacancies; @endphp
                        <td> Noite - {{$item->vacancies}}</td>
                    @endif
                    @else
                @endif
        @endforeach

            @if (isset($manha) and isset($tarde) and isset($noite))
                <td>@php $soma = $manha+$tarde+$noite @endphp {{$soma}}</td>

            @elseif(isset($manha) and isset($tarde) and !isset($noite))
                <td></td>
                <td>@php $soma = $manha+$tarde @endphp {{$soma}}</td>

            @elseif(isset($manha) and !isset($tarde) and isset($noite))
                <td></td>
                <td>@php $soma = $manha+$noite @endphp {{$soma}}</td>

            @elseif(!isset($manha) and isset($tarde) and isset($noite))
                <td></td>
                <td>@php $soma = $tarde+$noite @endphp {{$soma}}</td>

             @elseif(!isset($manha) and !isset($tarde) and isset($noite))
                <td></td>
                <td></td>
                <td>@php $soma = $noite @endphp {{$soma}}</td>

            @elseif(isset($manha) and !isset($tarde) and !isset($noite))
                <td></td>
                <td></td>
                <td>@php $soma = $manha @endphp {{$soma}}</td>

            @elseif(!isset($manha) and isset($tarde) and !isset($noite))
                <td></td>
                <td></td>
                <td>@php $soma = $tarde @endphp {{$soma}}</td>
            @else 
                <td> </td>
            @endif

                
            
        </tr>
    </tbody>
</table>

<br>
<br>
<table id="example" class="display" style="width:100%">
    <thead class="thead-parameter-group">
        <tr>
            <th class="th-parameter-group" style="font-size: 8pt;">Nº de Ordem</th>
            <th class="th-parameter-group" style="font-size: 8pt;">Nº de Candidato</th>
            <th class="th-parameter-group" style="font-size: 8pt;">Nome Completo</th>
            <th class="th-parameter-group" style="font-size: 8pt;">Turno</th>
            <th class="th-parameter-group" style="font-size: 8pt;">Nota</th>
            <th class="th-parameter-group" style="font-size: 8pt;">Estado</th>
        </tr>
    </thead>
    <tbody>
       
        @php $aprovados = 0 @endphp
        @php $estados = $estado->grades @endphp
        @php $inscritos_manha = 0 @endphp
        @php $inscritos_tarde = 0 @endphp
        @php $inscritos_noite = 0 @endphp
        @php $inscritos_sem_turno = 0 @endphp
        @php $inscritos_manha_admitidos = 0 @endphp
        @php $inscritos_tarde_admitidos = 0 @endphp
        @php $inscritos_noite_admitidos = 0 @endphp
        @php $inscritos_s_turno_admitidos = 0 @endphp
        @php $suplentes_manha = 0 @endphp
        @php $suplentes_tarde = 0 @endphp
        @php $suplentes_noite = 0 @endphp
        @php $suplentes_s_turno = 0 @endphp
        @php $n_admitido_manha = 0 @endphp
        @php $n_admitido_tarde = 0 @endphp
        @php $n_admitido_noite = 0 @endphp
        @php $n_admitido_s_turno = 0 @endphp


        @foreach ($discipline_grades->grades as $grades)
           <tr>
            <td>
                
            </td>

                {{--@foreach($grades->student->parameters as $parameter)
                    @if ($parameter->id === 19)
                        <td>{{$parameter->pivot->value}}</td>
                    @endif
                @endforeach--}}
                <td>{{$grades->student->candidate->code}}</td>

                @foreach($grades->student->parameters as $parameter)
                    @if ($parameter->id === 1)
                        <td style="text-align:left; padding-left:20px;">{{$parameter->pivot->value}}</td>
                    @endif
                @endforeach
               

                {{-- <td>{{$grades->student->name}}</td>--}}

                @forelse($grades->student->classes as $classes)
                
                @if(substr($classes->display_name, -2) == "M1") 
                    <td> Manhã </td>
                    @php $inscritos_manha++ @endphp
                @elseif(substr($classes->display_name, -2) == "T1")
                    <td> Tarde </td>
                    @php $inscritos_tarde++ @endphp
                @elseif(substr($classes->display_name, -2) == "N1")
                    <td> Noite </td>
                    @php $inscritos_noite++ @endphp
                    {{-- <td> {{$classes->display_name}} </td>--}}
                @endif

                @empty
                    @php $inscritos_sem_turno++ @endphp
                    <td></td>
                @endforelse     

                <td>{{ round($grades->value) }}</td>

                @if (round($grades->value) >= 10)
                   {{--@php $aprovados++ @endphp
                    @if($aprovados > 142)
                        <td>{{$aprovados}}</td>
                    @endif--}}
                   
                    @php $candidato = $grades->student->candidate->code; @endphp
                    @php $turno_candidato = "M1"; @endphp

                    @forelse ($grades->student->classes as $classes)
                       @php $turno_candidato = substr($classes->display_name, -2); @endphp
                            @break
                        @empty
                    @endforelse

                    @php $i = 1; @endphp 
                    @php $ordem_manha = 0; @endphp
                    @php $ordem_tarde = 0; @endphp
                    @php $ordem_noite = 0; @endphp
                    @foreach ($estado->grades as $estado_grades)
                        

                        @if($candidato == $estado_grades->student->candidate->code && $turno_candidato == "M1" && $ordem_manha <= $manha - 1)

                            <td>Admitido</td>
                            @php $inscritos_manha_admitidos++ @endphp
                        @elseif($candidato == $estado_grades->student->candidate->code && $turno_candidato == "M1" && $ordem_manha >= $manha)
                            <td>Suplente</td>
                            @php $suplentes_manha++ @endphp
                        @endif  
                        
                        @if($candidato == $estado_grades->student->candidate->code && $turno_candidato == "T1" && $ordem_tarde <= $tarde -1 )
                            <td>Admitido</td>
                            @php $inscritos_tarde_admitidos++ @endphp
                        @elseif($candidato == $estado_grades->student->candidate->code && $turno_candidato == "T1" && $ordem_tarde >= $tarde)
                            <td>Suplente</td>
                            @php $suplentes_tarde++ @endphp
                        @endif

                        @if($candidato == $estado_grades->student->candidate->code && $turno_candidato == "N1" && $ordem_noite <= $noite - 1)
                            <td>Admitido</td>
                            @php $inscritos_noite_admitidos++ @endphp
                        @elseif($candidato == $estado_grades->student->candidate->code && $turno_candidato == "N1" && $ordem_noite >= $noite)
                            <td>Suplente</td>
                            @php $suplentes_noite++ @endphp
                        @endif

                        @forelse ($estado_grades->student->classes as $classes)
                            @if(substr($classes->display_name, -2) == "M1")
                                @php $ordem_manha++ @endphp
                            @endif
                            @if(substr($classes->display_name, -2) == "T1")
                                @php $ordem_tarde++ @endphp
                            @endif
                            @if(substr($classes->display_name, -2) == "N1")
                                @php $ordem_noite++ @endphp
                            @endif
                        @empty
                            <td></td>
                        @endforelse

                    @endforeach

                @elseif(round($grades->value) < 10)
                    <td>Não Admitido </td>

                    @forelse($grades->student->classes as $classes)
                        @if(substr($classes->display_name, -2) == "M1") 
                            @php $n_admitido_manha++ @endphp
                        @elseif(substr($classes->display_name, -2) == "T1")
                            @php $n_admitido_tarde++ @endphp
                        @elseif(substr($classes->display_name, -2) == "N1")
                            @php $n_admitido_noite++ @endphp
                        @endif

                        @empty
                           @php $n_admitido_s_turno++ @endphp
                        @endforelse 
                @endif
            </tr>
         @endforeach
           {{--@for ($i = 1; $i <= count($discipline_grades->grades); $i++)
                <td>{{$i}}</td>
           @endfor
            @foreach ($discipline_grades->grades as $item_student_number)   
                    @foreach($item_student_number->student->parameters as $parameter)
                        @if ($parameter->id === 19)
                            <td>{{$parameter->pivot->value}}</td>
                        @endif
                     @endforeach 
            @endforeach
            @foreach ($discipline_grades->grades as $item_student_number)   
                    @foreach($item_student_number->student->classes as $classes)
                        <td> {{$classes->display_name}}</td>
                     @endforeach 
            @endforeach
            @foreach ($discipline_grades->grades as $item_student_name)
                <td>{{$item_student_name->student->name}}</td>
            @endforeach
            @foreach ($discipline_grades->grades as $item_grade)
                <td>{{$item_grade->value}}</td>
            @endforeach
            <td></td>--}}
      
    </tbody>
</table>
    <br>
{{--<table class="table table-parameter-group" align="right">
    <thead class="">
    <tr>
        <th>ss</th>
        <th style="font-size: 8pt; border: 0 !important;">Comissão de Acesso</th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td colspan="5">sss</td>
        <td>sss</td>
        <td style="font-size: 10pt;"><br>_________________________________________________________________</td>
    </tr>
    </tbody>
</table>--}}

<div style="float:left; position:relative; text-align:center;">
    <table class="table table-bordered">
        <thead>
            <tr>
                <td style="border-left-color:#fff;border-top-color:#fff;border-right-color:#fff;"></td>
                <td style="border-top-color:#fff;"></td>
                <th colspan="4" style="font-size: 10pt; text-align: center;">Candidatos</th>
            </tr>
            <tr>
                <th style="font-size: 10pt;">Turno</th>
                <th style="font-size: 10pt;">Vagas</th>
                <th style="font-size: 10pt;">Exames</th>
                <th style="font-size: 10pt;">Admitidos</th>
                <th style="font-size: 10pt;">Suplentes</th>
                <th style="font-size: 10pt;">Não Admitidos</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Manhã</td>
                <td>@if(isset($manha)){{$manha}}@endif</td>
                <td>@if(isset($inscritos_manha)) {{$inscritos_manha}} @endif</td>
                <td>@if(isset($inscritos_manha_admitidos)) {{$inscritos_manha_admitidos}}@endif</td>
                <td>@if(isset($suplentes_manha)) {{$suplentes_manha}}@endif</td>
                <td>@if(isset($n_admitido_manha)) {{$n_admitido_manha}}@endif</td>

            </tr>
            <tr>
                <td>Tarde</td>
                <td>@if(isset($tarde)){{$tarde}}@endif</td>
                <td>@if(isset($inscritos_tarde)) {{$inscritos_tarde}} @endif</td>
                <td>@if(isset($inscritos_tarde_admitidos)) {{$inscritos_tarde_admitidos}}@endif</td>
                <td>@if(isset($suplentes_tarde)) {{$suplentes_tarde}}@endif</td>
                <td>@if(isset($n_admitido_tarde)) {{$n_admitido_tarde}}@endif</td>
            </tr>
            <tr>
                <td>Noite</td>
                <td>@if(isset($noite)){{$noite}}@endif</td>
                <td>@if(isset($inscritos_noite)) {{$inscritos_noite}} @endif</td>
                <td>@if(isset($inscritos_noite_admitidos)) {{$inscritos_noite_admitidos}}@endif</td>
                <td>@if(isset($suplentes_noite)) {{$suplentes_noite}}@endif</td>
                <td>@if(isset($n_admitido_noite)) {{$n_admitido_noite}}@endif</td>
            </tr>
            <tr>
                <td>S/Turno</td>
                <td>0</td>
                <td>@if(isset($inscritos_sem_turno)){{$inscritos_sem_turno}} @endif</td>
                <td>@if(isset($inscritos_s_turno_admitidos)) {{$inscritos_s_turno_admitidos}}@endif</td>
                <td>@if(isset($suplentes_s_turno)) {{$suplentes_s_turno}}@endif</td>
                <td>@if(isset($n_admitido_s_turno)) {{$n_admitido_s_turno}}@endif</td>
            </tr>

             <tr>
                <td>Totais</td>
                
                    @if(isset($manha) and isset($tarde) and isset($noite)) 
                        @php $soma = $manha+$tarde+$noite; @endphp <td>{{$soma}}</td>

                    @elseif(isset($manha) and !isset($tarde) and !isset($noite))
                         @php $soma = $manha; @endphp <td>{{$soma}}</td>

                    @elseif(!isset($manha) and isset($tarde) and isset($noite))
                         @php $soma = $tarde+$noite; @endphp <td>{{$soma}}</td>

                    @elseif(!isset($manha) and isset($tarde) and !isset($noite))
                         @php $soma = $tarde; @endphp <td>{{$soma}}</td>

                    @elseif(isset($manha) and isset($tarde) and !isset($noite))
                         @php $soma = $manha+$tarde @endphp <td>{{$soma}}</td>

                    @elseif(!isset($manha) and !isset($tarde) and isset($noite))
                         @php $soma = $noite; @endphp <td>{{$soma}}</td>

                    @elseif(!isset($manha) and isset($tarde) and isset($noite))
                         @php $soma = $noite+$tarde; @endphp <td>{{$soma}}</td>

                    @endif
                
              
                     @if(isset($inscritos_manha) and isset($inscritos_tarde) and isset($inscritos_noite) and isset($inscritos_sem_turno)) 
                        @php $soma = $inscritos_manha+$inscritos_tarde+$inscritos_noite+$inscritos_sem_turno; @endphp <td>{{$soma}}</td>

                    @elseif(isset($inscritos_manha) and isset($inscritos_tarde) and isset($inscritos_noite)) 
                        @php $soma = $inscritos_manha+$inscritos_tarde+$inscritos_noite; @endphp <td>{{$soma}}</td>

                    @elseif(isset($inscritos_manha) and !isset($inscritos_tarde) and !isset($inscritos_noite))
                         @php $soma = $inscritos_manha; @endphp <td>{{$soma}}</td>

                    @elseif(!isset($inscritos_manha) and isset($inscritos_tarde) and isset($inscritos_noite))
                         @php $soma = $inscritos_tarde+$inscritos_noite; @endphp <td>{{$soma}}</td>

                    @elseif(!isset($inscritos_manha) and isset($inscritos_tarde) and !isset($inscritos_noite))
                         @php $soma = $inscritos_tarde; @endphp <td>{{$soma}}</td>

                    @elseif(isset($inscritos_manha) and isset($inscritos_tarde) and !isset($inscritos_noite))
                         @php $soma = $inscritos_manha+$inscritos_tarde @endphp <td>{{$soma}}</td>

                    @elseif(!isset($inscritos_manha) and !isset($inscritos_tarde) and isset($inscritos_noite))
                         @php $soma = $inscritos_noite; @endphp <td>{{$soma}}</td>

                    @elseif(!isset($inscritos_manha) and isset($inscritos_tarde) and isset($inscritos_noite))
                         @php $soma = $inscritos_noite+$inscritos_tarde; @endphp <td>{{$soma}}</td>
                    
                    @elseif(!isset($inscritos_manha) and isset($inscritos_tarde) and isset($inscritos_noite))
                         @php $soma = $inscritos_noite+$inscritos_tarde; @endphp <td>{{$soma}}</td>

                    @endif
              
                    
                     @if(isset($inscritos_manha_admitidos) and isset($inscritos_tarde_admitidos) and isset($inscritos_noite_admitidos) and isset($inscritos_s_turno_admitidos)) 
                        @php $soma = $inscritos_manha_admitidos+$inscritos_tarde_admitidos+$inscritos_noite_admitidos+$inscritos_s_turno_admitidos; @endphp <td>{{$soma}}</td>

                    @elseif(isset($inscritos_manha_admitidos) and !isset($inscritos_tarde_admitidos) and !isset($inscritos_noite_admitidos))
                         @php $soma = $inscritos_manha_admitidos; @endphp <td>{{$soma}}</td>

                    @elseif(!isset($inscritos_manha_admitidos) and isset($inscritos_tarde_admitidos) and isset($inscritos_noite_admitidos))
                         @php $soma = $inscritos_tarde_admitidos+$inscritos_noite_admitidos; @endphp <td>{{$soma}}</td>

                    @elseif(!isset($inscritos_manha_admitidos) and isset($inscritos_tarde_admitidos) and !isset($inscritos_noite_admitidos))
                         @php $soma = $inscritos_tarde_admitidos; @endphp <td>{{$soma}}</td>

                    @elseif(isset($inscritos_manha_admitidos) and isset($inscritos_tarde_admitidos) and !isset($inscritos_noite_admitidos))
                         @php $soma = $inscritos_manha_admitidos+$inscritos_tarde_admitidos @endphp <td>{{$soma}}</td>

                    @elseif(!isset($inscritos_manha_admitidos) and !isset($inscritos_tarde_admitidos) and isset($inscritos_noite_admitidos))
                         @php $soma = $inscritos_noite_admitidos; @endphp <td>{{$soma}}</td>

                    @elseif(!isset($inscritos_manha_admitidos) and isset($inscritos_tarde_admitidos) and isset($inscritos_noite_admitidos))
                         @php $soma = $inscritos_noite_admitidos+$inscritos_tarde_admitidos; @endphp <td>{{$soma}}</td>

                    @endif
                
                     @if(isset($suplentes_manha) and isset($suplentes_tarde) and isset($suplentes_noite) and isset($suplentes_s_turno)) 
                        @php $soma = $suplentes_manha+$suplentes_tarde+$suplentes_noite+$suplentes_s_turno; @endphp <td>{{$soma}}</td>

                    @elseif(isset($suplentes_manha) and !isset($suplentes_tarde) and !isset($suplentes_noite))
                         @php $soma = $suplentes_manha; @endphp <td>{{$soma}}</td>

                    @elseif(!isset($suplentes_manha) and isset($suplentes_tarde) and isset($suplentes_noite))
                         @php $soma = $suplentes_tarde+$suplentes_noite; @endphp <td>{{$soma}}</td>

                    @elseif(!isset($suplentes_manha) and isset($suplentes_tarde) and !isset($suplentes_noite))
                         @php $soma = $suplentes_tarde; @endphp <td>{{$soma}}</td>

                    @elseif(isset($suplentes_manha) and isset($suplentes_tarde) and !isset($suplentes_noite))
                         @php $soma = $inscritos_manha_admitidos+$suplentes_tarde @endphp <td>{{$soma}}</td>

                    @elseif(!isset($suplentes_manha) and !isset($suplentes_tarde) and isset($suplentes_noite))
                         @php $soma = $inscritos_noite_admitidos; @endphp <td>{{$soma}}</td>

                    @elseif(!isset($suplentes_manha) and isset($suplentes_tarde) and isset($suplentes_noite))
                         @php $soma = $inscritos_noite_admitidos+$suplentes_tarde; @endphp <td>{{$soma}}</td>

                    @endif

                     @if(isset($n_admitido_manha) and isset($n_admitido_tarde) and isset($n_admitido_noite) and isset($n_admitido_s_turno)) 
                        @php $soma = $n_admitido_manha+$n_admitido_tarde+$n_admitido_noite+$n_admitido_s_turno; @endphp <td>{{$soma}}</td>

                    @elseif(isset($n_admitido_manha) and !isset($n_admitido_tarde) and !isset($n_admitido_noite))
                         @php $soma = $n_admitido_manha; @endphp <td>{{$soma}}</td>

                    @elseif(!isset($n_admitido_manha) and isset($n_admitido_tarde) and isset($n_admitido_noite))
                         @php $soma = $n_admitido_tarde+$n_admitido_noite; @endphp <td>{{$soma}}</td>

                    @elseif(!isset($n_admitido_manha) and isset($n_admitido_tarde) and !isset($n_admitido_noite))
                         @php $soma = $n_admitido_tarde; @endphp <td>{{$soma}}</td>

                    @elseif(isset($n_admitido_manha) and isset($n_admitido_tarde) and !isset($n_admitido_noite))
                         @php $soma = $inscritos_manha_admitidos+$n_admitido_tarde @endphp <td>{{$soma}}</td>

                    @elseif(!isset($n_admitido_manha) and !isset($n_admitido_tarde) and isset($n_admitido_noite))
                         @php $soma = $inscritos_noite_admitidos; @endphp <td>{{$soma}}</td>

                    @elseif(!isset($n_admitido_manha) and isset($n_admitido_tarde) and isset($n_admitido_noite))
                         @php $soma = $inscritos_noite_admitidos+$n_admitido_tarde; @endphp <td>{{$soma}}</td>

                    @endif
            </tr>
        </tbody>
    </table>
</div>
<div style="float:right; position:relative; text-align:center;">
    <p>A comissão de acesso</p>
    <span>__________________________________</span>
</div>
<br>

<br>


<script>
    let discipline_grades = {!! $discipline_grades !!};
    console.log(discipline_grades);

    let estado = {!! $estado !!};
    console.log(estado);
</script>