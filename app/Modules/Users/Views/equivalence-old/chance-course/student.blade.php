<title>Mudança de curso | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'Estudantes com mudança de curso- CURSOS ESPECIAIS')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('matriculations.index') }}">Matrícula</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Listagem dos estudantes</li>
@endsection
@section('selects')
    <div class="mb-2">
        <label for="lective_years">Selecione o ano lectivo</label>
        <select name="lective_years" id="lective_years" class="selectpicker form-control form-control-sm">
            <option selected value="" data-terminado="1">Seleciona o ano lectivo</option>
            @foreach ($lectiveYears as $lectiveYear)
                <option value="{{ $lectiveYear->id }}" @if ($lectiveYearSelected == $lectiveYear->id) selected @endif
                    data-terminado="{{ $lectiveYear->is_termina }}">
                    {{ $lectiveYear->currentTranslation->display_name }}
                </option>
            @endforeach
        </select>
    </div>
@endsection
@section('body')
    <table id="change-curso-table" class="table table-striped table-hover">
        <thead>
            <tr>
                <th id="dado">#</th>
                <th>Nome</th>
                <th>Curso antigo</th>
                <th>Curso novo</th>
                <th>Accões</th>
            </tr>
        </thead>
    </table>
@endsection
@section('models')
    @include('layouts.backoffice.modal_confirm')
    @include('Users::equivalence.chance-course.modal_create')
    @include('Users::equivalence.chance-course.modal_equivalence_change')
@endsection
@section('scripts-new')
    <script>
        (() => {

            $(".new_change_course").click(function() {
                let text_year = $("#lective_years").val();
                $("#TituloCreatechange").text("Associar cursos ");
                $("#InputYear").val(text_year);
                $("input").text('');

                $("#CreateCursoChange").modal('show');
            });

            $("#modal_create_save").click(function() {
                if ($("#coursePrimary").val() != $("#courseNew").val()) {
                    $("#formChangeCourse").submit();
                } else {
                    $("#AlertaModa").removeClass('alert-warning');
                    $("#AlertaModa").addClass('alert-danger');
                    $("#alertMessage").text(
                        'Selecione cursos diferentes antes de tentar guardar a associação de mudança de curso.'
                    );
                }
            });

            $("#close_modal_create").click(function() {
                $("#CreateCursoChange").modal('hide');
            });

            let id_anoLective = $("#lective_years");

            id_anoLective.bind('change keypress', function() {
                $('#change-curso-table').DataTable().clear().destroy();
                Table();
            });

            $(".new_matricula").click(function(e) {
                id_anoLective = $("#lective_years").val();
                $(this).attr('href', 'confirmation_matriculation/create/' + id_anoLective);
            });

            Table();

            function Table() {
                let ano = $("#lective_years").val();
                let dataTable = $('#change-curso-table').DataTable({
                    ajax: "student-change-ajax/" + ano,
                    buttons: [
                        'colvis',
                        'excel',
                    ],
                    columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                    }, {
                        data: 'name',
                        name: 'name'
                    }, {
                        data: 'curso_velho',
                        name: 'curso_velho'
                    }, {
                        data: 'curso_novo',
                        name: 'curso_novo'
                    }, {
                        data: 'actions',
                        name: 'action',
                        orderable: false,
                        searchable: false
                    }],
                    language: {
                        url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}'
                    }
                });
                dataTable.page('first').draw('page');
            }
        })();
    </script>
@endsection