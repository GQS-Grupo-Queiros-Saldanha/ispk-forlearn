<!-- Campo para adição da percentagem no painel da criação e edição da disciplina/ style="display: none;" -->
<div class="form-group col" id="groupFieldPercent" >
    <label>@lang('GA::discipline-percentage.percentage')</label>
    @if(in_array($action, ['create', 'edit'], true))
        {{ Form::number('percentage', $action === 'create' ? old('percentage') : ($discipline->percentage ?? null), [
            'placeholder' => __('Percentage'),
            'class' => 'form-control',
            'step' => 'any',
            'min' => '0',
            'max' => '100',
            'id' => 'percentage_field'
        ]) }}
    @else
        <div class="form-control-plaintext">{{ $discipline->percentage ?? '-' }}</div>
    @endif
</div> 


