<div class="card">
    <div class="card-body row">
        <div class="col">
            <h5 class="card-title mb-3">@lang('Users::roles.roles')</h5>
            @if($action === 'create')
                {{ Form::bsLiveSelect('roles', $roles, null, ['required']) }}
            @else
                {!! implode(', ', $user->roles->pluck('currentTranslation.display_name')->toArray()) !!}
            @endcan
        </div>
    </div>
</div>
