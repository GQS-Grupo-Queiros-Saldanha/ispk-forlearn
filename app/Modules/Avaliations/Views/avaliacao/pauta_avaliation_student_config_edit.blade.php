<title>Avaliações | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'Editar Limite de Propina')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('panel_avaliation') }}">Avaliações</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('pauta_student.config') }}">Limite de Propina</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Editar</li>
@endsection
@section('body')
    {!! Form::open(['route' => ['pauta_student_config.update']]) !!}
    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h5>@choice('common.error', $errors->count())</h5>
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <div class="row">
        <div class="col-4 p-2">
            <label>Quatidade de Messes</label>
            <input type="hidden" value="{{ $getAno->id }}" name="id_criterio">
            <select name="quantidade_mes" id="quantidade_mes" class="selectpicker form-control form-control-sm"
                style="width: 100%; !important">
                <option style="width: 100%;" value={{ $getAno->quantidade_mes }} selected>{{ $getAno->quantidade_mes }} </option>
                <option style="width: 100%;" value="1">1</option>
                <option style="width: 100%;" value="2">2</option>
                <option style="width: 100%;" value="3">3</option>
                <option style="width: 100%;" value="4">4</option>
                <option style="width: 100%;" value="5">5</option>
                <option style="width: 100%;" value="6">6</option>
                <option style="width: 100%;" value="7">7</option>
                <option style="width: 100%;" value="8">8</option>
                <option style="width: 100%;" value="9">9</option>
                <option style="width: 100%;" value="10">10</option>
                <option style="width: 100%;" value="11">11</option>
                <option style="width: 100%;" value="12">12</option>
                <option style="width: 100%;" value="13">13</option>
                <option style="width: 100%;" value="14">14</option>
                <option style="width: 100%;" value="15">15</option>
                <option style="width: 100%;" value="16">16</option>
                <option style="width: 100%;" value="17">17</option>
                <option style="width: 100%;" value="18">18</option>
                <option style="width: 100%;" value="19">19</option>
                <option style="width: 100%;" value="20">20</option>
                <option style="width: 100%;" value="21">21</option>
                <option style="width: 100%;" value="22">22</option>
                <option style="width: 100%;" value="23">23</option>
                <option style="width: 100%;" value="24">24</option>
                <option style="width: 100%;" value="25">25</option>
                <option style="width: 100%;" value="26">26</option>
                <option style="width: 100%;" value="27">27</option>
                <option style="width: 100%;" value="28">28</option>
                <option style="width: 100%;" value="29">29</option>
                <option style="width: 100%;" value="30">30</option>
                <option style="width: 100%;" value="31">31</option>
            </select>
        </div>
        <div class="col-4 p-2">
            <label>Quatidade de Dias</label>
            <select name="quatidade_day" id="quatidade_day" class="selectpicker form-control form-control-sm">
                <option style="width: 100%;" value={{ $getAno->quatidade_day }} selected>{{ $getAno->quatidade_day }}</option>
                <option style="width: 100%;" value="1">1</option>
                <option style="width: 100%;" value="2">2</option>
                <option style="width: 100%;" value="3">3</option>
                <option style="width: 100%;" value="4">4</option>
                <option style="width: 100%;" value="5">5</option>
                <option style="width: 100%;" value="6">6</option>
                <option style="width: 100%;" value="7">7</option>
                <option style="width: 100%;" value="8">8</option>
                <option style="width: 100%;" value="9">9</option>
                <option style="width: 100%;" value="10">10</option>
                <option style="width: 100%;" value="11">11</option>
                <option style="width: 100%;" value="12">12</option>
                <option style="width: 100%;" value="13">13</option>
                <option style="width: 100%;" value="14">14</option>
                <option style="width: 100%;" value="15">15</option>
                <option style="width: 100%;" value="16">16</option>
                <option style="width: 100%;" value="17">17</option>
                <option style="width: 100%;" value="18">18</option>
                <option style="width: 100%;" value="19">19</option>
                <option style="width: 100%;" value="20">20</option>
                <option style="width: 100%;" value="21">21</option>
                <option style="width: 100%;" value="22">22</option>
                <option style="width: 100%;" value="23">23</option>
                <option style="width: 100%;" value="24">24</option>
                <option style="width: 100%;" value="25">25</option>
                <option style="width: 100%;" value="26">26</option>
                <option style="width: 100%;" value="27">27</option>
                <option style="width: 100%;" value="28">28</option>
                <option style="width: 100%;" value="29">29</option>
                <option style="width: 100%;" value="30">30</option>
                <option style="width: 100%;" value="31">31</option>
            </select>
        </div>
        <div class="col-4 p-2">
            <label>Ano Lectivo</label>
            <select name="lective_year_id" id="lective_year_id" class="selectpicker form-control form-control-sm">
                @foreach ($lectiveYears as $lectiveYear)
                    <option value="{{ $lectiveYear->id }}" @if ($lectiveYearSelected == $lectiveYear->id) selected @endif>
                        {{ $lectiveYear->currentTranslation->display_name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>
    <button type="submit" class="btn btn-sm btn-success mt-2 mb-3">
        <i class="fas fa-plus-circle"></i>
        Editar
    </button>
    {!! Form::close() !!}
@endsection
