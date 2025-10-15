 @foreach ($user->parameters as $user_parameters)
       @if ($user_parameters->id === 1)
             <p style="">Estudante: <b>{{$user_parameters->pivot->value}}</b></p>
       @endif
    @endforeach
    @foreach ($user->parameters as $user_parameters)
       @if ($user_parameters->id === 19)
             <p style="">Nº de matrícula: <b>{{$user_parameters->pivot->value}}</b></p>
       @endif
    @endforeach
<table class="table table-parameter-group" border="1" width="100%" style="text-align:center;">
    <thead class="thead-parameter-group">
        <tr>
            <th class="th-parameter-group" style="font-size: 8pt; text-align: left; ">Licenciado em</th>

            <th class="th-parameter-group" style="font-size: 8pt; background-color:#fff; border-color:#fff;">Média de Entrada</th>
             <th class="th-parameter-group" style="font-size: 8pt;">Média Final</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            @foreach ($user->courses as $user_course)
                <td width="40%" style="font-size: 8pt; text-align: left;border-right:0;">{{$user_course->currentTranslation->display_name}}</td>
            @endforeach 
            <td style="background-color:#fff; border-color:#fff;"></td>
            <td style="font-size: 8pt border-left:2px; border-color:red;"></td>
        </tr>
    </tbody>
</table>


<table class="table table-parameter-group" border="1px" width="100%" style= "text-align:center;">
    <thead class="thead-parameter-group">
        <tr>
            <th class="th-parameter-group" style="font-size: 8pt;">Ano</th>
            <th class="th-parameter-group" style="font-size: 8pt;">Semestre</th>
            <th class="th-parameter-group" style="font-size: 8pt;">Unidade Curricular</th>
            <th class="th-parameter-group" style="font-size: 8pt;">Nota</th>
            <th class="th-parameter-group" style="font-size: 8pt;">Matriculado(a)</th>
        </tr>
    </thead>
    <tbody>
       <tr>
            @foreach($user->courses as $user_course)
                @foreach ($user_course->disciplines as $user_discipline)
                    @foreach ($user_discipline->study_plans_has_disciplines as $user_studyplan_discipline)
                       <tr>
                            <td width="5%" style="font-size: 8pt;" class="td-repo">
                                 {{$user_studyplan_discipline->years}}                 
                            </td>
                            <td width="15%" style="font-size: 8pt; text-align:center;">{{$user_studyplan_discipline->discipline_period->currentTranslation->display_name}}</td>
                            <td width="40%" style="font-size: 8pt;  text-align:left; padding-left:15px">{{$user_discipline->currentTranslation->display_name}} &nbsp;&nbsp;&nbsp;</td>
                            <td width="5%" style="font-size: 8pt;">
                                @foreach ($user->matriculation->disciplines as $user_matriculation_discipline)
                                    @if($user_discipline->id == $user_matriculation_discipline->id)
                                        @foreach ($user_matriculation_discipline->grades as $user_matriculation_discipline_grades)
                                            {{-- @if ($user_matriculation_discipline_grades->student_id == Auth::user()->id) --}}
                                            {{$user_matriculation_discipline_grades->value}}
                                            {{-- @endif   --}} 
                                    @endforeach
                                    @endif 
                               @endforeach
                            </td>
                            <td width="5%" style="font-size: 8pt;">
                               @foreach ($user->matriculation->disciplines as $user_matriculation_discipline)
                                    @if($user_discipline->id == $user_matriculation_discipline->id)
                                        ✔
                                    @endif
                               @endforeach 
                            </td>
                        </tr>
                    @endforeach
                @endforeach
            @endforeach
        </tr>
    </tbody>
</table>

<script>
    let user = {!! $user !!};
    console.log(user);
 
    
    
</script>