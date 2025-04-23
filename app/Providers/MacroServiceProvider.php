<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Collective\Html\FormFacade as Form;

class MacroServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        // Global
        Form::component('bsCheckbox', 'components.form.checkbox', ['name', 'value' => null, 'checked' => false, 'attributes' => [], 'extra' =>[]]);
        Form::component('bsCustom', 'components.form.custom', ['name', 'value' => null, 'attributes' => [], 'extra' => []]);
        Form::component('bsDate', 'components.form.date', ['name', 'value' => null, 'attributes' => [], 'extra' =>[]]);
        Form::component('bsEmail', 'components.form.email', ['name', 'value' => null, 'attributes' => [], 'extra' =>[]]);
        Form::component('bsFloat', 'components.form.float', ['name', 'value' => null, 'attributes' => ['step' => '.01'], 'extra' =>[]]);
        Form::component('bsLiveSelect', 'components.form.live-select', ['name', 'values' => null, 'selected' => null, 'attributes' => [], 'extra' =>[]]);
        Form::component('bsLiveSelectEmpty', 'components.form.live-select-empty', ['name', 'values' => null, 'selected' => null, 'attributes' => [], 'extra' =>[]]);
        Form::component('bsLiveSelectCustomOrder', 'components.form.live-select-custom-order', ['name', 'values' => null, 'selected' => null, 'attributes' => [], 'extra' =>[]]);
        Form::component('bsLiveSelectHTML', 'components.form.live-select-html', ['name', 'values' => null, 'selected' => null, 'attributes' => [], 'extra' =>[]]);
        Form::component('bsNumber', 'components.form.number', ['name', 'value' => null, 'attributes' => [], 'extra' =>[]]);
        Form::component('bsPassword', 'components.form.password', ['name', 'attributes' => [], 'extra' =>[]]);
        Form::component('bsSelect', 'components.form.select', ['name', 'values' => null, 'selected' => null, 'attributes' => [], 'extra' =>[]]);
        Form::component('bsText', 'components.form.text', ['name', 'value' => null, 'attributes' => [], 'extra' =>[]]);
        Form::component('bsTextArea', 'components.form.textarea', ['name', 'value' => null, 'attributes' => [], 'extra' =>[]]);
        Form::component('bsTextEditor', 'components.form.texteditor', ['name', 'value' => null, 'attributes' => [], 'extra' =>[]]);
        Form::component('bsUpload', 'components.form.upload', ['name', 'value' => null, 'attributes' => [], 'extra' =>[]]);

        //read only
        Form::component('fCheckbox', 'components.field.checkbox',  ['array'], []);
        Form::component('fText', 'components.field.text',  ['text', 'value'], []);

        // Specific
        Form::component('bsParameterOption', 'Users::parameters.parameter-option', ['name', 'value' => null, 'attributes' => [], 'extras' => []]);
    }
}
