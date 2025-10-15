<title>Avaliações | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'RESTAURAR NOTAS')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('panel_avaliation') }}">Avaliações</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('percurso_task.index') }}">Percurso duplicado</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Restaurar notas</li>
@endsection
@section('body')
    <div class="row">
        <div class="col-12">
            {!! Form::open(['route' => ['percurso_task.restaurar'], 'method' => 'post', 'target' => '_blank']) !!}
            <input type="number" id="estudante" name="estudante" value="{{ $disciplina[0]->codigo }}" class="d-none" />
            <table id="request-table" class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Código</th>
                        <th>Disciplina</th>
                        <th>Nota</th>
                        <th>Eliminado por</th>
                        <th><input type="checkbox" id="all_check" /></th>
                    </tr>
                </thead>
                <tbody>
                    @php $i = 1; @endphp
                    @foreach ($disciplina as $item)
                        <tr>
                            <td>{{ $i++ }}</td>
                            <td>#{{ $item->code_disciplina }}</td>
                            <td>{{ $item->nome_disciplina }}</td>
                            <td>{{ round($item->grade, 0) }} </td>
                            <td>{{ $item->funcionario }}</td>
                            <td><input type="checkbox" class="disciplina_check" name="disciplina_check[]"
                                    value="{{ $item->discipline_id }}" />
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="col-12">
            <button type="submit" tabindex="0" data-bs-toggle="tooltip" data-html="true" target="_blank"
                class="btn btn-warning btn-sm float-right w-auto"> Restaurar
            </button>
        </div>
        {!! Form::close() !!}
    </div>
@endsection
