<div id="modal-modules" class="modal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">@lang('GA::study-plan-editions.modules')</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                @include('GA::study-plan-editions.partials.module')
                @if($action !== 'show')
                <button data-toggle="modal" data-target="#modal-module" type="button" class="btn btn-sm btn-success">
                    <i class="fas fa-plus"></i>
                </button>
                @endif

                <table id="table-modules" class="table">
                    <thead>
                    <tr>
                        <th>@lang('translations.display_name')</th>
                        @if($action !== 'show')
                            <th>@lang('common.actions')</th>
                        @endif
                    </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button id="btn-save-modules" type="button" class="btn btn-success">@lang('common.save')</button>
            </div>
        </div>
    </div>
</div>
