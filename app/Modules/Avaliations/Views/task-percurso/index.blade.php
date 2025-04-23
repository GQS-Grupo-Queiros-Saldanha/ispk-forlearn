<title>Avaliações | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'PERCURSO DUPLICADO')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('panel_avaliation') }}">Avaliações</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Percurso duplicado</li>
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
                        <option value="{{ $item->id }}"> {{ $item->currentTranslation['display_name'] }} </option>
                    @endif
                @endforeach
            </select>
        </div>
        <div class="col-6 p-2">
            <div class="row">
                <div class="col-9"></div>
                <div class="col-2">
                    @if (auth()->user()->hasAnyRole(['superadmin']))
                        <a class="btn btn-warning" href="{{ route('percurso_task.painel_task') }}"> Restaurar</a>
                    @endif
                </div>
            </div>
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
@section('models')
    <div class="modal fade bd-example-modal-lg" ata-bs-backdrop="static" id="myModal" tabindex="-1" role="dialog"
        aria-labelledby="myLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header  alert alert-danger">
                    <h5 class="modal-title" id="staticBackdropLabel">ALERTA</h5>
                </div>
                <div class="modal-body">
                    <p>
                        Caro(a) Utilizador(a) <b>{{ $funcionario->nome }}</b> ,
                        informamos que esta é uma área sensível.<br>
                        As futuras alterações que serão efectuadas no <b> percurso académico </b> de um determinado
                        <b>Estudante</b>,
                        serão de sua inteira responsabilidade.
                    </p>
                </div>
                <div class="modal-footer">
                    <a class="btn btn-danger" href="/avaliations/panel_avaliation"
                        style="text-decoration: none;border-radius: 7px;">Não Concordo</a>
                    <button type="button" class="btn btn-success concordo" data-bs-dismiss="#myModal"
                        style="border-radius: 7px;">Concordo</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts-new')
    @parent
    <script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.js"></script>
    <script>
        Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');

        // Quando o tipo de avaliação for alterada
        function pegar_estudantes(curso) {
            var AnoDataTable = $('#request-table').DataTable({
                ajax: {
                    "url": "/avaliations/percurso_task/estudantes_ajax/" + curso,
                    "type": "GET"
                },
                destroy: true,
                searchable: true,
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
        $(".concordo").click(function() {
            $('#myModal').modal('hide')
        });
        $('#myModal').modal('show');
        pegar_estudantes(11);
    </script>
@endsection
