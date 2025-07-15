@extends('layouts.print')

@section('page-title', 'Estatística de pagamento')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="/">Home</a></li>
    <li class="breadcrumb-item active">Estatística de pagamento</li>
@endsection

@section('styles-new')
    @parent
    <style>
        .table-responsive { border-radius: 0.25rem;}
        .table thead th {white-space: nowrap; vertical-align: middle;}
        .table tbody td { vertical-align: middle;}
    </style>
@endsection

@section('selects')
    <form method="GET" action="{{ route('estatistica.pagamento') }}">
        <div class="mb-3">
            <label for="lective_year" class="form-label">Ano lectivo</label>
            <select name="lective_year" id="lective_year" class="form-select form-select-sm" onchange="this.form.submit()">
                @foreach ($lectiveYears as $lectiveYear)
                    <option value="{{ $lectiveYear->id }}" @if ($lectiveYearSelected == $lectiveYear->id) selected @endif>
                        {{ $lectiveYear->currentTranslation->display_name }}
                    </option>
                @endforeach
            </select>
        </div>
    </form>
@endsection

@section('content')
    <div class="table-responsive border rounded">
        <table class="table table-bordered table-hover table-sm mb-0">
            <thead class="table-light">
                <tr>
                    <th rowspan="2" class="align-middle text-center bg-light">Curso</th>
                    @for ($ano = 1; $ano <= 5; $ano++)
                        <th colspan="5" class="text-center">{{ $ano }}º Ano</th>
                    @endfor
                </tr>
                <tr>
                    @for ($i = 1; $i <= 5; $i++)
                        <th class="text-center">M</th>
                        <th class="text-center">T</th>
                        <th class="text-center">N</th>
                        <th class="text-center">Prot.</th>
                        <th class="text-center">Total</th>
                    @endfor
                </tr>
            </thead>
            <tbody>
                @foreach ($courses as $c)
                    <tr>
                        <td class="fw-semibold bg-light">{{ $c->code }}</td>
                        @for ($ano = 1; $ano <= 5; $ano++)
                            <td class="text-center">{{ $estatisticas[$c->id][$ano]['M'] }}</td>
                            <td class="text-center">{{ $estatisticas[$c->id][$ano]['T'] }}</td>
                            <td class="text-center">{{ $estatisticas[$c->id][$ano]['N'] }}</td>
                            <td class="text-center">{{ $estatisticas[$c->id][$ano]['PT'] }}</td>
                            <td class="text-center">{{ $estatisticas[$c->id][$ano]['total'] }}</td>
                        @endfor
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection

