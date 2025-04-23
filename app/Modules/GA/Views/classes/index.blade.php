@section('title',__('GA::classes.classes'))
@extends('layouts.backoffice')

@section('styles')
    @parent
@endsection

@section('content') 

<script src="https://kit.fontawesome.com/e1fa782e3f.js" crossorigin="anonymous"></script>
    <div class="content-panel" style="padding: 0px">
        @include("GA::navbar.navbar")
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">@lang('GA::classes.classes')</h1>
                        {{-- <span class="text-muted">Turma A, Turma B,...</span> --}}
                    </div>
                    <div class="col-sm-6">
                        {{ Breadcrumbs::render('classes') }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Main content --}}
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">

                        <a href="{{ route('classes.create') }}" class="btn btn-success btn-sm mb-3">
                            @icon('fas fa-plus-square')
                            @lang('common.new')
                        </a>
                        <a href="{{ route('classes.gerarPDF', $lectiveYearSelected) }}" class="btn btn-success btn-sm mb-3" id="generate-pdf">
                            @icon('far fa-file-pdf')
                            @lang('Tabela de Turmas')
                        </a>
                        <div class="float-right mr-4" style="width:200px; !important">
                            <select name="lective_years" id="lective_years" class="selectpicker form-control form-control-sm" style="width: 100%; !important">
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

                        <div class="card">
                            <div class="card-body">
                                <table id="classes-table" class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th>@lang('common.code')</th>
                                        <th>@lang('GA::classes.class')</th>
                                        <th>@lang('GA::courses.course')</th>
                                        <th>@lang('GA::classes.year')</th>
                                        <th>@lang('GA::classes.vacancies')</th>
                                        <th>Ano lectivo</th>
                                        <th>@lang('GA::rooms.room')</th>
                                        <th>@lang('common.created_by')</th>
                                        <th>@lang('common.updated_by')</th>
                                        <th>@lang('common.created_at')</th>
                                        <th>@lang('common.updated_at')</th>
                                        <th>@lang('common.actions')</th>
                                    </tr>
                                    </thead>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- modal confirm --}}
    @include('layouts.backoffice.modal_confirm')
    @include('GA::classes.modal')

@endsection

@section('scripts')
    @parent
    <script>
        $(function () {
                $('#classes-table').DataTable({
                ajax: "{!! route('classes.ajax') !!}",
                buttons:[
                    'colvis',
                    'excel'
                ],
                columns: [{
                    data: 'code',
                    name: 'code',
                    visible: false
                }, {
                    data: 'display_name',
                    name: 'display_name'
                }, {
                    data: 'course',
                    name: 'vacancies'
                }, {
                    data: 'year',
                    name: 'year'
                }, {
                    data: 'vacancies',
                    name: 'vacancies'
                },{
                    data: 'lective_year',
                    name: 'lyt.display_name'
                }, {
                    data: 'room',
                    name: 'rt.display_name'
                }, {
                    data: 'created_by',
                    name: 'u1.name',
                    visible: false
                }, {
                    data: 'updated_by',
                    name: 'u2.name',
                    visible: false
                },{
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
                    url: '{{ asset('lang/datatables/'.App::getLocale().'.json') }}',
                }
            });

            $("#lective_years").change(function(){

                var lective_year = $("#lective_years").val();
                $('#classes-table').DataTable().clear().destroy();
                $('#classes-table').DataTable({
                "ajax": {
                "url": "/gestao-academica/study-plan-editions/classes-by-year/"+lective_year,
                "type": "GET",
                // "data": {
                //     "user_id": 451
                // }
                },
                buttons:[
                    'colvis',
                    'excel'
                ],
                columns: [{
                    data: 'code',
                    name: 'code',
                    visible: false
                }, {
                    data: 'display_name',
                    name: 'display_name'
                }, {
                    data: 'course',
                    name: 'vacancies'
                }, {
                    data: 'year',
                    name: 'year'
                }, {
                    data: 'vacancies',
                    name: 'vacancies'
                },{
                    data: 'lective_year',
                    name: 'lyt.display_name'
                }, {
                    data: 'room',
                    name: 'rt.display_name'
                }, {
                    data: 'created_by',
                    name: 'u1.name',
                    visible: false
                }, {
                    data: 'updated_by',
                    name: 'u2.name',
                    visible: false
                },{
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
                    url: '{{ asset('lang/datatables/'.App::getLocale().'.json') }}',
                }
            });

            })
        });

        // Delete confirmation modal
        Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');

    </script>
@endsection
