<div class="container-fluid">
    <div class="row">
        <div class="col">
            <h3>@lang('GA::events.options')</h3>
            {{ Form::bsCustom('options[backgroundColor]', $options['backgroundColor'] ?? '#3788d8', ['type' => 'color', 'placeholder' => __('GA::events.backgroundColor'), 'disabled' => $action === 'show', 'required'], ['label' => __('GA::events.backgroundColor')]) }}
            {{ Form::bsCustom('options[borderColor]', $options['borderColor'] ?? '#3788d8', ['type' => 'color', 'placeholder' => __('GA::events.borderColor'), 'disabled' => $action === 'show', 'required'], ['label' => __('GA::events.borderColor')]) }}
            {{ Form::bsCustom('options[textColor]', $options['textColor'] ?? '#ffffff', ['type' => 'color', 'placeholder' => __('GA::events.textColor'), 'disabled' => $action === 'show', 'required'], ['label' => __('GA::events.textColor')]) }}
        </div>
    </div>
</div>

