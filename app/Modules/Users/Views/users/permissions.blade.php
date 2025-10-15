@section('title',__('Users::users.permissions'))
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
                        <h1 class="m-0 text-dark">@lang('Users::users.permissions')</h1>
                    </div>
                    <div class="col-sm-6">
                        {{ Breadcrumbs::render('users.permissions', $user) }}
                    </div>
                </div>
            </div>
        </div>

        {{-- Main content --}}
        <div class="content">
            <div class="container-fluid">

                {!! Form::open(['route' => ['users.savePermissions', $user->id], 'method' => 'put']) !!}

                <div class="row">
                    <div class="col">

                        <button type="submit" class="btn btn-sm btn-success mb-3">
                            @icon('fas fa-save')
                            @lang('common.save')
                        </button>

                        <div class="card">
                            <div class="card-body">

                                <table id="permissions-table" class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th></th>
                                        <th>@lang('Users::permissions.name')</th>
                                        <th>@lang('translations.display_name')</th>
                                        <th>@lang('common.created_at')</th>
                                        <th>@lang('common.updated_at')</th>
                                    </tr>
                                    </thead>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>

                {!! Form::close() !!}

            </div>
        </div>
    </div>
@endsection

@section('scripts')
    @parent
    <script>
        $(function () {
            $('#permissions-table').DataTable({
                ajax: '{!! route('users.permissions.ajax', $user->id) !!}',
                paging: false,
                columns: [{
                    data: 'select',
                    name: 'select',
                    className: 'text-center adjust-checkbox-margin-top',
                    orderable: false,
                    searchable: false
                }, {
                    data: 'name',
                    name: 'name',
                    visible: false
                }, {
                    data: 'display_name',
                    name: 'pt.display_name'
                }, {
                    data: 'created_at',
                    name: 'created_at',
                    visible: false
                }, {
                    data: 'updated_at',
                    name: 'updated_at',
                    visible: false
                }],
                language: {
                    url: '{{ asset('lang/datatables/'.App::getLocale().'.json') }}',
                }
            });
        });
    </script>
@endsection
