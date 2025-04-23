<div class="content-panel">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">@lang('Users::roles.roles')</h1>
                </div>
                <div class="col-sm-6">
                    {{ Breadcrumbs::render('roles') }}
                </div>
            </div>
        </div>
    </div>

    {{-- Main content --}}
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col">

                    <a href="{{ route('roles.create') }}" class="btn btn-primary btn-sm mb-3">
                        @icon('fas fa-plus-square')
                        @lang('common.new')
                    </a>

                    <div class="card">
                        <div class="card-body">

                            <table id="roles-table" class="table table-striped table-hover">
                                <thead>
                                <tr>
                                    <th>@lang('Users::roles.name')</th>
                                    <th>@lang('translations.display_name')</th>
                                    <th>@lang('Users::roles.guard_name')</th>
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
