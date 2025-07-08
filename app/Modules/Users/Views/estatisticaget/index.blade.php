<title> Estatistica de pagamento | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'Estatistica de pagamento')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/">Home</a></li>
    <li class="breadcrumb-item"><a href="{{ route('panel_avaliation') }}">****</a></li>
    <li class="breadcrumb-item active" aria-current="page">*******</li>
@endsection

@section('styles-new')
    @parent
    <link rel="stylesheet" href="{{ asset('css/new_table_panel.css') }}" />
    <link rel="stylesheet" href="{{ asset('css/new_switcher.css') }}">
@endsection

@section('selects')
    <div class="mb-2">
        <label for="lective_year">Ano lectivo</label>
        <select name="lective_year" id="lective_year" class="form-control form-control-sm">
        {{--@foreach ($lectiveYears as $lectiveYear)
            <option value="{{ $lectiveYear->id }}" @if ($lectiveYearSelected == $lectiveYear->id) selected @endif>
                {{ $lectiveYear->currentTranslation->display_name }}
            </option>
        @endforeach--}}
        </select>
    </div>
@endsection

@section('body')

    <div class="row">
        <div class="d-flex justify-content-between w-100">
            <!-- Curso -->
            <div class="d-flex flex-column me-3" style="flex: 1;">
                <label for="selectorCurso">Curso</label>
                <select class="form-control form-control-sm" name="curso" id="selectorCurso">
                    <option selected>Selecione o Curso</option>
                    {{--@foreach ($courses as $c)
                        <option value="{{ $c->id }}">{{ $c->currentTranslation->display_name }}</option>
                    @endforeach--}}
                </select>
            </div>

            <!-- Turma -->
            <div class="d-flex flex-column ms-3" style="flex: 1;">
                <label for="selectorTurma">Turma</label>
                <select class="form-control form-control-sm" name="turma" id="selectorTurma">
                    <option selected>Escolha a turma</option>
                    <!-- JS vai preencher -->
                </select>
            </div>
        </div>
    </div>

    <div class="card mr-1">
        <h4>Ranking</h4>
        <table class="table table_pauta table-hover dark">
            <thead class="table_pauta">
                <tr>
                    <th>#</th>
                    <th>Turma</th>
                    <th>Estudante</th>
                    <th>Nota Final</th>
                </tr>
            </thead>
            <tbody id="students-table">
                <!-- Coloquei pelo JS -->
                <div class="alert alert-primary d-none" id="alert" role="alert" >
                    Não foram encontrados alunos com média maior ou igual a 14.
                  </div>
            </tbody>
        </table>
    </div>

    <div class="d-flex mb-2 gap-3">
        <button id="pdf" class="btn btn-success">
            <i class="fas fa-file-pdf"></i> Gerar PDF
        </button>
        <!--<button type="submit" class="btn btn-dark">
            <i class="fas fa-file-pdf"></i> Sem ação
        </button>-->
    </div>
@endsection

@section('scripts-new')
<script>
  
</script>
    
@endsection