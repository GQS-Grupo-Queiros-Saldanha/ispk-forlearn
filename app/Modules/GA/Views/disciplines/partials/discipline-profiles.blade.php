<div class="form-group col">
    <label>@lang('GA::discipline-profiles.discipline_profile')</label>
    @if(in_array($action, ['create','edit'], true))
        {{ Form::bsLiveSelect('profile', $profiles, isset($discipline) ? $discipline->discipline_profiles_id : null, ['required', 'placeholder' => '', 'id' => 'discipline_profile']) }}
    @else
        {{ $discipline->disciplineProfile->translation->display_name }}
    @endcan
</div>
