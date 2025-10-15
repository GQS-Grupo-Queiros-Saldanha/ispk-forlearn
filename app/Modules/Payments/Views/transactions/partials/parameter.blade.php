{{--
To call this partial:
    $action string [required]
    $parameter Parameter [required]
    $parameter_group ParameterGroup [required]
    $user User [required]
    $child boolean - TRUE if it shows up on a selected option
--}}

<div @if(isset($child) && $child) data-parameter="{{ $parameter->id }}" @endif class="@if(isset($child) && $child) p-1 bg-light collapse @endif">
    @php
        $disabled = $action === 'show' || !Auth::user()->hasAnyRole($parameter->roles->pluck('id')->toArray());
        $required = $parameter->required ? 'required' : null;
        $name = 'parameters[' . $parameter_group->id . '][' . $parameter->id . ']';

        //Obter value
        $value = null;
        if ($action !== 'create') {
            $user_parameter = $user->parameters->filter(function ($item) use ($parameter, $parameter_group) {
                return $item->pivot->parameters_id === $parameter->id && $item->pivot->parameter_group_id === $parameter_group->id;
            })->first();

            if ($user_parameter) {
                if (in_array($user_parameter->type, ['dropdown', 'checkbox'])) {
                    $value = explode(',', $user_parameter->pivot->value);
                } else {
                    $value = $user_parameter->pivot->value ?? null;
                }
            }
        }
    @endphp

    @switch($parameter->type)
        @case('integer')    {{ Form::bsNumber($name, $value, ['placeholder' => $parameter->currentTranslation->description, 'title' => $parameter->currentTranslation->description, 'disabled' => $disabled, 'step' => '1', $required], ['label' => $parameter->currentTranslation->display_name]) }} @break
        @case('float')      {{ Form::bsNumber($name, $value, ['placeholder' => $parameter->currentTranslation->description, 'title' => $parameter->currentTranslation->description, 'disabled' => $disabled, 'step' => 'any', $required], ['label' => $parameter->currentTranslation->display_name]) }} @break
        @case('textarea')   {{ Form::bsTextArea($name, $value, ['placeholder' => $parameter->currentTranslation->description, 'title' => $parameter->currentTranslation->description, 'disabled' => $disabled, $required], ['label' => $parameter->currentTranslation->display_name]) }} @break
        @case('dropdown')
        {{--{{ Form::bsSelect($name . '[]', $parameter->options->pluck('currentTranslation.display_name', 'id'), $value, ['title' => $parameter->currentTranslation->description, 'disabled' => $disabled, $required], ['label' => $parameter->currentTranslation->description]) }}--}}

        {{-- Has to be manually set because of extra parameters --}}
        <div class="form-group col">
            @if(!isset($label) || (isset($label) && $label))
                {{ Form::label($name, $parameter->currentTranslation->display_name ?? $name) }}
            @endif

            <select @if($parameter->options->contains('has_related_parameters', true)) data-options-have-related-parameters="true" @endif name="{{ $name }}" class="form-control form-control-sm{{ $errors->has($name) ? ' is-invalid' : '' }}" @if($parameter->required) required @endif title="{{ $parameter->currentTranslation->description }}">
                @if(!$parameter->required && empty($value))
                    <option value="" selected disabled></option>
                @endif
                @foreach($parameter->options as $option)
                    <option
                        value="{{ $option->id }}"
                        @if($option->has_related_parameters) data-related-parameters="{{ json_encode($option->relatedParametersRecursive->pluck('id')->toArray()) }}" @endif
                        @if(in_array($option->id, $value ?? [], false)) selected @endif
                    >{{ $option->currentTranslation->display_name }}
                    </option>
                @endforeach
            </select>

            {{-- Related parameters --}}
            @if($parameter->type === 'dropdown')
                @php
                    $options_with_related_parameters = $parameter->options->filter(function($item) {
                        return $item->has_related_parameters;
                    });
                @endphp
                @if(!$options_with_related_parameters->isEmpty())
                    @foreach($options_with_related_parameters as $option)
                        @foreach($option->relatedParametersRecursive as $parameter)
                            @include('Users::users.partials.parameter', ['parameter' => $parameter, 'action' => $action, 'parameter_group' => $parameter_group, 'user' => $user, 'child' => true])
                        @endforeach
                    @endforeach
                @endif
            @endif

            @if ($errors->has($name))
                <div class="is-invalid">
                    <strong>{{ $errors->first($name) }}</strong>
                </div>
            @endif
        </div>
        @break
        @case('file_pdf')
        @case('file_image')
        @case('file_doc')
        {{ Form::bsUpload($name, $value, ['disabled' => $disabled, $required], ['label' => $parameter->currentTranslation->display_name]) }}
        @break
        @case('checkbox')
        <div class="form-group">
            <label>{{ $parameter->currentTranslation->display_name }}</label>
            @foreach($parameter->options as $option)
                {{ Form::bsCheckbox($name . '[' . $option->id . ']', $option->id, $action === 'create' ? old('parameters.'.$parameter->id.'.'.$option->id) ?? false : in_array($option->id, $value ?? [], false) , ['disabled' => $disabled], ['label' => $option->currentTranslation->display_name]) }}
            @endforeach
        </div>
        @break
        @default
        {{ Form::bsCustom($name, $value, ['type' => $parameter->type, 'placeholder' => $parameter->currentTranslation->description, 'title' => $parameter->currentTranslation->description, 'disabled' => $disabled, 'required' => $required], ['label' => $parameter->currentTranslation->display_name]) }}
    @endswitch
</div>
