@section('title',__('Turmas'))
@extends('layouts.backoffice')

@section('content')

<!-- Calendar filter -->
<div class="content-panel">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">@lang('Avaliações')</h1>
                </div>
                <div class="col-sm-6">
                    {{-- {{ Breadcrumbs::render('profile') }} --}}
                </div>
            </div>
        </div>
    </div>

    {{-- Main content --}}
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <label>Curso</label>
                        @php
                            $curso = [
                                'Engenharia Informática',
                                'Engenharia do Ambiente',
                                'Engenharia Mecânica'
                    ];
                        @endphp
                        {{Form::select(
                            $name = 'curso',
                            $values = $curso,
                            0,['data-live-search' => 'true','class' => 'selectpicker form-control form-control-sm']

                    )}}
                </div>
                <div class="col-12">
                    <label>Unidade curricular</label>
                        @php
                            $unidades = [
                                'Análise matemática',
                                'Algebra Linear e geometria Analítica',
                                'Métodos numéricos'
                    ]   ;
                        @endphp
                        {{Form::select(
                            $name = 'unidades',
                            $values = $unidades,
                            0,['data-live-search' => 'true','class' => 'selectpicker form-control form-control-sm']

                        )}}
                    </div>
                    <div class="row" style="margin-top: 10px;">
                    <div class="col-12">
                        {{ Form::bsDate('data_definir', null, ['placeholder' => __('Data a definir'), '', 'required'], ['label' => __('Data a definir')]) }}
                    </div>
                </div>

            </div>

        </div>
    </div>
</div>

<!-- Calendar -->
<div class="content-panel" style="margin-bottom: 10px;">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">

                <div class="col-sm-6">
                </div>
            </div>
        </div>
    </div>

    {{-- Main content --}}
    <div class="content">
        <div class="container-fluid">
                <div class="col-12">
                        <h1 class="m-0 text-dark">Lista de avaliações</h1>
                        <table class="evaluations table">
                            <thead>
                                <tr>
                                    <th style="width: 100px;">
                                        <span>data</span>
                                    </th>
                                    <th style="width: 100px;">
                                        <span>unidade curricular</span>
                                    </th>
                                    <th style="width: 100px;">
                                        <span>epoca</span>
                                    </th>
                                    <th style="width: 100px;">
                                        <span>turma</span>
                                    </th>
                                    <th style="width: 100px;">
                                        <span>sala</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td>08-01-2018</td><td>Análise Matemática</td><td>Normal</td><td>Turma A</td><td>15</td></tr>
                                <tr><td>18-02-2018</td><td>Análise Matemática</td><td>Final</td><td>Turma A</td><td>Auditório</td></tr>
                                <tr><td>03-03-2018</td><td>Algebra Linear e geometria analítica</td><td>Normal</td><td>Turma A</td><td>14</td></tr>
                                <tr><td>03-03-2018</td><td>Métodos Numéricos</td><td>Normal</td><td>Turma A</td><td>15</td></tr>
                                <tr><td>23-03-2018</td><td>Métodos Numéricos</td><td>Final</td><td>Turma B</td><td>13</td></tr>
                                <tr><td>13-03-2018</td><td>Algebra Linear e geometria analítica</td><td>Final</td><td>Turma B</td><td>15</td></tr>
                            </tbody>
                        </table>
                    </div>
            <div class="row">
            </div>
        </div>
    </div>
</div>
<div style="float: right;">
<button type="submit" class="btn forlearn-btn add" >@icon('fas fa-edit')Editar</button>
<button type="submit" class="btn forlearn-btn add" ><i class="fas fa-print"></i>Imprimir</button>
</div>
@endsection
@section('script')
@parent


@endsection
