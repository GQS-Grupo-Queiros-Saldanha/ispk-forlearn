{{--
To call this partial:
    $action string [required]
    $parameter Parameter [required]
    $parameter_group ParameterGroup [required]
    $user User [required]
    $child boolean - TRUE if it shows up on a selected option
--}}

<div @if (isset($child) && $child) data-parameter="{{ $parameter->id }}" @endif
    class="@if (isset($child) && $child) p-1 collapse bg-light-grey @endif">
    @php
    
        $disabled = $action === 'show' || !(Auth::check() && Auth::user()->hasAnyRole($parameter->roles->pluck('id')->toArray()));

        $required = $parameter->required ? 'required' : null;
        $name = 'parameters[' . $parameter_group->id . '][' . $parameter->id . ']';
        
        //Obter value
        $value = null;
        if ($action !== 'create') {
            $user_parameter = $user->parameters
                ->filter(function ($item) use ($parameter, $parameter_group) {
                    return $item->pivot->parameters_id === $parameter->id && $item->pivot->parameter_group_id === $parameter_group->id;
                })
                ->first();
        
            if ($user_parameter) {
                $name = 'parameters[' . $parameter_group->id . '][' . $parameter->id . ']';
                $disabled = 'disabled';
                if (in_array($user_parameter->type, ['dropdown', 'checkbox'])) {
                    $value = explode(',', $user_parameter->pivot->value);
                } elseif ($user_parameter->type === 'file_image') {
                    $value = '<img class="img-parameter" src="' . URL::to('/') . '/storage/attachment/' . $user_parameter->pivot->value . '">';
                } else {
                    $value = $user_parameter->pivot->value ?? null;
                }
            }
        }
        
        if ($parameter->id == 311 && isset($user->lastCandidatura->code)) {
            $value = $user->lastCandidatura->code;
        }
        
    @endphp

    @switch($parameter->type)
        @case('dropdown')
            {{-- Has to be manually set because of extra parameters --}}
            <div class="form-group" style="font-size: {{$options['font-size']}}">
               
                {{-- CABEÇALHO --}}
                            
                @if (!isset($label) || (isset($label) && $label))
                    {{ Form::label($name, $parameter->currentTranslation->display_name ?? $name) }}
                    
                @endif
            

                <select @if ($parameter->options->contains('has_related_parameters', true)) data-options-have-related-parameters="true" @endif
                    name="{{ $name }}" class="form-control form-control-sm{{ (isset($errors) && $errors->has($name)) ? ' is-invalid' : '' }}"
                    readonly disabled style="display: none" style="font-size: {{$options['font-size']}}">
                    @if (!$parameter->required && empty($value))
                        <option value="" selected disabled></option>
                    @endif
                    @foreach ($parameter->options as $option)
                        <option style="font-size: {{$options['font-size']}}" value="{{ $option->id }}"
                            @if ($option->has_related_parameters) data-related-parameters="{{ json_encode($option->relatedParametersRecursive->pluck('id')->toArray()) }}" @endif
                            @if (in_array($option->id, $value ?? [], false)) selected @endif>{{ $option->currentTranslation->display_name }}
                        </option>
                    @endforeach
                </select>

                @if (!empty($value))
                    @foreach ($parameter->options as $option)
                        @if (in_array($option->id, $value ?? [], false))
                            {{ $option->currentTranslation->display_name }}
                        @endif
                    @endforeach
                @else
                    ...
                @endif
                {{-- Related parameters --}}
                @php
                    $options_with_related_parameters = $parameter->options->filter(function ($item) {
                        return $item->has_related_parameters;
                    });
                @endphp
                @if (!$options_with_related_parameters->isEmpty())
                    @foreach ($options_with_related_parameters as $option)
                        @foreach ($option->relatedParametersRecursive as $parameter)
                            @if (!in_array($parameter->type, ['file_doc', 'file_pdf'], true))
                                @include('Users::users.partials.pdf_parameter', [
                                    'parameter' => $parameter,
                                    'action' => $action,
                                    'parameter_group' => $parameter_group,
                                    'user' => $user,
                                    'child' => true,
                                ])
                            @endif
                        @endforeach
                    @endforeach
                @endif
            </div>
        @break

        @case('file_image')
            {{ $value }}
        @break

        @case('checkbox')
            <div class="form-group" style="font-size: {{ $options['font-size'] }}">
                <label>{{ $parameter->currentTranslation->display_name }}</label>
                @if (!empty($value))
                    <ul>
                        @foreach ($parameter->options as $option)
                            @if (in_array($option->id, $value))
                                <li>{{ $option->currentTranslation->display_name }}</li>
                            @endif
                        @endforeach
                    </ul>
                @else
                    ...
                @endif
            </div>
        @break

        @default
            <div class="form-group" style="font-size: {{ $options['font-size'] }}">
                <label>{{ $parameter->currentTranslation->display_name }}</label>
                {{ !empty($value) ? $value : '...' }}
                <input type="hidden" value="{{ $value }}">
            </div>
    @endswitch
</div>
