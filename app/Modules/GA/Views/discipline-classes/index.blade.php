@section('title',__('GA::discipline-classes.discipline_classes'))
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
                        <h1 class="m-0 text-dark">@lang('GA::discipline-classes.discipline_classes')</h1>
                        <span class="text-muted">Ciências e Educação, Humanidades...</span>
                    </div>
                    <div class="col-sm-6">
                        {{ Breadcrumbs::render('discipline-classes') }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Main content --}}
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">

                        <a href="{{ route('discipline-classes.create') }}" class="btn btn-success btn-sm mb-3">
                            @icon('fas fa-plus-square')
                            @lang('common.new')
                        </a>

                        <div class="card">
                            <div class="card-body">
                                <table id="discipline-classes-table" class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th>@lang('common.code')</th>
                                        <th>@lang('GA::discipline-classes.discipline_classes')</th>
                                        <th>@lang('GA::classes.classes')</th>
                                        <th>@lang('GA::discipline-classes.disciplines')</th>
                                        <th>@lang('GA::study-plan-editions.study_plan_edition')</th>
                                        <th>@lang('GA::discipline-classes.discipline_regime')</th>
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
            $('#discipline-classes-table').DataTable({
                ajax: "{!! route('discipline-classes.ajax') !!}",
                buttons:[
                    'colvis',
                    'excel'
                ], 
                columns: [{
                    data: 'discipline_class',
                    name: 'discipline_classes.display_name',
                    visible: false
                }, {
                    data: 'class',
                    name: 'c.display_name'
                }, {
                    data: 'discipline_regime',
                    name: 'drt.display_name'
                }, {
                    data: 'study_plan_edition',
                    name: 'spet.display_name'
                }, {
                    data: 'discipline',
                    name: 'dt.display_name'
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
