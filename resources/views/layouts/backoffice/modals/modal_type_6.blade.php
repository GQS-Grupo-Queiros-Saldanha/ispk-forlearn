<div class="modal fade" role="dialog" id="modal_type_6">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="form_modal_type_6" onsubmit="dt6.formSubmit(); return false;">
                <div class="modal-header">
                    <h4 class="modal-title">@lang('GA::study-plan-editions.study_plan_edition_discipline_regimes')</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-12">
                            <label>@lang('GA::disciplines.disciplines')</label>
                            {{ Form::bsLiveSelect('dc_disciplines', $disciplines, $action === 'create' ? old('discipline') : '', ['required']) }}
                        </div>

                        <!-- Translations -->

                        <div class="col-12">
                            <div class="card" style='margin-top: 20px;'>
                                <div class="card-header d-flex p-0">
                                    <h3 class="card-title p-3">@lang('translations.languages')</h3>
                                    <ul class="nav nav-pills ml-auto p-2">
                                        @foreach($languages as $language)
                                            <li class="nav-item">
                                                <a class="nav-link @if($language->default) active show @endif"
                                                href="#modal_type_6_language{{ $language->id }}"
                                                data-toggle="tab">{{ $language->name }}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                                <div class="card-body">
                                    <div class="tab-content">
                                        @foreach($languages as $language)
                                            <div class="tab-pane row @if($language->default) active show @endif" id="modal_type_6_language{{ $language->id }}">
                                                {{ Form::bsText('dct_presentation['.$language->id.']', $action === 'create' ? old('dct_presentation.'.$language->id) : $translations[$language->id]['dct_presentation'] ?? null, ['group' => '1', 'placeholder' => __('translations.dct_presentation'), 'disabled' => $action === 'show', !$language->default ?: 'required'], ['label' => __('translations.dct_presentation')]) }}
                                                {{ Form::bsText('dct_bibliography['.$language->id.']', $action === 'create' ? old('dct_bibliography.'.$language->id) : $translations[$language->id]['dct_bibliography'] ?? null, ['group' => '2', 'placeholder' => __('translations.dct_bibliography'), 'disabled' => $action === 'show', !$language->default ?: 'required'], ['label' => __('translations.dct_bibliography')]) }}
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn forlearn-btn add">
                        <i class="far fa-plus"></i>@lang('modal.confirm_button')
                    </button>
                    <button type="button" class="btn forlearn-btn cancel" data-dismiss="modal">
                        <i class="far fa-window-close"></i>@lang('modal.cancel_button')
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
