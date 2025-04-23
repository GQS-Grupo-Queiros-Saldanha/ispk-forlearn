{{--<label>@lang('GA::discipline-classes.discipline_class')</label>--}}
    {{--@if(in_array($action, ['create','edit'], true))--}}
        {{--{{ Form::bsSelect('class', [], $action === 'create' ? old('class') : $schedule->discipline_class_id ?? null, ['required']) }}--}}
    {{--@else--}}
        {{--<span>{!! $schedule->class->display_name !!} </span>--}}
  {{-- @endcan--}}
{{--</div>--}}

{{--<div class="form-group col">--}}
   {{--<label>Turma</label>--}}
        {{--<select class="bsSelect">--}}
            {{--@foreach($classes as $class)--}}
            {{--<option value="{{$class->id}}">{{$class->display_name}}</option>--}}
            {{--@endforeach--}}
        {{--</select>--}}
{{--</div>--}}



<div class="form-group col">
    <label>Turma</label>
    @if(in_array($action, ['create','edit'], true))
        {{ Form::bsLiveSelect('classes', $classes, $action === 'create' ? old('classes') : $schedule->discipline_class_id->id ?? null, ['required']) }}
    @else
        <span>{!! $schedule->discipline_class_id !!}</span>
    @endcan
</div>
