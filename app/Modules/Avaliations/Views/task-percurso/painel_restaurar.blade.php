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
    <li class="breadcrumb-item active" aria-current="page">Eliminar notas</li>
@endsection
@section('body')
    <div class="row">
        <div class="col-6 p-2">
            <label>Selecione o curso</label>
            <select data-live-search="true" required class="selectpicker form-control form-control-sm" required=""
                id="Curso_id_Select" data-actions-box="true" data-selected-text-format="values" name="id_curso"
                tabindex="-98">
                <option></option>
                @foreach ($courses as $item)
                    @if ($item->id == 11)
                        <option value="{{ $item->id }}" selected>{{ $item->currentTranslation['display_name'] }}
                        </option>
                    @else
                        <option value="{{ $item->id }}">{{ $item->currentTranslation['display_name'] }}</option>
                    @endif
                @endforeach
            </select>
        </div>
    </div>
    <table id="request-table" class="table table-striped table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Matrícula</th>
                <th>Estudante</th>
                <th>email</th>
                <th>Actividades</th>
            </tr>
        </thead>
    </table>
@endsection
@section('scripts')
    @parent
    <script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <script>
        Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');
        // Quando o tipo de avaliação for alterada
        function pegar_estudantes(curso) {
            var AnoDataTable = $('#request-table').DataTable({
                ajax: {
                    "url": "/avaliations/percurso_task/estudantes_ajax_last/" +curso,
                    "type": "GET"
                },
                destroy: true,
                buttons: [
                    'colvis',
                    'excel'
                ],
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    },
                    {
                        data: 'matricula',
                        name: 'matricula',
                        searchable: true
                    },
                    {
                        data: 'nome',
                        name: 'nome',
                        searchable: true
                    },
                    {
                        data: 'email',
                        name: 'email',
                        searchable: true
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        searchable: false
                    }
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

        $("#Curso_id_Select").change(function() {
            pegar_estudantes($(this).val());
        });
        pegar_estudantes(11);
    </script>
@endsection