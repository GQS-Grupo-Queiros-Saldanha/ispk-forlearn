<title>Avaliações | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'LANÇAR NOTAS POR TRANSIÇÃO')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('panel_avaliation') }}">Avaliações</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Lançar notas por transição</li>
@endsection
@section('styles-new')
    @parent
    <link rel="stylesheet" href="{{ asset('css/new_table_panel.css') }}"/>
@endsection
@section('selects')
    <div class="mb-2">
        {{-- <label for="lective_year">Selecione o ano lectivo</label>
        <select name="lective_year" id="lective_year" class="selectpicker form-control form-control-sm">
            <option selected value="" data-terminado="1">Seleciona o ano lectivo</option>
            @foreach ($lectiveYears as $lectiveYear)
                <option value="{{ $lectiveYear->id }}" @if ($lectiveYearSelected == $lectiveYear->id) selected @endif
                    data-terminado="{{ $lectiveYear->is_termina }}">
                    {{ $lectiveYear->currentTranslation->display_name }}
                </option>
            @endforeach
        </select> --}}
    </div>
    <div class="mb-2">
        <label>Estudantes</label>
        <select name="type" id="type" class="selectpicker form-control form-control-sm">
            <option value="1">Todos</option>
            <option value="2">Com notas</option>
            <option value="3">Sem notas</option>
        </select>
    </div>
@endsection
@section('body')
    <table id="students" class="table table-striped table-hover display">
        <thead>
            <tr>
                <th>#</th>
                <th>Nº de matrícula</th>
                <th>Estudante</th>
                <th>Email</th>
                <th>Curso</th>
                <th>Ano curricular</th>
                <th>Criado a</th>
                <th>Actualizado a</th>
                <th>Ação</th>
            </tr>
        </thead>
    </table>
@endsection
@section('models')
    @include('layouts.backoffice.modal_confirm')
@endsection
@section('scripts-new')
    @parent
    <script>
        const table = $("#students");

        function showStudentsByType(ajaxValue) {
            let tam = table.children('tbody').length;
            if (tam > 0) table.DataTable().clear().destroy();
            
            oTable = table.DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": ajaxValue,
                buttons: [
                    'colvis',
                    'excel'
                ],
                "columns": [{
                        data: 'DT_RowIndex',
                        name: 'DT_RowIndex'
                    },
                    {
                        data: 'matriculation',
                        name: 'matriculation'
                    },
                    
                    {
                        data: 'student',
                        name: 'student',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'email',
                        name: 'email',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'course',
                        name: 'course',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'year',
                        name: 'year',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'created_at',
                        name: 'created_at',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'updated_at',
                        name: 'updated_at',
                        orderable: true,
                        searchable: true
                    },
                    {
                        data: 'actions',
                        name: 'actions',
                        orderable: false,
                        searchable: false
                    }
                ],
                "lengthMenu": [
                    [10, 25, 100, -1],
                    [10, 25, 100, "Todos"]
                ],
                language: {
                    url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}'
                }

            });
        }

        reloadAjaxData();

        function reloadAjaxData() {
            let ajaxValue = "old_student_get_list";
            if ($("#type").val() == 1) {
                ajaxValue = "old_student_get_list";
            } else if ($("#type").val() == 2) {
                ajaxValue = "old_student_get_list_with_grades";
            } else if ($("#type").val() == 3) {
                ajaxValue = "old_student_get_list_without_grades";
            }

            // const lective_year = $("#lective_year").val();
            // if (lective_year != "") {
            //     ajaxValue += `?year=${lective_year}`;
            // }

            showStudentsByType(ajaxValue);
        }

        $("#type").change(function() {
            reloadAjaxData();
        });

        $("#lective_year").change(function() {
            reloadAjaxData();
        });

        Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');
    </script>
@endsection
