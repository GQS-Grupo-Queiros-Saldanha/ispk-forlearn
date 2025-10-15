@section('title',__('Turmas'))
@extends('layouts.backoffice')

@section('content')

<!-- Calendar filter -->
<div class="content-panel">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">@lang('Turmas')</h1>
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
                <div class="col-8">
                <label>Turma</label>
                    @php
                        $turma = [
                            'Turma A',
                            'Turma B',
                            'Turma C',
                            'Turma D'
                ];
                    @endphp
                    {{Form::select(
                        $name = 'turma',
                        $values = $turma,
                        0,['data-live-search' => 'true','class' => 'selectpicker form-control form-control-sm']

                )}}
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
                        <h1 class="m-0 text-dark">Turma A</h1>
                        <table class="turma table">
                            <thead>
                                <tr>
                                    <th>
                                        <span>Nº de aluno</span>
                                    </th>
                                    <th>
                                        <span>nome do aluno</span>
                                    </th>
                                    <th>
                                        <span>email</span>
                                    </th>
                                    <th>
                                        <span>contacto</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr><td>000001</td><td>Alicia Franca</td><td>test@test.pt</td><td>12341234123</td></tr>
                                <tr><td>000002</td><td>Anita Vázquez</td><td>test@test.pt</td><td>12341234123</td></tr>
                                <tr><td>000003</td><td>Roseli Aveiro</td><td>test@test.pt</td><td>12341234123</td></tr>
                                <tr><td>000006</td><td>Bibiana Lustosa</td><td>test@test.pt</td><td>12341234123</td></tr>
                                <tr><td>000012</td><td>Levi Villaverde</td><td>test@test.pt</td><td>12341234123</td></tr>
                                <tr><td>000023</td><td>Jutaí Homem</td><td>test@test.pt</td><td>12341234123</td></tr>
                                <tr><td>000024</td><td>Matias Carrasco</td><td>test@test.pt</td><td>12341234123</td></tr>
                                <tr><td>000055</td><td>Romano Delgado</td><td>test@test.pt</td><td>12341234123</td></tr>
                                <tr><td>000047</td><td>Sandoval Peçanha</td><td>test@test.pt</td><td>12341234123</td></tr>
                                <tr><td>000019</td><td>Dalila Figueroa</td><td>test@test.pt</td><td>12341234123</td></tr>
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
