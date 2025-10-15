@extends('layouts.backoffice')

<title>Aulas | forLEARN® by GQS</title>
@section('styles')
    @parent
@endsection

@section('content')
    <script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script>
    <div class="content-panel" style="padding: 0px;">
        @include('Lessons::navbar.navbar')
        <div class="content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12">
                        <div class=" float-right">
                            {{ Breadcrumbs::render('lessons') }}
                        </div>
                    </div>
                </div>
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>@lang('Lessons::lessons.lessons')</h1>
                    </div>
                    @if (auth()->user()->hasRole(['student']))
                       
                    @else
                        <div class="col-sm-6" style="padding-right: 30px">
                            <div class="float-right div-anolectivo" style="width: 45%; !important">
                                <label>Selecione o ano lectivo</label>
                                <br>
                                <select name="lective_year" id="lective_year"
                                    class="selectpicker form-control form-control-sm">
                                    @foreach ($lectiveYears as $lectiveYear)
                                        @if ($lectiveYearSelected == $lectiveYear->id)
                                            <option value="{{ $lectiveYear->id }}" selected>
                                                {{ $lectiveYear->currentTranslation->display_name }}
                                            </option>
                                        @else
                                            <option value="{{ $lectiveYear->id }}">
                                                {{ $lectiveYear->currentTranslation->display_name }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>

                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Main content --}}
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">

                        {{-- <a href="{{ route('lessons.create') }}" class="btn btn-primary btn-sm mb-3">
                            <i class="fas fa-plus-square"></i>
                            @lang('common.new')
                        </a> --}}
                        @if (auth()->user()->hasRole(['student']) )
                       
                        <div class="card" style="height: 50vh;">
                            <div class="card-body">
                              
                            </div>
                        </div>

                        @else

                        <div class="card">
                            <div class="card-body">

                                <table id="lessons-table" class="table table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>@lang('Lessons::lessons.teachers')</th>
                                            <th>@lang('Lessons::lessons.discipline')</th>
                                            <th>@lang('Lessons::lessons.class')</th>
                                            <th>@lang('Lessons::lessons.regime')</th>
                                            <th>@lang('Lessons::lessons.summary')</th>
                                            <th>@lang('common.created_at')</th>
                                            <th>@lang('common.updated_at')</th>
                                            <th>@lang('common.actions')</th>
                                        </tr>
                                    </thead>
                                </table>

                            </div>
                        </div>
                        @endif
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
        $(function() {
            var lective_year = $("#lective_year").val();

            $("#lective_year").change(function() {
                lective_year = $("#lective_year").val();

                $("#lessons-table").DataTable().destroy();
                get_lessons(lective_year);

            });

            get_lessons(lective_year);

            function get_lessons(id_lective_year) {
                $('#lessons-table').DataTable({
                    ajax: {
                        url: "lessons_ajax/" + id_lective_year,
                        type: "GET",
                        data: {
                            _token: '{{ csrf_token() }}'
                        },
                        // cache: false,
                        // dataType: 'json'
                    },
                    buttons: [
                        'colvis',
                        'excel'
                    ],
                    columns: [{
                        data: 'id',
                        name: 'id'
                    }, {
                        data: 'teacher',
                        name: 'teacher.name',
                    }, {
                        data: 'discipline',
                        name: 'dt.display_name'
                    }, {
                        data: 'class',
                        name: 'c.display_name'
                    }, {
                        data: 'regime',
                        name: 'drt.display_name'
                    }, {
                        data: 'summary',
                        name: 'st.display_name',
                    }, {
                        data: 'created_at',
                        name: 'created_at',
                        visible: false
                    }, {
                        data: 'updated_at',
                        name: 'updated_at',
                        visible: false
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
            }
        });

        // Delete confirmation modal
        Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');
    </script>
@endsection
