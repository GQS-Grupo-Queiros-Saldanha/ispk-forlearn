@section('title',__('GA::discipline-curricula.create_discipline_curricula'))
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
                        <h1 class="m-0 text-dark">@lang('GA::discipline-curricula.discipline_curricula')</h1>
                        <span class="text-muted">...</span>
                    </div>
                    <div class="col-sm-6">
                        {{ Breadcrumbs::render('discipline-curricula') }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Main content --}}
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">

                        <a href="{{ route('discipline-curricula.create') }}" class="btn btn-success btn-sm mb-3">
                            @icon('fas fa-plus-square')
                            @lang('common.new')
                        </a>

                        <div class="card">
                            <div class="card-body">
                                <table id="discipline-curricula-table" class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th>@lang('GA::discipline-curricula.discipline')</th>
                                        <th>@lang('GA::study-plan-editions.study_plan_edition')</th>
                                        <th>@lang('GA::discipline-curricula.presentation')</th>
                                        <th>@lang('GA::discipline-curricula.bibliography')</th>
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

        // Truncate a string
        function strtrunc(str, max, add) {
            add = add || '...';
            return (typeof str === 'string' && str.length > max ? str.substring(0, max) + add : str);
        }

        $(function () {
            $('#discipline-curricula-table').DataTable({
                'columnDefs': [
                    {
                        'targets': 2,
                        'render': function (data, type, full, meta) {
                            if (type === 'display') {
                                data = strtrunc(data, 50);
                            }
                            return data;
                        }
                    }, {
                        'targets': 3,
                        'render': function (data, type, full, meta) {
                            if (type === 'display') {
                                data = strtrunc(data, 50);
                            }
                            return data;
                        }
                    }
                ],
                ajax: "{!! route('discipline-curricula.ajax') !!}",
                buttons:[
                    'colvis',
                    'excel'
                ],
                columns: [
                    {
                        data: 'discipline',
                        name: 'dt.display_name'
                    }, {
                        data: 'study_plan_edition',
                        name: 'spet.display_name'
                    }, {
                        data: 'presentation',
                        name: 'dct.presentation'
                    }, {
                        data: 'bibliography',
                        name: 'dct.bibliography'
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
        });

        // Delete confirmation modal
        Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');

    </script>
@endsection
