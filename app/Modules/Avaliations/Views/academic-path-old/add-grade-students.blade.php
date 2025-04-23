@section('title',__('Atribuir notas'))
@extends('layouts.backoffice')

@section('styles')
@parent
@endsection

@section('content')

<div class="content-panel">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Atribuir notas</h1>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <div class="float-right mt-4">
                        <a href="{{ route('old_student.index') }}" class="btn btn-secondary btn-sm mb-3">
                            Voltar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr>
    {{-- Main content --}}
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Nº de matrícula</th>
                                <th>Nome completo</th>
                                <th>Email</th>
                                <th>Código</th>
                                <th>Curso</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>{{ $matriculationCode}}</td>
                                <td>{{ $personalName }}</td>
                                <td>{{ $studentInfo->email }}</td>
                                <td>{{ $studentInfo->matriculation->code}}</td>
                                <td>
                                    @foreach ($studentInfo->courses as $course)
                                    @php $course_id = $course->id; @endphp
                                    {{$course->currentTranslation->display_name}}
                                    @endforeach
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <hr>
            @php $matriculedYear = ($getDisciplinesMatriculed->year == 3 ? 4 : 3); @endphp
            @php $flag = true; @endphp
            <div class="row">
                <div class="col">
                    <div class="card">
                        <div class="card-body">
                            {!! Form::open(['route' => ['old_student.store']]) !!}
                            @csrf
                            <input type="hidden" name="user_id" value="{{$studentInfo->id}}">

                            {{-- caso seja estudante transferido--}}
                            @if(!$state->isEmpty() && $state->first()->state_id == 14)
                            <div class="col-4">
                                <span>Nome da instituição de ensino superior de proveniência</span>
                            <input type="text" class="form-control" name="home_institution" value="{{ $home_institution[0]->home_institution ?? "" }}" required>
                            </div>
                                <br>
                            @endif

                            <table id="students-table" class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Ano</th>
                                        <th>Código</th>
                                        <th>Disciplina</th>
                                        <th>Ano letivo</th>
                                        <th>Nota</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($disciplines as $discipline)
                                     @php $flag = true; @endphp
                                    <tr>
                                        {{-- ano curricular da disciplina --}}
                                        @if ($course_id == 25 && $discipline->years == $getDisciplinesMatriculed->year && substr($discipline->code, 0, -4) != substr($getDisciplinesMatriculed->discipline_code, 0, -4))

                                        @elseif ($course_id == 25 && $discipline->years == $matriculedYear && substr($discipline->code, 0, -4) != substr($getDisciplinesMatriculed->discipline_code, 0, -4))

                                        @else
                                            <td> {{$discipline->years}} </td>
                                        @endif

                                        {{-- codigo da disciplina--}}
                                        @if ($course_id == 25 && $discipline->years == $getDisciplinesMatriculed->year && substr($discipline->code, 0, -4) != substr($getDisciplinesMatriculed->discipline_code, 0, -4))

                                        @elseif ($course_id == 25 && $discipline->years == $matriculedYear && substr($discipline->code, 0, -4) != substr($getDisciplinesMatriculed->discipline_code, 0, -4))

                                        @else
                                            <td>
                                                {{$discipline->code}}
                                            </td>
                                        @endif

                                        {{-- nome da disciplina--}}
                                        @if ($course_id == 25 && $discipline->years == $getDisciplinesMatriculed->year && substr($discipline->code, 0, -4) != substr($getDisciplinesMatriculed->discipline_code, 0, -4))

                                        @elseif ($course_id == 25 && $discipline->years == $matriculedYear && substr($discipline->code, 0, -4) != substr($getDisciplinesMatriculed->discipline_code, 0, -4))

                                        @else
                                            <td>
                                                {{$discipline->display_name}}
                                                <input type="hidden" name="discipline_id[]" required value="{{ $discipline->disciplines_id}}">
                                            </td>
                                        @endif

                                        {{-- selecao do ano letivo--}}
                                        @if ($course_id == 25 && $discipline->years == $getDisciplinesMatriculed->year && substr($discipline->code, 0, -4) != substr($getDisciplinesMatriculed->discipline_code, 0, -4))

                                        @elseif ($course_id == 25 && $discipline->years == $matriculedYear && substr($discipline->code, 0, -4) != substr($getDisciplinesMatriculed->discipline_code, 0, -4))

                                        @else
                                        @for ($i = 2013; $i <= 2020; $i++)
                                            @php
                                            $flag_year_option=false;
                                            $array_to_search[]=$i;
                                          @endphp
                                        @endfor
                                        

                                    
                                             <td>

                                                <select name="lective_year[]" class="form-control" required style="width:100px; height:30px;">
                                                    @if ($grades->isNotEmpty())
                                                      @for ($i = 0; $i < count($grades); $i++)
                                                         @if ($discipline->disciplines_id == $grades[$i]->discipline_id)
                                                    
                                                           @if (!in_array($grades[$i]->lective_year, $array_to_search))
                                                            @php $flag_year_option=true; @endphp
                                                             <option value="{{$grades[$i]->lective_year}}" selected > {{$grades[$i]->lective_year}}  
                                                             </option> 

                                                             @else
                                                             <option value="{{$grades[$i]->lective_year}}" selected > {{$grades[$i]->lective_year}}  
                                                            </option> 
                                                             @endif
                                                        @endif

                                                       @endfor
                                                         @for ($i = 2013; $i <= 2020; $i++)
                                                            {{-- sse for uma ano 21/22 --}}
                                                            @if (!$flag_year_option)
                                                                @if ($studentInfo->matriculation->course_year == $discipline->years)
                                                                <option value="{{$i}}" >{{$i}} </option>
                                                                @else
                                                                <option value="{{$i}}">{{$i}}</option>
                                                                @endif
                                                            @endif
                                                         @endfor
                                                        @else
                                                         @for ($i = 2013; $i <= 2020; $i++)
                                                            {{-- sse for uma ano 21/22 --}}
                                                            @if (!$flag_year_option)
                                                                @if ($studentInfo->matriculation->course_year == $discipline->years)
                                                                <option value="{{$i}}" >{{$i}} </option>
                                                                @else
                                                                <option value="{{$i}}">{{$i}}</option>
                                                                @endif
                                                            @endif
                                                         @endfor

                                                   
                                                     @endif
                                                        
                                              

                                                </select>
                                            </td>
                                        @endif
                                    {{-- bloco central --}}
                                    
                                   
                                 










                                        {{-- input para selecao de notas--}}
                                        @if ($course_id == 25 && $discipline->years == $getDisciplinesMatriculed->year && substr($discipline->code, 0, -4) != substr($getDisciplinesMatriculed->discipline_code, 0, -4))

                                        @elseif ($course_id == 25 && $discipline->years == $matriculedYear && substr($discipline->code, 0, -4) != substr($getDisciplinesMatriculed->discipline_code, 0, -4))

                                        @else
                                        @php $flag = true; @endphp
                                            {{-- {{dd($array_to_search)}} --}}
                                        <td>
                                        @for ($i = 0; $i < count($grades); $i++)

                                            @if ($discipline->disciplines_id == $grades[$i]->discipline_id )
                                            @php $flag = false; @endphp
                                              @if (!in_array($grades[$i]->lective_year, $array_to_search))
                                              {{-- Quando o ano for aquele que não pode entrar por transição de notas -- exemplo: 21/22    --}}
                                                    <label for="">Esta nota não pode ser editada via transição</label>
                                                    <input type="number" name="grade[]" class="form-control" style="width:100px; height:30px;" min="0" max="20" value="{{$grades[$i]->grade}}" readonly>
                                                 @else

                                                 <input type="number" name="grade[]" class="form-control" style="width:100px; height:30px;" min="0" max="20" value="{{$grades[$i]->grade}}">
                                                 @endif
                                                
                                            @endif
                                            @endfor
                                            @if ($flag)
                                                <input type="number" name="grade[]" class="form-control" style="width:100px; height:30px;" min="0" max="20" value="">
                                            @endif
                                        </td>



                                        @endif
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="float-right">

                                <button type="submit" class="btn btn-success btn-sm mb-3">
                                    Inserir notas
                                </button>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- modal confirm --}}
@include('layouts.backoffice.modal_confirm')


@endsection



@section('scripts')
@parent
<script>
    // Delete confirmation modal
    Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');

</script>
@endsection
