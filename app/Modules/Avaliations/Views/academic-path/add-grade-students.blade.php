<title>Avaliações | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'ATRIBUIR NOTAS')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('panel_avaliation') }}">Avaliações</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('old_student.index') }}">Lancar notas por transisão</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Atribuir notas</li>
@endsection
@section('styles-new')
    @parent
    <link rel="stylesheet" href="{{ asset('css/new_table_panel.css') }}" />
@endsection
@section('body')
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nº de matrícula</th>
                <th>Nome completo</th>
                <th>Email</th>
                <th>Curso</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $matriculationCode }}</td>
                <td>{{ $personalName }}</td>
                <td>{{ $studentInfo->email }}</td>

                <td>
                    @foreach ($studentInfo->courses as $course)
                        @php $course_id = $course->id; @endphp
                        {{ $course->currentTranslation->display_name }}
                    @endforeach
                </td>
            </tr>
        </tbody>
    </table>
    <hr>
    @php $matriculedYear = ($getDisciplinesMatriculed->year == 3 ? 4 : 3); @endphp
    @php
        $flag = true;
        $anoLectivo = ['2012','2013','2014','2015','2016',2017,'2018','2019','20 / 21','21 / 22', '22 / 23', '23 / 24'];
    @endphp
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    {!! Form::open(['route' => ['old_student.store']]) !!}
                    @csrf
                    <input type="hidden" name="user_id" value="{{ $studentInfo->id }}">
                    {{-- caso seja estudante transferido --}}
                    @if (!$state->isEmpty() && $state->first()->state_id == 14)
                        <div class="col-4">
                            <span>Nome da instituição de ensino superior de proveniência</span>
                            <input type="text" class="form-control" name="home_institution"
                                value="{{ $home_institution[0]->home_institution ?? '' }}" required>
                        </div>
                        <br>
                    @endif
                    <table id="students-table" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Ano</th>
                                <th>Código da disciplina</th>
                                <th>Disciplina</th>
                                <th>Ano lectivo</th>
                                <th>Nota</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($disciplines as $discipline)
                                @php $flag = true; @endphp
                                <tr>
                                    {{-- ano curricular da disciplina --}}
                                    @if (
                                        $course_id == 25 &&
                                            $discipline->years == $getDisciplinesMatriculed->year &&
                                            substr($discipline->code, 0, -4) != substr($getDisciplinesMatriculed->discipline_code, 0, -4))
                                    @elseif (
                                        $course_id == 25 &&
                                            $discipline->years == $matriculedYear &&
                                            substr($discipline->code, 0, -4) != substr($getDisciplinesMatriculed->discipline_code, 0, -4))
                                    @else
                                        <td> {{ $discipline->years }} </td>
                                    @endif

                                    {{-- codigo da disciplina --}}
                                    @if (
                                        $course_id == 25 &&
                                            $discipline->years == $getDisciplinesMatriculed->year &&
                                            substr($discipline->code, 0, -4) != substr($getDisciplinesMatriculed->discipline_code, 0, -4))
                                    @elseif (
                                        $course_id == 25 &&
                                            $discipline->years == $matriculedYear &&
                                            substr($discipline->code, 0, -4) != substr($getDisciplinesMatriculed->discipline_code, 0, -4))
                                    @else
                                        <td>
                                            {{ $discipline->code }}
                                        </td>
                                    @endif
                                    {{-- nome da disciplina --}}
                                    @if (
                                        $course_id == 25 &&
                                            $discipline->years == $getDisciplinesMatriculed->year &&
                                            substr($discipline->code, 0, -4) != substr($getDisciplinesMatriculed->discipline_code, 0, -4))
                                    @elseif (
                                        $course_id == 25 &&
                                            $discipline->years == $matriculedYear &&
                                            substr($discipline->code, 0, -4) != substr($getDisciplinesMatriculed->discipline_code, 0, -4))
                                    @else
                                        <td>
                                            {{ $discipline->display_name }}
                                            <input 
                                                type="hidden" name="discipline_id[]"
                                                required
                                                value="{{ $discipline->disciplines_id }}"
                                            >
                                        </td>
                                    @endif

                                    {{-- selecao do ano letivo --}}
                                    @if (
                                        $course_id == 25 &&
                                            $discipline->years == $getDisciplinesMatriculed->year &&
                                            substr($discipline->code, 0, -4) != substr($getDisciplinesMatriculed->discipline_code, 0, -4))
                                    @elseif (
                                        $course_id == 25 &&
                                            $discipline->years == $matriculedYear &&
                                            substr($discipline->code, 0, -4) != substr($getDisciplinesMatriculed->discipline_code, 0, -4))
                                    @else
                                         @foreach ($anoLectivo as $ano)
                                            @php
                                                $flag_year_option = false;
                                                $array_to_search[] = $ano;
                                            @endphp
                                        @endforeach
                                        <td>
                                            <select name="lective_year[]" class="form-control w-auto" required>
                                               
                                                @if ($grades->isNotEmpty())
                                                    @for ($i = 0; $i < count($grades); $i++)
                                                        @if ($discipline->disciplines_id == $grades[$i]->discipline_id)
                                                            @if (!in_array($grades[$i]->lective_year, $array_to_search))
                                                                @php $flag_year_option=true; @endphp
                                                                <option value="{{ $grades[$i]->lective_year }}" selected>
                                                                    {{ $grades[$i]->lective_year }} 
                                                                </option>
                                                            @else
                                                                <option value="{{ $grades[$i]->lective_year }}" selected>
                                                                    {{ $grades[$i]->lective_year }} 
                                                                </option>
                                                            @endif
                                                        @endif
                                                    @endfor 

                                                    @foreach ($anoLectivo as $ano)
                                                        @if (!$flag_year_option)
                                                            @if ($getDisciplinesMatriculed->year == $discipline->years)
                                                                <option value="{{ $ano }}">{{ $ano }} 
                                                                </option>
                                                            @else
                                                                <option value="{{ $ano }}">{{ $ano }} 
                                                                </option>
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                @else
                                                    @foreach ($anoLectivo as $ano)
                                                        {{-- sse for uma ano 21/22 --}}
                                                        @if (!$flag_year_option)
                                                            @if ($getDisciplinesMatriculed->year == $discipline->years)
                                                                <option value="{{ $ano }}">{{ $ano }} 
                                                                </option>
                                                            @else
                                                                <option value="{{ $ano }}">{{ $ano }} 
                                                                </option>
                                                            @endif
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </select>
                                        </td>
                                    @endif
                                    {{-- bloco central --}}

                                    {{-- input para selecao de notas --}}
                                    @if (
                                        $course_id == 25 &&
                                            $discipline->years == $getDisciplinesMatriculed->year &&
                                            substr($discipline->code, 0, -4) != substr($getDisciplinesMatriculed->discipline_code, 0, -4))
                                    @elseif (
                                        $course_id == 25 &&
                                            $discipline->years == $matriculedYear &&
                                            substr($discipline->code, 0, -4) != substr($getDisciplinesMatriculed->discipline_code, 0, -4))
                                    @else
                                        @php $flag = true; @endphp
                                        <td>
                                            @for ($i = 0; $i < count($grades); $i++)
                                                @if ($discipline->disciplines_id == $grades[$i]->discipline_id)
                                                    @php $flag = false; @endphp
                                                    @if (!in_array($grades[$i]->lective_year, $array_to_search))
                                                        {{--Quando o ano for aquele que não pode entrar por transição de notas--exemplo:21/22--}}
                                                        <label for="">Esta nota não pode ser editada via transição</label>
                                                        <input type="number" name="grade[]" class="form-control w-auto"
                                                            min="0" max="20" value="{{ $grades[$i]->grade }}"
                                                            readonly >
                                                    @else
                                                        <input type="number" name="grade[]" class="form-control w-auto"
                                                            min="0" max="20"
                                                            value="{{ $grades[$i]->grade }}" />
                                                    @endif
                                                @endif
                                            @endfor
                                            @if ($flag)
                                                <input type="number" name="grade[]" class="form-control w-auto"
                                                    min="0" max="20" value="" />
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
@endsection
@section('models')
    @include('layouts.backoffice.modal_confirm')
@endsection
@section('scripts-new')
    @parent
    <script>
        Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');
    </script>
@endsection
