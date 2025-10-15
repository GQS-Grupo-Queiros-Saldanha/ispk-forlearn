<!--F4k3-->
@section('title',__('Atendimento'))
@extends('layouts.backoffice')

@section('styles')
    @parent
@endsection

@section('content')

    <div class="content-panel">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">@lang('Atendimento')</h1>
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

                                <table id="attendance-table" class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th>@lang('Senha')</th>
                                        <th>@lang('Serviços')</th>
                                        <th>@lang('Nº')</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>A</td>
                                        <td>1º Ciclo - Licenciatura</td>
                                        <td id="num-senha-a">7</td>
                                    </tr>
                                    <tr>
                                        <td>B</td>
                                        <td>2º Ciclo - Mestrados</td>
                                        <td id="num-senha-b">2</td>
                                    </tr>
                                    <tr>
                                        <td>C</td>
                                        <td>3º ciclo - Doutoramento e Pós-graduações</td>
                                        <td id="num-senha-c">0</td>
                                    </tr>
                                    <tr>
                                        <td>D</td>
                                        <td>Atendimento proritário</td>
                                        <td id="num-senha-d">0</td>
                                    </tr>
                                    <tr>
                                        <td>E</td>
                                        <td>Mobilidade / Erasmus</td>
                                        <td id="num-senha-e">0</td>
                                    </tr>
                                    <tr>
                                        <td>F</td>
                                        <td>Outros</td>
                                        <td id="num-senha-f">0</td>
                                    </tr>
                                    </tbody>
                                </table>

                                <div id="time" class="float-right"></div>

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
    <script>
        $(function () {
            Number.prototype.pad = function (len) {
                return (new Array(len+1).join("0") + this).slice(-len);
            };

            let $time = $('#time');
            let days = ["Domingo", "Segunda", "Terça", "Quarta", "Quinta", "Sexta", "Sábado"];
            let months = ["Janeiro", "Fevereiro", "Março", "Abril", "Maio", "Junho", "Julho", "Agosto", "Setembro", "Outubro", "Novembro", "Dezembro"];
            setInterval(function() {

                let now = new Date();
                let str = days[now.getDay()] + ", " + now.getDate() + " de " + months[now.getMonth()] + " de " + now.getFullYear() + " " + now.getHours().pad(2) +":" + now.getMinutes().pad(2) + ":" + now.getSeconds().pad(2);

                $time.html(str);
            }, 1)

        });

        // Delete confirmation modal
        //Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');
    </script>
@endsection
