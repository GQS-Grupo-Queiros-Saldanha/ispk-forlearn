<div class="form-group col">
    <label>@lang('GA::discipline-areas.discipline_area')</label>
    @if(in_array($action, ['create','edit'], true))
        {{ Form::bsLiveSelect('discipline_areas[]', $areas, $action === 'create' ? old('discipline_areas') : $discipline->disciplineAreas->pluck('id')->toArray(), ['required', 'multiple']) }}
    @else
        {{ implode(', ', $discipline->disciplineAreas->pluck('translation.display_name')->toArray()) }}
    @endcan
</div>
