{{--
To call this partial:
    $action string [required]
    $parameter Parameter [required]
    $parameter_group ParameterGroup [required]
    $user User [required]
    $child boolean - TRUE if it shows up on a selected option
--}}
{{-- {{$parameter_group }} --}}
@php
    use App\Modules\Users\util\FaseCandidaturaUtil;
    
    $array_excludes = isset($excludes) ? $excludes : [];
    $view = true;
    foreach ($array_excludes as $obj) {
        if ($obj->parametor == $parameter->id && $parameter_group->id == $obj->group) {
            $view = false;
            break;
        }
    }
    
@endphp
@if ($view)
    <div @if (isset($child) && $child) data-parameter="{{ $parameter->id }}" @endif
        class="@if(isset($child) && $child) bg-light collapse @endif">
        @php
            $disabled = $action === 'show' || !Auth::user()->hasAnyRole($parameter->roles->pluck('id')->toArray());
            $required = $parameter->required ? 'required' : null;
            $name = 'parameters[' . $parameter_group->id . '][' . $parameter->id . ']';
            //Obter value
            $value = null;
            if ($action !== 'create') {
                $user_parameter = $user->parameters
                    ->filter(function ($item) use ($parameter, $parameter_group) {
                        return ($item->pivot->parameters_id === $parameter->id && $item->pivot->parameter_group_id === $parameter_group->id) || ($item->pivot->parameters_id === $parameter->id && $item->pivot->parameters_id === 25);
                    })
                    ->first();
            
                if ($user_parameter) {
                    if (in_array($user_parameter->type, ['dropdown', 'checkbox'])) {
                        $value = explode(',', $user_parameter->pivot->value);
                    } else {
                        $value = $user_parameter->pivot->value ?? null;
                    }
                }
            }
            
            if ($parameter_group->id == 11 && $parameter->id == 311) {
                $value_code = FaseCandidaturaUtil::userCandidate($user->id);
                $value = isset($value_code->code) ? $value_code->code : $value;
            }
        @endphp

        @switch($parameter->type)
            @case('integer')
                @if ($parameter->code === 'telemovel_principal')
                    {{ Form::bsCustom($name, $value, ['placeholder' => $parameter->currentTranslation->description, 'title' => $parameter->currentTranslation->description, 'disabled' => $disabled, 'type' => 'tel', 'minlength' => 9, 'maxlength' => 9, 'required' => (bool) $required], ['label' => $parameter->currentTranslation->display_name]) }}
                @else
                    {{ Form::bsNumber($name, $value, ['placeholder' => $parameter->currentTranslation->description, 'title' => $parameter->currentTranslation->description, 'disabled' => $disabled, 'step' => '1', $required], ['label' => $parameter->currentTranslation->display_name]) }}
                @endif
            @break

            @case('float')
                {{ Form::bsNumber($name, $value, ['placeholder' => $parameter->currentTranslation->description, 'title' => $parameter->currentTranslation->description, 'disabled' => $disabled, 'step' => 'any', $required], ['label' => $parameter->currentTranslation->display_name]) }}
            @break

            @case('textarea')
                {{ Form::bsTextArea($name, $value, ['placeholder' => $parameter->currentTranslation->description, 'title' => $parameter->currentTranslation->description, 'disabled' => $disabled, $required], ['label' => $parameter->currentTranslation->display_name]) }}
            @break

            @case('dropdown')
                {{-- {{ Form::bsSelect($name . '[]', $parameter->options->pluck('currentTranslation.display_name', 'id'), $value, ['title' => $parameter->currentTranslation->description, 'disabled' => $disabled, $required], ['label' => $parameter->currentTranslation->description]) }} --}}
                {{-- {{ Has to be manually set because of extra parameters --}}
                {{-- @if ($parameter_group->id == 14) float-right-inner @endif --}}
                <div class="form-group col  @if(isset($child) && $child) form-inner @endif">

                    @if (!isset($label) || (isset($label) && $label))
                        {{ Form::label($name, $parameter->currentTranslation->display_name ?? $name) }}
                    @endif

                    <select @if ($parameter->options->contains('has_related_parameters', true)) data-options-have-related-parameters="true" @endif
                        name="{{ $name }}"
                        class="form-control {{ $errors->has($name) ? ' is-invalid' : '' }} @if (isset($child) && $child) mb-1 @endif"
                        @if ($parameter->required) required @endif
                        title="{{ $parameter->currentTranslation->description }}">
                        @if (!$parameter->required && empty($value))
                            <option value="" selected disabled></option>
                        @endif
                        @foreach ($parameter->options as $option)
                            <option value="{{ $option->id }}"
                                @if ($option->has_related_parameters) data-related-parameters="{{ json_encode($option->relatedParametersRecursive->pluck('id')->toArray()) }}" @endif
                                @if (in_array($option->id, $value ?? [], false)) selected @endif>
                                {{ $option->currentTranslation->display_name }}
                            </option>
                        @endforeach
                    </select>
                    {{-- Related parameters --}}
                    @if ($parameter->type === 'dropdown')
                        @php
                            $options_with_related_parameters = $parameter->options->filter(function ($item) {
                                return $item->has_related_parameters;
                            });
                            $params_include = [];
                            $array_parms = isset($include_only) ? $include_only : [];
                            $tam_array = sizeof($array_parms);
                            foreach ($array_parms as $parm) {
                                $params_include[] = $parm->parametor;
                            }
                        @endphp
                        @if (!$options_with_related_parameters->isEmpty())
                            @foreach ($options_with_related_parameters as $option)
                                @php
                                   //$option->relatedParametersRecursive = FaseCandidaturaUtil::optionsParms($option->relatedParametersRecursive);  
                                @endphp
                                @foreach ($option->relatedParametersRecursive as $parameter)
                                    @php
                                        $data = [
                                        'parameter' => $parameter,
                                        'action' => $action,
                                        'parameter_group' => $parameter_group,
                                        'user' => $user,
                                        'child' => true,
                                        ];
                                    @endphp
                                    @include('Users::users.partials.parameter', $data)
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
                {{-- <label> {{ $parameter->currentTranslation->display_name }}</label>
            <input type="file" name="{{ $name }}" value="{{ $value }}" accept="application/pdf" disabled=" {{ $disabled }}" {{ $required }}> --}}
                {{-- Form::bsUpload($name, $value, ['disabled' => $disabled, $required], ['label' => $parameter->currentTranslation->display_name]) --}}
            @case('file_image')
                @if (isset($value))
                    @php $url = 'https://' . $_SERVER['HTTP_HOST'] . '/instituicao-arquivo/attachment/' . $value;  @endphp
                    @if ($action == 'show')
                        <img class="img-thumbnail" src="{{ $url }}" alt="" srcset="">
                    @elseif($action != 'edit')
                        <iframe class="ml-3" for="{{ $name }}" src="{{ $url }}" width="90%" height="200px"></iframe>
                    @endif
                @endif
            @case('file_doc')
                @if($action != "show")
                    {{ Form::bsUpload($name, $value, ['disabled' => $disabled, $required], ['label' => $parameter->currentTranslation->display_name]) }}
                @endif
            @break

            @case('checkbox')
                <div class="form-group">
                    <label>{{ $parameter->currentTranslation->display_name }}</label>
                    @foreach ($parameter->options as $option)
                        {{ Form::bsCheckbox($name . '[' . $option->id . ']', $option->id, $action === 'create' ? old('parameters.' . $parameter->id . '.' . $option->id) ?? false : in_array($option->id, $value ?? [], false), ['disabled' => $disabled], ['label' => $option->currentTranslation->display_name]) }}
                    @endforeach
                </div>
            @break

            @default
                @if ($parameter->code === 'n_bilhete_de_identidade')
                    {{-- {{$name}} --}}
                    {{ Form::bsCustom($name, $value, ['type' => $parameter->type, 'placeholder' => $parameter->currentTranslation->description, 'title' => $parameter->currentTranslation->description, 'minlength' => 14, 'maxlength' => 14, 'disabled' => $disabled, 'required' => (bool) $required], ['label' => $parameter->currentTranslation->display_name]) }}
                @else
                    {{ Form::bsCustom($name, $value, ['type' => $parameter->type, 'placeholder' => $parameter->currentTranslation->description, 'title' => $parameter->currentTranslation->description, 'disabled' => $disabled, 'required' => (bool) $required], ['label' => $parameter->currentTranslation->display_name]) }}
                @endif
        @endswitch
    </div>
@endif
