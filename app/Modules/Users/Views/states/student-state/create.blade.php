<title>Matrículas | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'Estado do Estudante')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('matriculations.index') }}">Matrículas</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('states.matriculation') }}">Estado dos Matriculados</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Novo</li>
@endsection
@section('body')
    <div class="card-body">
        {!! Form::open(['route' => ['student_state.store']]) !!}
        <div class="row">
            <div class="form-group col-sm-5">
                <label for="name" class="col-form-label">Nome completo:</label>
                {{ Form::bsLiveSelect('user_id', $students, old('user_id') ?: null, ['placeholder' => '', 'id' => 'students']) }}

            </div>
            <div class="form-group col-sm-5">
                <label for="state" class="col-form-label">Estado:</label>
                {{ Form::bsLiveSelectEmpty('state_id', [], old('state_id') ?: null, ['placeholder' => '', 'id' => 'states', 'disabled']) }}
            </div>
            <div class="form-group col-sm-2">
                <label for="" class="col-form-label"></label>
                <button type="submit" id="submit" class="btn btn-success mt-4" style="">
                    @icon('fas fa-plus-square') Salvar
                </button>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
@endsection
@section('models')
    @include('layouts.backoffice.modal_confirm')
@endsection
@section('scripts-new')
    @parent
    <script>
        (() => {

            let cookies = document.cookie;

            let nova = cookies.split(";");

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

            function getStudentState(student_id) {
                let url = "{{ route('states_by_id', ':student_id') }}";
                url = url.replace(':student_id', student_id);
                $.ajax({
                    url: url
                }).done(function(response) {
                    let states = response.states;
                    let student_state = response.student;
                    if (states.length) {
                        states.forEach(function(state) {
                            if (student_state != "") {
                                if (state.id == student_state[0].id) {
                                    $("#states").append('<option selected="true" value="' + state.id +
                                        '">' + state.display_name + '</option>');
                                } else {
                                    $("#states").append('<option value="' + state.id + '">' + state
                                        .display_name + '</option>');
                                }
                                $("#states").prop('disabled', false);
                                $("#states").selectpicker('refresh');
                            } else {
                                $("#states").append('<option value="' + state.id + '">' + state
                                    .display_name + '</option>');
                                $("#states").prop('disabled', false);
                                $("#states").selectpicker('refresh');
                            }
                        })

                    }
                })
            }

            $("#students").change(function() {
                let student_id = $("#students").children("option:selected").val();
                $("#states").prop('disabled', true);
                $("#states").empty();
                getStudentState(student_id);
            })

            $(".tirar").click(function() {

                let cookies = document.cookie;
                let nova = cookies.split(";");

                if (nova[0] == "tela=cheia") {

                    $(".left-side-menu,.top-bar").show();
                    $(".btn-logout").hide();
                    $(".content-wrapper").css({
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

        })();
    </script>
@endsection
