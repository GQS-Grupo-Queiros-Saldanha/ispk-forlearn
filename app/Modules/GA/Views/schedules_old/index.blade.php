<!--F4k3-->
@section('title',__('Horário'))
@extends('layouts.backoffice')

@section('styles')
    @parent
@endsection

@section('content')

<!-- Calendar filter -->
<div class="content-panel">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">@lang('Horários')</h1>
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
                        <label>Ano letivo</label>
                            @php
                                $ano = [
                                    '2016/2017',
                                    '2017/2018',
                                    '2018/2019',
                        ];
                            @endphp
                            {{Form::select(
                                $name = 'ano',
                                $values = $ano,
                                0,['data-live-search' => 'true','class' => 'selectpicker form-control form-control-sm']

                        )}}
                    </div>
                    <div class="col-6">
                        <label>Semestre</label>
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
                            {{ Form::bsDate('Data a definir', null, ['placeholder' => __('Data a definir'), '', 'required'], ['label' => __('Data a definir')]) }}
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <div class="content-panel">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0 text-dark">@lang('Horário')</h1>
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

                                <table id="optional-groups-table" class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th>@lang('Dia')</th>
                                        <th>@lang('08h')</th>
                                        <th>@lang('09h')</th>
                                        <th>@lang('10h')</th>
                                        <th>@lang('11h')</th>
                                        <th>@lang('12h')</th>
                                        <th>@lang('13h')</th>
                                        <th>@lang('14h')</th>
                                        <th>@lang('15h')</th>
                                        <th>@lang('16h')</th>
                                        <th>@lang('17h')</th>
                                        <th>@lang('18h')</th>
                                        <th>@lang('19h')</th>
                                        <th>@lang('20h')</th>
                                        <th>@lang('21h')</th>
                                        <th>@lang('22h')</th>
                                        <th>@lang('23h')</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>@lang('Segunda-Feira')</td>
                                        <td></td>
                                        <td class="bg-dark text-light rounded" colspan="3">MAT II <span class="text-muted">(TP)</span></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td class="bg-dark text-light rounded" colspan="3">MAT II <span class="text-muted">(TP)</span></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>@lang('Terça-Feira')</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>@lang('Quarta-Feira')</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td class="bg-dark text-light rounded" colspan="4">MAT I <span class="text-muted">(TP)</span></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>@lang('Quinta-Feira')</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>@lang('Sexta-Feira')</td>
                                        <td class="bg-dark text-light rounded" colspan="2">LABC I <span class="text-muted">(T)</span></td>
                                        <td class="bg-dark text-light rounded" colspan="2">LABC I <span class="text-muted">(P)</span></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>@lang('Sábado')</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
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
    <script>
        $(function () {
            $('#optional-groups-table').DataTable({
                serverSide: false,
                processing: false,
                paging: false,
                ordering: false,
                buttons: false,
                language: {
                    url: '{{ asset('lang/datatables/'.App::getLocale().'.json') }}',
                }
            });
        });

        // Delete confirmation modal
        //Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');
    </script>
@endsection
