<title>Matrículas | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title')
    @lang('DISCIPLINA EQUIVALÊNCIA') - {{ mb_strtoupper($dados_geral->course) }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('matriculations.index') }}">Matrículas</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('matriculations-equivalence.index') }}">Equivalência</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Estudante disciplina(s)</li>
@endsection
@section('styles-new')
    <style>
        .red {
            background-color: red !important;
        }

        .dt-buttons {
            float: left;
            margin-bottom: 20px;
        }

        .dataTables_filter label {
            float: right;
        }


        .dataTables_length label {
            margin-left: 10px;
        }

        .div-anolectivo {
            width: 300px;
            padding-top: 16px;
            padding-right: 0px;
            margin-right: 15px;
        }

        table,
        th,
        td {
            padding: 10px;
            border: 1px solid black;
            border-collapse: collapse;
        }
    </style>
@endsection
@section('body')
    @php
        $disciplines = disciplinesSelect([$dados_geral->course_id], null);
        $flag = false;
    @endphp
    <form action="{{ route('equivalence_student.store') }}" method="POST">
        @method('POST')
        @csrf
        <div class="">
            <div class="col">
                @if ($errors->any())
                    <div class="alert alert-danger alert-dismissible">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">
                            ×
                        </button>
                        <h5>@choice('common.error', $errors->count())</h5>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="">
                    <div class="row">
                        <div class="col-5">
                            <div class="form-group">
                                <label>Estudante</label>
                                <input type="text" value="{{ $dados_geral->student }}" readonly class="form-control">
                            </div>
                        </div>
                        <div class="col-5">
                            <div class="form-group">
                                <label>Email</label>
                                <input type="text" value="{{ $dados_geral->email }}" readonly class="form-control">
                            </div>
                        </div>
                        <div class="col-2">
                            <a id="group2" href="#" class="btn btn-primary ml-2 mt-3 EditarBTN">
                                <i class="fas fa-edit"></i>
                                Editar
                            </a>
                        </div>
                    </div>
                    <input type="hidden" class="form-control" value="{{ $dados_geral->id }}" name="users_transf_id"
                        readonly>
                    <input type="hidden" class="form-control" value="{{ $dados_geral->id_usuario }}" name="user_data"
                        readonly>
                    <input type="hidden" class="form-control" value="{{ $dados_geral->lectiveYear }}" name="anoLective"
                        readonly id="anoLective">
                    <input type="hidden" id="lectiveY" value="" name="anoLectivo">
                    <div class="row">
                        <div class="col-12 text-secondary">
                            <h4>Disciplinas</h4>
                            @foreach ($disciplines as $disc)
                                @foreach ($dados_discipline as $minha_disci)
                                    @if ($disc->id == $minha_disci->id_discipline_equivalence)
                                        @php
                                            $flag = true;
                                        @endphp
                                    @endif
                                @endforeach
                                <div style="padding: 4px;">
                                    <input type="checkbox" name="equivalence_disciplina[]" value="{{ $disc->id }}"
                                        disabled {{ $flag == true ? 'checked' : '' }} class="disciplinas_check">
                                    #{{ $disc->code }} - {{ $disc->currentTranslation['display_name'] }}
                                    <br>
                                </div>
                                @php
                                    $flag = false;
                                @endphp
                            @endforeach
                        </div>
                    </div>
                </div>
                <hr>
                <div class="float-right" hidden id="group_btnSubmit">
                    <button type="submit" class="btn btn-success mb-3">
                        <i class="fas fa-plus-circle"></i>
                        Guardar
                    </button>
                </div>
            </div>
        </div>
    </form>
@endsection
@section('scripts-new')
    @parent
    <script>
        (() => {
            $(".EditarBTN").click(function() {
                $(".disciplinas_check").attr("disabled", false);
                $("#group_btnSubmit").attr("hidden", false);
            });
        })();
    </script>
@endsection
