@section('title',__('Grades::grades.grades'))
@extends('layouts.backoffice')

@section('styles')
    @parent
@endsection

@section('content')

    <div class="content-panel">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>@lang('Grades::grades.grades')</h1>
                    </div>
                    <div class="col-sm-6">
                        {{ Breadcrumbs::render('grades.student') }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Main content --}}
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">

                        @if(auth()->user()->can('view-grades-others'))
                        <div class="card">
                            <div class="row">
                                <div class="col-6">
                                    <div class="form-group col">
                                        <label>@lang('Grades::grades.student')</label>
                                        {{ Form::bsLiveSelect('user', $users, null, ['required']) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                        @endif

                        <div class="card">
                            <div class="card-body">

                                <table id="grades-table" class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th>@lang('GA::courses.course')</th>
                                        <th>@lang('GA::disciplines.discipline')</th>
                                        <th>@lang('Grades::grades.student')</th>
                                        <th>@lang('Grades::grades.grade')</th>
                                        <th>@lang('common.created_by')</th>
                                        <th>@lang('common.updated_by')</th>
                                        <th>@lang('common.created_at')</th>
                                        <th>@lang('common.updated_at')</th>
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
        var dataTableBaseUrl = '{{ route('grade_student.ajax', 0) }}'.slice(0, -1);
        var dataTablePayments = null;

        $(function () {
            dataTablePayments = $('#grades-table').DataTable({
                ajax: dataTableBaseUrl + '{!! auth()->user()->id !!}',
                columns: [
                    {
                        data: 'course',
                        name: 'ct.display_name',
                        visible: false
                    }, {
                        data: 'discipline',
                        name: 'dt.display_name',
                    }, {
                        data: 'student',
                        name: 'u0.name',
                        visible: false
                    }, {
                        data: 'value',
                        name: 'value',
                    }, {
                        data: 'created_by',
                        name: 'u1.name',
                        visible: false
                    }, {
                        data: 'updated_by',
                        name: 'u2.name',
                        visible: false
                    }, {
                        data: 'created_at',
                        name: 'created_at',
                        visible: false
                    }, {
                        data: 'updated_at',
                        name: 'updated_at',
                        visible: false
                    }
                ],
                language: {
                    url: '{{ asset('lang/datatables/'.App::getLocale().'.json') }}'
                }
            });

            @if(auth()->user()->can('view-grades-others'))
            var selectUser = $('#user');

            function switchDataOnDataTable(element) {
                dataTablePayments.ajax.url(dataTableBaseUrl + parseInt(element.value)).load();
            }

            if (!$.isEmptyObject(selectUser)) {
                switchDataOnDataTable(selectUser[0]);
                selectUser.change(function () {
                    switchDataOnDataTable(this);
                });
            }
            @endif
        });
    </script>
@endsection
