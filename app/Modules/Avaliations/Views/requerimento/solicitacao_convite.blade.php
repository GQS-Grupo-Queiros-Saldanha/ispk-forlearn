@extends('layouts.generic_index_new', ['breadcrumb_super' => true])
@section('title', __('Solicitação de Convite'))

@section('page-title')
    @lang('Solicitação de Convite')
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('requerimento.index') }}">Requerimentos</a></li>
    <li class="breadcrumb-item active" aria-current="page">Convite</li>
@endsection

@section('selects')
    <div class="mb-2 mt-3">
        <label for="lective_year">Selecione o ano lectivo</label>
        <select name="lective_year" id="lective_year" class="selectpicker form-control form-control-sm" style="width: 100%; !important">
            @foreach ($lectiveYears as $lectiveYear)
                @if ($lectiveYearSelected == $lectiveYear->id)
                    <option value="{{ $lectiveYear->id }}" selected>
                        {{ $lectiveYear->currentTranslation->display_name }}
                    </option>
                @else
                    <option value="{{ $lectiveYear->id }}">
                        {{ $lectiveYear->currentTranslation->display_name }}
                    </option>
                @endif
            @endforeach
        </select>
    </div>
@endsection

@section('body')
    <form action="{{ route('requerimento.solicitacao_revisao_prova_store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col">
                <div class="card">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group col">
                                <label>Estudante</label>
                                <select name="student_id" id="students" class="selectpicker form-control form-control-sm" data-live-search="true">
                                    <option value="" selected>Selecione o Estudante</option>
                                    <!--Colocado pelo JS-->
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <hr>
                <div class="float-right">
                    <button type="submit" class="btn btn-success mb-3">
                        <i class="fas fa-plus-circle"></i>Requerer
                    </button>
                </div>
            </div>
        </div>
    </form>
  
@endsection

@section('scripts')
    @parent
<script>

</script>

@endsection