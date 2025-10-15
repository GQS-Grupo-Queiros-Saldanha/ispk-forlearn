<!--F4k3-->
@section('title',__('Calendário'))
@extends('layouts.backoffice')

@section('styles')
    @parent
@endsection

@section('content')

    <!-- Avaliações -->
    <div class="content-panel">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">@lang('Calendário')</h1>
                    </div>
                    <div class="col-sm-6">
                        {{-- Breadcrumbs::render('optional-groups') --}}
                    </div>
                </div>
            </div>
        </div>

        {{-- Main content --}}
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">

                        {{--<a href="{{ route('optional-groups.create') }}" class="btn btn-primary btn-sm mb-3">
                            @icon('fas fa-plus-square')
                            @lang('common.new')
                        </a>--}}

                        <div class="card">
                            <div class="card-body">

                                <div class="row">
                                    <div class="col-6">
                                        {!! Form::bsSelect('ano_letivo',[1 => '2018/2019'], null, [], ['label' => 'Ano letivo']) !!}
                                    </div>
                                    <div class="col-6">
                                        {!! Form::bsSelect('mes', [0 => 'Maio'], null, [], ['label' => 'Mês']) !!}
                                    </div>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendário -->
    <div class="content-panel">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">@lang('Maio')</h1>
                    </div>
                    <div class="col-sm-6">
                        {{-- Breadcrumbs::render('optional-groups') --}}
                    </div>
                </div>
            </div>
        </div>

        {{-- Main content --}}
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">

                        {{--<a href="{{ route('optional-groups.create') }}" class="btn btn-primary btn-sm mb-3">
                            @icon('fas fa-plus-square')
                            @lang('common.new')
                        </a>--}}

                        <div class="card">
                            <div class="card-body">

                                <style>
                                .table th {
                                    text-align: center;
                                }
                                .table td {
                                    position: relative;
                                    width: 60px;
                                    height: 80px;
                                    color: #E5E5E5;
                                    font-size: 50px;
                                    text-align: center;
                                    vertical-align: middle;
                                }
                                .table td span {
                                    display: flex;
                                    align-items: center;
                                    justify-content: center;
                                    flex-direction: column;
                                    position: absolute;
                                    top: 0;
                                    left: 0;
                                    width: 100%;
                                    height: 100%;
                                    color: #000;
                                    font-size: 12px;
                                }
                                </style>

                                <table id="evaluations-table" class="table">
                                    <thead>
                                    <tr>
                                        <th>@lang('Segunda-Feira')</th>
                                        <th>@lang('Terça-Feira')</th>
                                        <th>@lang('Quarta-Feira')</th>
                                        <th>@lang('Quinta-Feira')</th>
                                        <th>@lang('Sexta-Feira')</th>
                                        <th>@lang('Sábado')</th>
                                        <th>@lang('Domingo')</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td>1</td>
                                        <td>2</td>
                                        <td>3</td>
                                        <td>4</td>
                                    </tr>
                                    <tr>
                                        <td>5</td>
                                        <td>6</td>
                                        <td>7</td>
                                        <td>8</td>
                                        <td>9</td>
                                        <td>10</td>
                                        <td>11</td>
                                    </tr>
                                    <tr>
                                        <td>12</td>
                                        <td>13</td>
                                        <td>14</td>
                                        <td>
                                            15
                                            <span>
                                                <b>GDFI</b>
                                                Prova Escrita Final<br>
                                                18h
                                            </span>
                                        </td>
                                        <td>16</td>
                                        <td>17</td>
                                        <td>18</td>
                                    </tr>
                                    <tr>
                                        <td>
                                            19
                                            <span>
                                                <b>MIAD</b>
                                                Prova Escrita Final<br>
                                                09:30h
                                            </span>
                                        </td>
                                        <td>20</td>
                                        <td>21</td>
                                        <td>22</td>
                                        <td>23</td>
                                        <td>24</td>
                                        <td>25</td>
                                    </tr>
                                    <tr>
                                        <td>26</td>
                                        <td>27</td>
                                        <td>28</td>
                                        <td>29</td>
                                        <td>30</td>
                                        <td>31</td>
                                        <td></td>
                                    </tr>
                                    </tbody>
                                </table>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- modal confirm --}}
    @include('layouts.backoffice.modal_confirm')

@endsection

@section('scripts')
    @parent
@endsection
