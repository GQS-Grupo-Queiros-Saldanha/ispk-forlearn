@section('title', __('Exibir NEA'))
@extends('layouts.backoffice')

@section('styles')
    @parent
@endsection

@section('content')

    <div class="content-panel" style="padding: 0;">
        @include('Avaliations::avaliacao.navbar')
        <div class="content-header">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-sm-12">
                        <div class=" float-right">
                            <ol class="breadcrumb float-rigth" style="padding-top: 4px; padding-bottom: 0px;">
                                <li class="breadcrumb-item"><a href="{{ route('panel_avaliation') }}">Avaliações</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Exibir notas de exame de acesso</li>
                            </ol>
                        </div>
                    </div>
                </div>

                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>@lang('Exibir notas de exame de acesso')</h1>
                    </div>

                    <div class="col-sm-6">

                    </div>

                </div>
            </div>
        </div>

        {{-- Main content --}}
        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-12">

                        {{-- INCLUI O MENU DE BOTÕES --}}
                        {{-- @include('Avaliations::avaliacao.show-panel-avaliation-button') --}}

                        @if (auth()->user()->can('view-grades-others'))
                            <div class="card" style="margin-left: 5px;">

                                <div class="row">
                                <div class="col-6">
                                <div class="form-group col"> 
                                    <label>Estudante</label>
                                    {{ Form::bsLiveSelect('user', $users, null, ['required']) }}
                                </div>
                                </div>
                                </div>
                            </div>
                        @endif
                    </div>
                    </div>
                    

                    <div class="card">
                        <div class="card-body">
                            
                            <table id="grades-table" class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>@lang('GA::courses.course')</th>
                                        <th>@lang('GA::disciplines.discipline')</th>
                                        <th>@lang('Grades::grades.student')</th>
                                        <th>@lang('Grades::grades.grade')</th>
                                        <th>@lang('common.created_by')</th>
                                        <th>@lang('common.updated_by')</th>
                                        <th>@lang('common.created_at')</th>
                                        <th>@lang('common.updated_at')</th>
                                    </tr>
                                </thead>
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
        var cookies = document.cookie;

        var nova = cookies.split(";");

        if (nova[0] == "tela=cheia") {



            $(".left-side-menu,.top-bar").hide();
            $(".btn-logout").show();

            $(".content-wrapper").css({
                margin: '0 auto',
                marginTop: '0px',
                position: 'absolute',
                left: '0',
                top: '0',
                padding: '0',
                width: '100%'
            });

            $(".content-panel").css({
                marginTop: '0px'
            });
        }

        $(".tirar").click(function() {

            var cookies = document.cookie;

            var nova = cookies.split(";");

            if (nova[0] == "tela=cheia") {


                $(".left-side-menu,.top-bar").show();
                $(".btn-logout").hide();
                $(".content-wrapper").css({
                    // margin: '0 auto',
                    // marginTop: '0px',  
                    position: 'absolute',
                    left: '370px',
                    top: '84px',
                    padding: '20px',
                    width: 'calc(100% - 370px)'
                });

                $(".content-panel").css({
                    marginTop: '14px'
                });


                document.cookie = "tela=normal";

            } else if (nova[0] == "tela=normal") {

                $(".btn-logout").show();
                $(".left-side-menu,.top-bar").hide();

                $(".content-wrapper").css({
                    margin: '0 auto',
                    marginTop: '0px',
                    position: 'absolute',
                    left: '0',
                    top: '0',
                    padding: '0',
                    width: '100%'
                });

                $(".content-panel").css({
                    marginTop: '0px'
                });
                document.cookie = "tela=cheia";

            } else {
                document.cookie = "tela=cheia";
            }

        });

        var dataTableBaseUrl = '{{ route('grade_student.ajax', 0) }}'.slice(0, -1);
        var dataTablePayments = null;

        $(function() {
            dataTablePayments = $('#grades-table').DataTable({
                ajax: dataTableBaseUrl + '{!! auth()->user()->id !!}',
                buttons: [
                    'colvis',
                    'excel'
                ],
                columns: [{
                    data: 'course',
                    name: 'ct.display_name',
                    visible: false
                }, {
                    data: 'discipline',
                    name: 'dt.display_name',
                }, {
                    data: 'student',
                    name: 'u0.name',
                    visible: false
                }, {
                    data: 'value',
                    name: 'value',
                }, {
                    data: 'created_by',
                    name: 'u1.name',
                    visible: false
                }, {
                    data: 'updated_by',
                    name: 'u2.name',
                    visible: false
                }, {
                    data: 'created_at',
                    name: 'created_at',
                    visible: false
                }, {
                    data: 'updated_at',
                    name: 'updated_at',
                    visible: false
                }],
                language: {
                    url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}'
                }
            });

            @if (auth()->user()->can('view-grades-others'))
                var selectUser = $('#user');

                function switchDataOnDataTable(element) {
                    dataTablePayments.ajax.url(dataTableBaseUrl + parseInt(element.value)).load();
                }

                if (!$.isEmptyObject(selectUser)) {
                    switchDataOnDataTable(selectUser[0]);
                    selectUser.change(function() {
                        switchDataOnDataTable(this);
                    });
                }
            @endif
        });
    </script>
@endsection
