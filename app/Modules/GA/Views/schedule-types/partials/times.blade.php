<div class="card bg-light" data-dynamic-form>
    <div class="card-header">
        <h3 class="card-title p-3">@lang('GA::schedule-types.times')</h3>
    </div>
    <div class="card-body border" data-dynamic-form-template="times" @if($action !== 'create') data-dynamic-form-fill="{{ $times }}" @endif>

    @if($action === 'edit')
        {{ Form::hidden('times[ID][id]', null, ['data-dynamic-form-input-id-template' => 'ID', 'data-dynamic-form-input-name' => 'id']) }}
    @endif
    {{ Form::bsText('times[ID][code]', null, [
        'placeholder' => __('common.code'),
        'disabled' => $action === 'show',
        'required',

        // Dynamic fields
        'data-dynamic-form-input-id-template' => 'ID',
        'data-dynamic-form-input-name' => 'code',
    ], ['label' => __('common.code')]) }}
    <div class="row">
        <div class="col-6">
       
    {{ Form::bsCustom('times[ID][start]', null, [
        'type' => 'time',
        'placeholder' => __('GA::schedule-types.start'),
        'title' => __('Início'),
        'disabled' => $action === 'show',
        'required',

        // Dynamic fields
        'data-dynamic-form-input-id-template' => 'ID',
        'data-dynamic-form-input-name' => 'start',
    ], ['label' => __('Início')]) }}
        </div>

        <div class="col-6">
        {{ Form::bsCustom('times[ID][end]', null, [
        'type' => 'time',
        'placeholder' => __('GA::schedule-types.end'),
        'title' => __('Término'),
        'disabled' => $action === 'show',
        'required',

        // Dynamic fields
        'data-dynamic-form-input-id-template' => 'ID',
        'data-dynamic-form-input-name' => 'end',
    ], ['label' => __('Término')]) }}

        </div>
    </div>
   


    
    <!-- Translations -->
        <div class="card">
            <div class="card-header d-flex p-0">
                <h5 class="card-title p-3">@lang('translations.languages')</h5>
                <ul class="nav nav-pills ml-auto p-2">
                    @foreach($languages as $language)
                        <li class="nav-item">
                            <a class="nav-link @if($language->default) active show @endif"
                               href="#language{{ $language->id }}"
                               data-toggle="tab">{{ $language->name }}</a>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    @foreach($languages as $language)
                        <div class="tab-pane row @if($language->default) active show @endif" id="language{{ $language->id }}">
                            <div class="row">
                                    <div class="col-6">
                                    {{ Form::bsText('times[ID][display_name]['.$language->id.']', null, [
                                'placeholder' => __('translations.display_name'),
                                'disabled' => $action === 'show',
                                !$language->default ?: 'required',

                                // Dynamic fields
                                'data-dynamic-form-input-id-template' => 'ID',
                                'data-dynamic-form-input-name' => "translations.{$language->id}.display_name",
                            ], ['label' => __('translations.display_name')]) }}
                                    </div>
                                    <div class="col-6">

                                    {{ Form::bsText('times[ID][description]['.$language->id.']', null, [
                                'placeholder' => __('translations.description'),
                                'disabled' => $action === 'show',
                                !$language->default ?: 'required',

                                // Dynamic fields
                                'data-dynamic-form-input-id-template' => 'ID',
                                'data-dynamic-form-input-name' => "translations.{$language->id}.description",
                            ], ['label' => __('translations.description')]) }}
                                    </div>
                            </div>
                            

                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        @if($action !== 'show')
            <button type="button" class="btn btn-sm btn-success" data-dynamic-form-add>@lang('common.add')</button>
            <button type="button" class="btn btn-sm btn-danger" data-dynamic-form-remove>
                @lang('common.remove')
                <i class="fas fa-level-down-alt"></i>
            </button>
        @endif

    </div>
</div>
