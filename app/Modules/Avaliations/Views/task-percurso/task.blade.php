<title>Avaliações | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'ELIMINAR NOTAS')
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
    <li class="breadcrumb-item active" aria-current="page">Eliminar Notas</li>
@endsection
@section('body')
    <div class="row">
        <div class="col-6 p-2">
            <label>Estudante</label>
            <select data-live-search="true" required class="selectpicker form-control form-control-sm" required=""
                id="Curso_id_Select" data-actions-box="true" data-selected-text-format="values" name="id_curso"
                tabindex="-98" disabled>
                <option value="{{ $estudante }}" selected>
                    {{ $estudante }}
                </option>
            </select>
        </div>
    </div>
    {!! Form::open(['route' => ['percurso_task.delete'], 'method' => 'post', 'target' => '_blank']) !!}
    <input type="number" id="estudante" name="estudante" value="{{ $codigo }}" class="d-none" />
    <table id="request-table" class="table table-striped table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Código</th>
                <th>Disciplina</th>
                <th>Nota</th>
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
                    @foreach ($notas as $item_notas)
                        @if ($item_notas->discipline_id == $item->id_disciplina)
                            <td> {{ round($item_notas->grade, 0) }} </td>
                            <td>
                                <input type="checkbox" class="disciplina_check" name="disciplina_check[]"
                                    value="{{ $item_notas->discipline_id }}" />
                            </td>
                        @endif
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
    <button type="submit"tabindex="0" data-bs-toggle="tooltip" data-html="true" target="_blank" href="percurso_task/show"
        class="btn btn-danger btn-sm float-right w-max rounded">
        @icon('fas fa-trash-alt') Eliminar notas
    </button>
    {!! Form::close() !!}
@endsection
@section('scripts-new')
    @parent
    <script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <script>
        Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');

        function pegar_estudantes() {
            var AnoDataTable = $('#request-table').DataTable({
                buttons: [
                    'colvis',
                    'excel'
                ],
                "lengthMenu": [
                    [10, 50, 100, 50000],
                    [10, 50, 100, "Todos"]
                ],
                language: {
                    url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}'
                }
            });
        }
        var check = "checado";
        $("#all_check").click(function() {
            if (check == "checado") {
                $(".disciplina_check").attr("checked", true);
                check = "nada";
            } else {
                $(".disciplina_check").attr("checked", false);
                check = "checado";
            }
        });
    </script>
@endsection
