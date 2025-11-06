<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
 use App\Http\Controllers\Auth\LoginController;
 
Route::group([
    'prefix' => LaravelLocalization::setLocale(),
    'middleware' => [
        'web',
        'localeSessionRedirect',
        'localizationRedirect',
    ]], function () {

    Route::get('/', 'HomeController@index')->name('home');
    
    //emarq
        Route::get('/instituicao-arquivo/attachment/{filename}', 'InstituicaoArquivoController@getArquivo');
    //fim - emarq

    //Auth::routes();
    
    // Route::get('login', function(){
    //     return view('bloqueoSytem');
    // })->name('login');
    Route::post('login', 'Auth\LoginController@loginApi');
    Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
    Route::get('logout', 'Auth\LoginController@logout')->name('logout');
    //Route::get('/{ordem}', [LoginController::class, 'WhatsappChecked'])->name('criterio');
    Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail')->name('password.email');
    Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.request');
    Route::post('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm')->name('password.update');
    Route::get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password.reset');
    Route::get('register', 'Auth\RegisterController@showRegistrationForm')->name('register');
    Route::post('register', 'Auth\RegisterController@register');

    Route::get('calendar', 'CalendarController@index')->name('calendar');

    Route::get('candidaturas-inicio', 'CandidatureController@register')->name('candidaturas');
    Route::post('candidaturas-inicio', 'CandidatureController@submitData')->name('candidature.submitData');
    Route::get('/candidaturas/email_convert/{name}', 'CandidatureController@convertToEmail');

    Route::get('message-success/{id}', 'CandidatureController@callMessageSuccess')->name('message-success');
    Route::post('re-send-email', 'CandidatureController@reSendEmail')->name('reSend.email');
    Route::get('message-resend/{id}', 'CandidatureController@callMessageReSend')->name('message-resend');

    Route::get('candidaturas/candidate-login', 'CandidatureController@login');

    Route::post('candidate-login', 'CandidatureController@makeLogin')->name('candidate.login');
    Route::get('candidaturas/candidate-form/{id}', 'CandidatureController@candidateForm')->name('candidate.form');

    Route::post('candidaturas/candidate-form-send', 'CandidatureController@store')->name('candidate.store');


 //Rota do cadastro da Instituição
    Route::get('instituicao-pdf', 'InstitutionController@index')->name('institution.index');
    Route::get('instituicao-cadastro', 'InstitutionController@create')->name('institution.create');
    Route::post('instituicao-salvar', 'InstitutionController@store')->name('institution.store');
    Route::get('instituicao-mostrar', 'InstitutionController@show')->name('institution.show');
    Route::get('instituicao-editar', 'InstitutionController@edit')->name('institution.edit');
    Route::post('instituicao-atualizar', 'InstitutionController@update')->name('institution.update');
    Route::get('instituicao-apagar', 'InstitutionController@destroy')->name('institution.destroy');
    Route::get('instituicao-municipios', 'InstitutionController@getMunicipios')->name('institution.mun');



    Route::get('send-mail', function(){
        $data = ["name" => "user", "email" => "email"];
        Mail::send('emails.link-access', $data, function($info){
            $info->to('f.campos.gqs@gmail.com', 'Andre')->subject('Laravel email');
            $info->from('franciiscocampos170@gmail.com', 'Example');
        });

        return "Enviar e-mail!";



    });
});