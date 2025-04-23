<!--F4k3-->
@section('title',__('Pedidos de requerimento'))
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
                        <h1 class="m-0 text-dark">@lang('Pedidos de requerimento')</h1>
                        <span class="text-muted">em construção...</span>
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
                                    .col {
                                        font-size: 14px;
                                    }
                                    .col a {
                                        display: flex;
                                        justify-content: center;
                                        align-items: center;
                                    }
                                    .col i {
                                        margin-right: 20px;
                                        font-size: 43px;
                                    }
                                </style>

                                <div class="row">
                                    <div class="col">
                                        <a href="#">
                                            @icon('fas fa-file-alt') DECLARAÇÕES
                                        </a>
                                    </div>
                                    <div class="col">
                                        <a href="#">
                                            @icon('fas fa-stamp') CERTIDÕES
                                        </a>
                                    </div>
                                    <div class="col">
                                        <a href="#">
                                            @icon('fas fa-graduation-cap') MELHORAMENTO DE NOTA
                                        </a>
                                    </div>
                                    <div class="col">
                                        <a href="#">
                                            @icon('fas fa-info-circle') OUTRAS INFORMAÇÕES
                                        </a>
                                    </div>
                                </div>
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
