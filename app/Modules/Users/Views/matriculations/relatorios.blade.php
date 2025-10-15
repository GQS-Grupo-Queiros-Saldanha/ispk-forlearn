@extends('layouts.generic_index_new')
<title>Estatísticas de confirmação de matrículas</title>
@section('page-title', 'Estatísticas de confirmação de matrículas')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('matriculations.index') }}">Matrículas</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Relatórios</li>
@endsection


{!! Form::open(['route' => ['matriculas.relatorios.pdf'], 'method' => 'post', 'required' => 'required', 'target' => '_blank']) !!}
@section('selects')
<br>
<br>
    <div class="mb-2">
        <label for="lective_year">Selecione o ano lectivo</label>
        <select name="lective_year" id="lective_year" class="selectpicker form-control form-control-sm">
            <option selected value="" data-terminado="1">Seleciona o ano lectivo</option>
            @foreach ($lectiveYears as $lectiveYear)
                <option value="{{ $lectiveYear->id }}" @if ($lectiveYearSelected == $lectiveYear->id) selected @endif
                    data-terminado="{{ $lectiveYear->is_termina }}">
                    {{ $lectiveYear->currentTranslation->display_name }}
                </option>
            @endforeach
        </select>
    </div>
@endsection
@section('body')
    <div class="row">
        <div class="col">
            @csrf
            @method('post')
           
            <input type="hidden" name="AnoLectivo" value="" id="Ano_lectivo_foi">
        </div>
        <br><br>
        <div class="col-12 justify-content-md-end">
            <div class="form-group col-4  justify-content-md-end" style="float:right;">
                <button type="submit" id="btn-listar" class="btn btn-primary  float-end" target="_blank"
                    style="width:180px;">
                    <i class="fas fa-file-pdf"></i>
                    Gerar PDF
                </button>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
@endsection
@section('models')
    @include('layouts.backoffice.modal_confirm')
@endsection

