<div class="form-group col" style="margin-bottom: 24px;display:inline">
    
    @if(in_array($action, ['show','edit'], true))
    <input type="checkbox" name="is_special"
    @if($course->is_special == 1) checked @endif
    @if($action == 'show') disabled @endif>
    @else
    <input type="checkbox" name="is_special">
    @endif
    <label>Curso Especial</label>
</div>