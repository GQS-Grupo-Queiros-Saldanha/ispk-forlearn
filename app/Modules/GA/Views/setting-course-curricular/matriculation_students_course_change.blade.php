<title>Matrículas | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'Estudantes com mudança de curso por bloqueio de ano curricular')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('matriculations.index') }}">Matrículas</a>
    </li>    
    <li class="breadcrumb-item active" aria-current="page">Bloqueio de ano curricular</li>
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
    <table id="lective-years-table" class="table table-striped table-hover">
        <thead>
            <tr>
                <th>#</th>
                <th>Ano lectivo</th>
                <th>Matrícula nº</th>
                <th>Estudante</th>
                <th>Curso antigo</th>
                <th>Ano curricular</th>
                <th>Curso antigo</th>
                <th>Ano curricular</th>
                <th>@lang('common.created_by')</th>
                <th>@lang('common.created_at')</th>
                <th>@lang('common.updated_by')</th>
                <th>@lang('common.updated_at')</th>
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
        (() => {

            let lective_year = $("#lective_years");

            show_data(lective_year.val());

            lective_year.bind('change keypress', function() {
                id_anoLective = $("#lective_years").val();
                $('#lective-years-table').DataTable().clear().destroy();
                console.log(id_anoLective);

                show_data(id_anoLective);
            });

            function show_data(lectiveyear) {
                $('#lective-years-table').DataTable({
                    ajax: {
                        url: "students-course-curricular-change-ajax/" + lectiveyear,
                        "type": "GET",
                    },
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
                            data: 'display_name',
                            name: 'lyt.display_name'
                        }, {
                            data: 'num_matricula',
                            name: 'students_course_change.num_matricula '
                        }, {
                            data: 'student',
                            name: 'student.name '
                        }, {
                            data: 'course_name',
                            name: 'ct.display_name'
                        }, {
                            data: 'course_year',
                            name: 'students_course_change.course_year'
                        }, {
                            data: 'coursename',
                            name: 'ct1.display_name'
                        }, {
                            data: 'courseyear',
                            name: 'students_course_change.course_year_new'
                        }, {
                            data: 'created_by',
                            name: 'u1.name',
                            visible: true
                        }, {
                            data: 'created_at',
                            name: 'created_at',
                            visible: true
                        }, {
                            data: 'updated_by',
                            name: 'u2.name',
                            visible: true
                        }, {
                            data: 'updated_at',
                            name: 'updated_at',
                            visible: true
                        }
                    ],
                    language: {
                        url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}',
                    }
                });
            }

            Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');
            
        })();
    </script>
@endsection
