<!--F4k3-->
@section('title',__('Avaliações'))
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
                        <h1 class="m-0 text-dark">@lang('Avaliações')</h1>
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

                                {!! Form::fText('Curso : ', 'Mestrado em Desenvolvimento e Cooperação Internacional - Desenvolvimento e Cooperação Internacional 2015') !!}

                                <div class="row">
                                    <div class="col-6">
                                        {!! Form::bsSelect('periodo', [1 => '1º semestre 2018/2019'], null, [], ['label' => 'Período de execução']) !!}
                                    </div>
                                    <div class="col-6">
                                        {!! Form::bsSelect('unidade_curricular', [0 => 'Todas'], null, [], ['label' => 'Unidade curricular']) !!}
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
                        <h1 class="m-0 text-dark">@lang('Plano curricular')</h1>
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

                                <table id="evaluations-table" class="table table-hover">
                                    <thead>
                                    <tr>
                                        <th>@lang('Data')</th>
                                        <th>@lang('Unidade curricular')</th>
                                        <th>@lang('Tipo de avaliação')</th>
                                        <th>@lang('Época')</th>
                                        <th>@lang('Nota')</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>{{ \Carbon\Carbon::now()->startOfWeek()->addWeek()->toDateString() }}</td>
                                        <td>Ciências Sociais e Desenvolvimento</td>
                                        <td>Final</td>
                                        <td>Normal</td>
                                        <td>-</td>
                                    </tr>
                                    <tr>
                                        <td>{{ \Carbon\Carbon::now()->startOfWeek()->addWeek()->addDays(2)->toDateString() }}</td>
                                        <td>Cooperação internacional para o Desenvolvimento</td>
                                        <td>Final</td>
                                        <td>Normal</td>
                                        <td>-</td>
                                    </tr>
                                    <tr>
                                        <td>{{ \Carbon\Carbon::now()->startOfWeek()->subWeek()->addDays(2)->toDateString() }}</td>
                                        <td>Demografia</td>
                                        <td>Final</td>
                                        <td>Normal</td>
                                        <td>5</td>
                                    </tr>
                                    <tr>
                                        <td>{{ \Carbon\Carbon::now()->startOfWeek()->subWeek()->addDays(4)->toDateString() }}</td>
                                        <td>Demografia</td>
                                        <td>Final</td>
                                        <td>Recurso</td>
                                        <td>11</td>
                                    </tr>
                                    <tr>
                                        <td>{{ \Carbon\Carbon::now()->startOfWeek()->subWeek()->toDateString() }}</td>
                                        <td>Economia e Política de Desenvolvimento</td>
                                        <td>Final</td>
                                        <td>Normal</td>
                                        <td>12</td>
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
            let table = $('#evaluations-table').DataTable({
                serverSide: false,
                processing: false,
                paging: false,
                buttons: [],
                language: {
                    url: '{{ asset('lang/datatables/'.App::getLocale().'.json') }}',
                }
            });
        });

        // Delete confirmation modal
        //Modal.confirm('{!! Request::fullUrl() !!}/', '{!! csrf_token() !!}');

    </script>
@endsection
