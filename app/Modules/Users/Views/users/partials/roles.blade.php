@php
    $authUser = auth()->user();
    $isCoordenador = is_coordenador($authUser);
    $otherProfile = isset($user) && $user->id != $authUser->id;
    $show = !($isCoordenador && $otherProfile);
@endphp

@if($show)
<div class="@if(!isset($large)) card @endif">
    <div class="@if(!isset($large)) card-body row @endif">
        <div class="@if(!isset($large)) col @else large-form form-group col @endif">
            @if (isset($large))
                <label for="">@lang('Users::roles.roles')</label>
            @else
                <h5 class="card-title mb-3">@lang('Users::roles.roles')</h5>
            @endif

            @if(($action ?? null) === 'create')
                {{ Form::bsLiveSelect('roles', $roles ?? [], request()->role ?: null, ['required', 'placeholder' => '']) }}
            @else
                @if(isset($user) && isset($user->roles))
                    {!! implode(', ', $user->roles->pluck('currentTranslation.display_name')->toArray()) !!}
                @else
                    <span class="text-muted">@lang('Users::roles.no_roles')</span>
                @endif
            @endif
        </div>
    </div>
</div>
@endif
