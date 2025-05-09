<div class="@if(!isset($large)) card @endif">
    <div class="@if(!isset($large)) card-body row @endif">
        <div class="@if(!isset($large)) col @else large-form form-group col @endif">
            @if (isset($large))
                <label for="">@lang('Users::roles.roles')</label>
            @else
                <h5 class="card-title mb-3">@lang('Users::roles.roles')</h5>
            @endif
            @if($action === 'create')
                {{ Form::bsLiveSelect('roles', $roles, request()->role ?: null, ['required', 'placeholder' => '']) }}
            @else
                {!! implode(', ', $user->roles->pluck('currentTranslation.display_name')->toArray()) !!}
            @endcan
        </div>
    </div>
</div>
