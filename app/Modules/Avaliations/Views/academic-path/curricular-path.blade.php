<title>Avaliações | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'Histórico acadêmico')
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
    <li class="breadcrumb-item">
        <a href="{{ route('old_student.add', $studentInfo->id) }}">Atribuir notas</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Histórico acadêmico</li>
@endsection
@section('styles-new')
    @parent
    <link rel="stylesheet" href="{{ asset('css/new_table_panel.css') }}" />
@endsection
@section('body')
    <div class="row">
        <div class="col-12">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th class="text-center">Nº de matrícula</th>
                        <th class="text-center">Nome completo</th>
                        <th class="text-center">Email</th>
                        <th class="text-center">Número</th>
                        <th class="text-center">Curso</th>
                        <th class="text-center">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center">{{ $studentInfo->matriculation->code }}</td>
                        <td class="pl-3">{{ $personalName }}</td>
                        <td class="text-center">{{ $studentInfo->email }}</td>
                        <td class="text-center">{{ $matriculationCode }}</td>
                        <td class="text-center">
                            @foreach ($studentInfo->courses as $course)
                                {{ $course->currentTranslation->display_name }}
                            @endforeach
                        </td>
                        <td class="text-center"> --- </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="col-12">
            <table class="table table-bordered table-hover">
                <thead>
                    <th class="text-center">Ano</th>
                    <th class="text-center">Código</th>
                    <th class="text-center">Disciplina</th>
                    <th class="text-center ">Ano lectivo</th>
                    <th class="text-center ">Nota</th>
                </thead>
                <tbody>
                    @foreach ($disciplines as $discipline)
                        <tr>
                            <td class="text-center">{{ $discipline->year }} º</td>
                            <td class="text-center">{{ $discipline->code }}</td>
                            <td class="pl-3">{{ $discipline->name }}</td>
                            <td class="text-center ">{{ $discipline->lective_year }}</td>
                            <td class="text-center ">{{ $discipline->grade }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="float-right">
        <a href="{{ route('old_student.print', $studentInfo->id) }}" class="btn btn-primary" target="_blank">
            <i class="fas fa-print"></i>
            Imprimir
        </a>
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
