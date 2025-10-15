@section('title',__('Calendário'))
@extends('layouts.backoffice')

@section('content')

<!-- Calendar filter -->
<div class="content-panel">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">@lang('Calendario')</h1>
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
                <div class="col-6">
                <label>Ano Lectivo</label>
                    @php
                        $ano = [
                            '1º Semestre 2017 - 2018',
                            '2º Semestre 2017 - 2018',
                            '1º Semestre 2018 - 2019',
                            '2º Semestre 2018 - 2019'
                ];
                    @endphp
                    {{Form::select(
                        $name = 'ano_lectivo',
                        $values = $ano,
                        1,['data-live-search' => 'true','class' => 'selectpicker form-control form-control-sm']

                )}}
                </div>
                <div class="col-6">
                        <label>Mês</label>
                            @php
                                $mes = [
                                    'Janeiro',
                                    'Fevereiro',
                                    'Março',
                                    'Abril',
                                    'Maio',
                                    'Junho',
                                    'Julho',
                                    'Agosto',
                                    'Setembro',
                                    'Outubro',
                                    'Novembro',
                                    'Dezembro',
                                ];

                            @endphp
                            {{Form::select(
                                $name = 'mes',
                                $values = $mes,
                                1,['data-live-search' => 'true','class' => 'selectpicker form-control form-control-sm']

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
                        <h1 class="m-0 text-dark">Junho</h1>
                        <table class='calendar'>
                            <thead>
                                <tr>
                                    <th>
                                        <span>Segunda-feira</span>
                                    </th>
                                    <th>
                                        <span>Terça-feira</span>
                                    </th>
                                    <th>
                                        <span>Quarta-feira</span>
                                    </th>
                                    <th>
                                        <span>Quinta-feira</span>
                                    </th>
                                    <th>
                                        <span>Sexta-feira</span>
                                    </th>
                                    <th>
                                        <span>Sábado</span>
                                    </th>
                                    <th>
                                        <span>Domingo</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>

                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td><label>1</label></td>
                                        <td><label>2</label></td>
                                        <td><label>3</label></td>
                                    </tr>
                                    <tr>
                                        <td><label>4</label></td>
                                        <td><label>5</label><div class="content"><strong>MARN</strong><span>Prova escrita final</span><span>18h</span></div></td>
                                        <td><label>6</label></td>
                                        <td><label>7</label></td>
                                        <td><label>8</label></td>
                                        <td><label>9</label></td>
                                        <td><label>10</label></td>
                                    </tr>
                                    <tr>
                                        <td><label>11</label></td>
                                        <td><label>12</label></td>
                                        <td><label>13</label></td>
                                        <td><label>14</label></td>
                                        <td><label>15</label></td>
                                        <td><label>16</label></td>
                                        <td><label>17</label></td>
                                    </tr>
                                    <tr>
                                        <td><label>18</label><div class="content"><strong>MARN</strong><span>Aula prática</span><span>11h</span></div></td>
                                        <td><label>19</label></td>
                                        <td><label>20</label></td>
                                        <td><label>21</label></td>
                                        <td><label>22</label><div class="content"><strong>EXAME</strong><span>Exame escrito</span><span>10h30m</span></div></td>
                                        <td><label>23</label></td>
                                        <td><label>24</label></td>
                                    </tr>
                                    <tr>
                                        <td><label>25</label><div class="content"><strong>MARN</strong><span>Reunião</span><span>16h</span></div></td>
                                        <td><label>26</label></td>
                                        <td><label>27</label></td>
                                        <td><label>28</label></td>
                                        <td><label>29</label></td>
                                        <td><label>30</label></td>
                                        <td></td>
                                    </tr>

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
