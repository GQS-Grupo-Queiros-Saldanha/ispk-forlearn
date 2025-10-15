<div class="form-group col-2">
        <label>Número máximo de faltas</label>
        @if(in_array($action, ['create','edit'], true))
        <input type="number" class="form-control" name="absence" value="{{ isset($discipline) ? $discipline->maximum_absence : 0}}">
        @else
            {{$discipline->maximum_absence ? $discipline->maximum_absence : 'N/A' }}
        @endcan
</div>
