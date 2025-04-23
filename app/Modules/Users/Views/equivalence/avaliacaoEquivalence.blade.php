<title>Avaliações | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'TRANSIÇÃO DE EQUIVALÊNCIA')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('panel_avaliation') }}">Avaliações</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Transição de equivalência</li>
@endsection
@section('styles-new')
    @parent
    <link rel="stylesheet" href="{{ asset('css/new_table_panel.css') }}" />
@endsection
@section('selects')
    <div class="mb-2">
        <label for="lective_years">Selecione o ano lectivo</label>
        <select name="lective_year" id="lective_year" class="selectpicker form-control form-control-sm">
            <option selected value="" data-terminado="1">Seleciona o ano lectivo</option>
            @foreach ($lectiveYears as $lectiveYear)
                <option value="{{ $lectiveYear->id }}" @if ($lectiveYearSelected == $lectiveYear->id) selected @endif>
                    {{ $lectiveYear->currentTranslation->display_name }}
                </option>
            @endforeach
        </select>
    </div>
@endsection
@section('body')
    <form action="{{ route('equivalence_student_grade.store') }}" method="POST">
        @method('POST')
        @csrf
        <div class="row">
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
            <div class="row">
                <diV class="col-6">
                    <label>Curso(s)</label>
                    {{ Form::bsLiveSelect('course', $courses, null, ['placeholder' => 'Selecione o curso', 'required' => 'required', 'id' => 'courseID']) }}
                </div>
                <div class="col-6">
                    <label>Estudante</label>
                    <select data-live-search="true" required name="Studants"
                        class="selectpicker form-control form-control-sm" id="studentID">
                    </select>
                </div>
            </div>
            <table class="table table-hover dark mt-4">
                <thead>
                    <th>#</th>
                    <th>CÓDIGO</th>
                    <th>DISCIPLINA</th>
                    <th>NOTA</th>
                </thead>
                <tbody id="students"></tbody>
            </table>
            <input type="hidden" id="lectiveY" value="" name="anoLectivo">
        </div>
        <div class="float-right" id="group_btnSubmit" hidden>
            <button type="submit" class="btn btn-success mb-3">
                <i class="fas fa-plus-circle"></i>
                Guardar
            </button>
        </div>
    </form>
@endsection
@section('scripts-new')
    @parent
    <script>
        $(function() {
            //gET STUDENT ABOUT COURSE
            $("#courseID").change(function() {
                $("#students").empty();
                $("#school_name").val('');
                var course_id = $("#courseID").val();
                var lective = $("#lective_year").val();

                $.ajax({
                    url: "/users/get_students_equivalence/" + course_id + "/" + lective,
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',
                    success: function(result) {
                        $("#studentID").prop('disabled', true);
                        $("#studentID").empty();

                        $("#studentID").append('<option selected="" value=""></option>');
                        $.each(result, function(index, row) {
                            $("#studentID").append('<option value="' + row.id + '">' +
                                row.name + " #" + row.student_number + " (" + row
                                .email + ")" + '</option>');
                        });

                        $("#studentID").prop('disabled', false);
                        $("#studentID").selectpicker('refresh');
                    },
                    error: function(dataResult) {}
                });
            });
            //get DISCIPLINA
            $("#studentID").change(function() {
                $("#students").empty();
                $("#school_name").val('');
                var student_id = $("#studentID").val();
                var lective = $("#lective_year").val();
                var bodyData = '';
                $.ajax({
                    url: "/users/get_students_disciplines/" + student_id,
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',
                    beforeSend: function() {
                        if ($("#studentID").val() == "") {
                            return false;
                        }
                    },
                    success: function(result) {
                        if (result.length > 0) {
                            $("#students").empty();
                            var i = 1;
                            var notaP;
                            $("#lectiveY").val($("#lective_year").val());
                            $.each(result, function(index, row) {
                                bodyData += '<tr>'
                                bodyData += "<td class='text-center'>" + i++ +
                                    "</td>";
                                bodyData += "<td class='text-center'>" + row
                                    .codigo + "</td>";
                                bodyData += "<td class=''>" + row.disciplina +
                                    "</td>";
                                bodyData += "<input type='hidden' value='" + row
                                    .disc_id + "' name='discipline_id[]' >";
                                notaP = row.nota != null ? row.nota : "";
                                min = row.type == 1 ? 10 : 0;
                                // bodyData += "<td class='text-center fs-2'>"+row.state+"</td>";  
                                bodyData +=
                                    "<td class='text-center '><input type='number' value='" +
                                    notaP +
                                    "' min='" + min +  "' class='form-control w-auto'  max='20' name='nota[]' required></td>";
                                bodyData += '</tr>'
                            });
                            notaP = "";
                            $("#group_btnSubmit").attr('hidden', false);
                        } else {
                            bodyData += '<tr>'
                            bodyData +=
                                "<td colspan='4' class='text-center '>Nenhuma disciplina foi encontrado associada a equivalência do estudante selecionado.</td>";

                            bodyData += '</tr>'
                            $("#group_btnSubmit").attr('hidden', true);
                        }

                        $("#students").append(bodyData);
                    },
                    error: function(dataResult) {}
                });
            })
        });
    </script>
@endsection
