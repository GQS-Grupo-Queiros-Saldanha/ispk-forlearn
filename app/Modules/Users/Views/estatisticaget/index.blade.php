<title>Melhores Estudantes | forLEARN® by GQS</title>
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
    </div>
@endsection

@section('body')

    <h1>Ola</h1>
@endsection

@section('scripts-new')
<script>
  
</script>
    
@endsection