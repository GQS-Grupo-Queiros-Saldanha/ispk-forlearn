@section('title',__('GA::discipline-absence-configuration.discipline_absence_configuration'))
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
                        <h1 class="m-0 text-dark">@lang('GA::discipline-absence-configuration.discipline_absence_configuration')</h1>
                        <span class="text-muted">Faltas...</span>
                    </div>
                    <div class="col-sm-6">
                        {{ Breadcrumbs::render('discipline-absence-configuration') }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Main content --}}
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">

                        {{-- <a href="{{ route('discipline-absence-configuration.create') }}" class="btn btn-primary btn-sm mb-3">
                            @icon('fas fa-plus-square')
                            @lang('common.new')
                        </a> --}}

                        <div class="card">
                            <div class="card-body">
                                <table id="discipline-absence-configuration-table" class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th>@lang('GA::study-plan-editions.study_plan_edition')</th>
                                        <th>@lang('GA::discipline-absence-configuration.discipline_regime')</th>
                                        <th>@lang('GA::discipline-absence-configuration.discipline')</th>
                                        <th>@lang('GA::discipline-absence-configuration.max_absences')</th>
                                        <th>@lang('GA::discipline-absence-configuration.is_total')</th>
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
            $('#discipline-absence-configuration-table').DataTable({
                ajax: "{!! route('discipline-absence-configuration.ajax') !!}",
                buttons:[
                    'colvis',
                    'excel'
                ],
                columns: [{
                    data: 'spet',
                    name: 'spet.display_name'
                }, {
                    data: 'drt',
                    name: 'drt.display_name'
                }, {
                    data: 'dt',
                    name: 'dt.display_name'
                }, {
                    data: 'max_absences',
                    name: 'max_absences'
                }, {
                    data: 'is_total',
                    name: 'is_total'
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
