@section('title',__('GA::lective-years.lective_years'))
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
                <div class="row">
                    <div class="col-sm-12">
                        <div class=" float-right">
                            {{ Breadcrumbs::render('lective-years') }}   
                        </div>
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">
                            {{-- @lang('GA::lective-years.lective_years') --}}
                            Lista de anos curriculares bloqueados
                        </h1>
                    </div>
                    {{--
                    <div class="col-sm-6" style="padding-right: 30px">
                        <div class="float-right div-anolectivo" style="width: 45%; !important">
                            <label for="lective_years">Selecione o ano lectivo</label>
                            <select name="lective_years" id="lective_years" class="selectpicker form-control form-control-sm">                                
                                
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
                    --}}
                </div>
            </div>


        </div>

        <!-- Main content -->
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">

                        {{-- <a href="{{ route('lective-years.create') }}" class="btn btn-success btn-sm mb-3">
                            @icon('fas fa-plus-square')
                            @lang('common.new')
                        </a> --}}

                        <div class="card">
                            <div class="card-body">

                                <table id="lective-years-table" class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        {{-- <th>@lang('common.code')</th> --}}
                                        {{-- <th>@lang('translations.display_name')</th> --}}
                                        {{-- <th>@lang('GA::lective-years.start_date')</th> --}}
                                        {{-- <th>@lang('GA::lective-years.end_date')</th> --}}
                                        {{-- <th>@lang('common.created_by')</th> --}}
                                        {{-- <th>@lang('common.updated_by')</th> --}}
                                        {{-- <th>@lang('common.created_at')</th> --}}
                                        {{-- <th>@lang('common.updated_at')</th> --}}
                                        {{-- <th>@lang('common.actions')</th> --}}
                                        <th>#</th>
                                        <th>Ano lectivo</th>
                                        <th>Curso</th>
                                        <th>Ano curricular</th>
                                        <th>Estado</th>
                                        <th>@lang('common.created_by')</th>
                                        <th>@lang('common.created_at')</th>
                                        <th>@lang('common.updated_by')</th>
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

@endsection

@section('scripts')
    @parent
    <script>
        $(function () {
            $('#lective-years-table').DataTable({
                ajax: '{!! route('course-curricular-year-block.ajax') !!}',
                    buttons:[
                    'colvis',
                    'excel'
                ],
                columns: [{
                        data: 'DT_RowIndex',
                        orderable: false,
                        searchable: false
                },
                // {
                //     data: 'code',
                //     name: 'code',
                //     visible: false
                // }, 
                {
                    data: 'display_name',
                    name: 'lyt.display_name'
                }, {
                    data: 'course_name',
                    name: 'course_name'
                }, {
                    data: 'curricular_year',
                    name: 'curricular_year'
                }, {
                    data: 'state',
                    name: 'state'
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
                , {
                    data: 'actions',
                    name: 'action',
                    orderable: false,
                    searchable: false
                }
            ],
                language: {
                    url: '{{ asset('lang/datatables/'.App::getLocale().'.json') }}',
                }
            });
        });

        // Delete confirmation modal
        Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');

    </script>
@endsection
