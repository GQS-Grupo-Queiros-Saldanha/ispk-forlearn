{{--<label>@lang('GA::discipline-classes.discipline_class')</label>
    @if(in_array($action, ['create','edit'], true))
        {{ Form::bsSelect('class', [], $action === 'create' ? old('class') : $schedule->discipline_class_id ?? null, ['required']) }}
    @else
        <span>{!! $schedule->class->display_name !!} </span>
   @endcan
</div>--}} 
{{--  <div class="form-group col">

    @if(in_array($action, ['create','edit'], true))

   <label>Turma</label>
        <select class="selectpicker form-control form-control-sm"
        data-actions-box="true"
        data-live-search="true" name="classes">
        <option></option>
            @foreach($classes as $class)
                @if(isset($schedule->discipline_class_id) && ($schedule->discipline_class_id==$class->id))    
                    <option selected value="{{$class->id}}">{{$class->display_name}}</option>
                @else  
                    <option value="{{$class->id}}">{{$class->display_name}}</option>
                @endif
            @endforeach 
        </select>


@else
    <label>Turma</label>
    <span>{!! $schedule->class->display_name !!}</span>
@endcan
</div> 
 --}}
 
<style>
    .border-for{
        border: 1px solid #e1e1e1!important;
    }
</style>
<div class="form-group col">
    <label>Turma</label>
    @if(in_array($action, ['create','edit'], true))
        {{ Form::Select('classes', [], $action === 'create' ? old('classes') : $schedule->discipline_class_id ?? null, ['required'],['label' => ' ', 'class' => 'btn dropdown-toggle bs-placeholder btn-light border-for']) }}
    @else
        <span>{!! $schedule->class->display_name !!}</span>
    @endcan
</div>


