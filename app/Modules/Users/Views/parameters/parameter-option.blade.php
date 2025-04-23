{{-- Note 1: $extras['languages'] doesn't work so we have to use those stupid foreachs and $key === "languages" --}}
{{-- Note 2: it is imperative that inputs have on their name "[0]" on these kinds of partial views --}}
{{-- Note 3: it is imperative that bootstrap tabs have on their id "_0" on these kinds of partial views --}}
{{-- Note 4: please save in the parent div the index in an attribute 'data-id') --}}
@php $index = $extras['index'] ?? 0 @endphp

<div data-index="{{ $index }}" class="card">
    <div class="card-body">

        @if($extras['action'] === 'edit' && isset($extras['id']))
            {{ Form::hidden('options[' . $index . '][id]', $extras['id'])}}
        @endif

        <!-- Input -->
        <div class="input-group mb-2">
            {{ Form::text($name, $value, array_merge(['class' => 'form-control'], $attributes)) }}

            @if($extras['action'] !== 'show')
                <div class="input-group-append">
                    <a class="btn btn-danger text-white" data-role="remove-field">
                        @lang('common.remove')
                        <i class="fas fa-minus"></i>
                    </a>
                </div>
            @endif
        </div>

        <!-- Tabs -->
        <div class="nav nav-tabs" role="tablist">
            @foreach($extras as $key=>$extra)
                @if($key==='languages')
                    @foreach($extra as $language)
                        <a class="nav-item nav-link @if($language->default) active @endif"
                           href="#option_language_{{ $index }}_{{ $language->id }}"
                           data-toggle="tab"
                           role="tab">{{ $language->name }}</a>
                    @endforeach
                @endif
            @endforeach
        </div>

        <!-- Translations -->
        <div class="tab-content border-left border-right border-bottom bg-white p-3">
            @foreach($extras as $key=>$extra)
                @if($key==='languages')
                    @foreach($extra as $language)
                        <div class="tab-pane @if($language->default) active show @endif" id="option_language_{{ $index }}_{{ $language->id }}" role="tabpanel">
                            {{ Form::bsText('options[' . $index . '][display_name][' . $language->id . ']', $extras['action'] === 'create' ? old('display_name.'.$language->id) : $extras['translations'][$language->id]['display_name'] ?? null, ['placeholder' => __('translations.display_name'), 'disabled' => $extras['action'] === 'show', !$language->default ?: 'required'], ['label' => __('translations.display_name')]) }}
                            {{ Form::bsText('options[' . $index . '][description][' . $language->id . ']', $extras['action'] === 'create' ? old('description.'.$language->id) : $extras['translations'][$language->id]['description'] ?? null, ['placeholder' => __('translations.description'), 'disabled' => $extras['action'] === 'show', !$language->default ?: 'required'], ['label' => __('translations.description')]) }}
                        </div>
                    @endforeach
                @endif
            @endforeach
        </div>

        {{-- Related parameters --}}
        <br>
        <div class="form-group">
            @lang('Users::parameters.has_related_parameters')

            {{ Form::bsLiveSelectCustomOrder('options[' . $index . '][related_parameters][]', $extras['parameters'], $extras['action'] === 'create' ? old('courses') : (!$extras['parameter']->options->isEmpty() ? $extras['parameter']->options[$index]->relatedParameters->pluck('id') : null), ['multiple']) }}

            <br>

            <label>@lang('common.ordination')</label>
            <ol class="list-group list-group-sortable order"></ol>

        </div>
    </div>
</div>
<br>
