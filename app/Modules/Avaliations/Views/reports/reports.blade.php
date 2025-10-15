@section('title', "Relatório das avaliações")
@extends('layouts.backoffice')

@section('styles')
    @parent
@endsection

@section('content')

    <div class="content-panel">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-8">
                        <h1>Relatório das avaliações</h1>
                    </div>
                    <div class="col-sm-4">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('back.page') }}">Home</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Relatório das avaliações</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main content --}}

        <div class="content">
            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                    <div class="col-6">
                        <div id="main">
                                <label for="options">Pesquisar por</label>
                                <select name="options" id="options" class="form-control">
                                    <option value="0"></option>
                                    <option value="1">Curso</option>
                                    <option value="2">Departamentos</option>
                                </select>
                        </div>
                        <div id="dependencies">
                            <br>
                            <div id="course-dep" hidden>
                                <label for="courses">Curso</label>
                                {{ Form::bsLiveSelectEmpty('courses', [], null, ['class' => "form-control"]) }}
                            </div>
                            <div id="department-dep" hidden>
                                <label for="courses">Departamento</label>
                                {{ Form::bsLiveSelectEmpty('departments', [], null, ['class' => "form-control"]) }}
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="card">
                            <div class="card-body">

                                <div id="tail">
                                <table id="users-table" class="table table-striped table-hover">
                                    <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Professor</th>
                                        <th>Curso</th>
                                        <th>Disciplina</th>
                                        <th>Turma</th>
                                        <th>Departamento</th>
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
    </div>

    {{-- modal confirm --}}
    @include('layouts.backoffice.modal_confirm')

@endsection

@section('scripts')
    @parent
        <script>
            $(function () {
                var selectCourses = $("#courses");
                var selectDepartments = $("#departments");

                populateSelectWithCourses();
                populateSelectWithDepartments();

               $('#users-table').DataTable({
                   processing: true,
                    serverSide: true,
                  ajax: '{!! route('getTeachers.ajax') !!}',
                    columns: [
                        {
                            data: 'DT_RowIndex', orderable: false, searchable: false
                        },
                        {
                            data: 'name',
                            name: 'name',
                            visible: true
                        },
                        {
                            data: 'course_name',
                            name: 'course_name',
                            visible: true
                        },
                        {
                            data: 'discipline_name',
                            name: 'discipline_name',
                            visible: true
                        },
                        {
                            data: 'class_name',
                            name: 'class_name',
                            visible: true
                        },
                        {
                            data: 'departments_name',
                            name: 'departments_name',
                            visible: false
                        },
                    ],

                    "lengthMenu": [ [10, 50, 100, 50000], [10, 50, 100, "Todos"] ],
                    language: {
                        url: '{{ asset('lang/datatables/'.App::getLocale().'.json') }}',
                    }
                });

                $("#options").change(function(){
                    if ($("#options").val() == 1) {
                        $("#department-dep").prop('hidden', true);
                        $("#course-dep").prop('hidden', false);
                        $("#courses").change(function()
                        {
                            var courseId = $("#courses").val();
                            searchByCourse(courseId);
                        });
                    }else if($("#options").val() == 2){
                        $("#course-dep").prop('hidden', true);
                        $("#department-dep").prop('hidden', false);
                        $("#departments").change(function()
                        {
                            var departmentId = $("#departments").val();
                            searchByDepartment(departmentId);
                        });
                    }else if($("#options").val() == 0){
                        $("#course-dep").prop('hidden', true);
                        resetSearch();
                    }
                });

                function searchByCourse(courseId)
                {
                    var t = $("#users-table").DataTable();
                    t.destroy();
                    //t.clear().draw();


                $('#users-table').DataTable({
                    processing: true,
                    serverSide: true,
                  ajax: '/avaliations/get_teachers_by_course/' + courseId,
                    columns: [
                        {
                            data: 'DT_RowIndex', orderable: false, searchable: false
                        },
                        {
                            data: 'name',
                            name: 'name',
                            visible: true
                        },
                        {
                            data: 'course_name',
                            name: 'course_name',
                            visible: true
                        },
                        {
                            data: 'discipline_name',
                            name: 'discipline_name',
                            visible: true
                        },
                        {
                            data: 'class_name',
                            name: 'class_name',
                            visible: true
                        },
                        {
                            data: 'departments_name',
                            name: 'departments_name',
                            visible: false
                        },
                    ],

                    "lengthMenu": [ [10, 50, 100, 50000], [10, 50, 100, "Todos"] ],
                    language: {
                        url: '{{ asset('lang/datatables/'.App::getLocale().'.json') }}',
                    }
                });
                }

                function searchByDepartment(departmentId)
                {
                    var t = $("#users-table").DataTable();
                    t.destroy();
                    //t.clear().draw();


                $('#users-table').DataTable({
                    processing: true,
                    serverSide: true,
                  ajax: '/avaliations/get_teachers_by_departments/' + departmentId,
                    columns: [
                        {
                            data: 'DT_RowIndex', orderable: false, searchable: false
                        },
                        {
                            data: 'name',
                            name: 'name',
                            visible: true
                        },
                        {
                            data: 'course_name',
                            name: 'course_name',
                            visible: true
                        },
                        {
                            data: 'discipline_name',
                            name: 'discipline_name',
                            visible: true
                        },
                        {
                            data: 'class_name',
                            name: 'class_name',
                            visible: true
                        },
                        {
                            data: 'departments_name',
                            name: 'departments_name',
                            visible: false
                        },
                    ],

                    "lengthMenu": [ [10, 50, 100, 50000], [10, 50, 100, "Todos"] ],
                    language: {
                        url: '{{ asset('lang/datatables/'.App::getLocale().'.json') }}',
                    }
                });
                }

                function resetSearch()
                {
                    var t = $("#users-table").DataTable();
                    t.destroy();
                    //t.clear().draw();

                $('#users-table').DataTable({
                    processing: true,
                    serverSide: true,
                  ajax: '{!! route('getTeachers.ajax') !!}',
                    columns: [
                        {
                         data: 'DT_RowIndex', orderable: false, searchable: false
                        },
                        {
                            data: 'name',
                            name: 'name',
                            visible: true
                        },
                        {
                            data: 'course_name',
                            name: 'course_name',
                            visible: true
                        },
                        {
                            data: 'discipline_name',
                            name: 'discipline_name',
                            visible: true
                        },
                        {
                            data: 'class_name',
                            name: 'class_name',
                            visible: true
                        },
                        {
                            data: 'departments_name',
                            name: 'departments_name',
                            visible: false
                        },
                    ],

                    "lengthMenu": [ [10, 50, 100, 50000], [10, 50, 100, "Todos"] ],
                    language: {
                        url: '{{ asset('lang/datatables/'.App::getLocale().'.json') }}',
                    }
                });

                }


                function populateSelectWithCourses(){
                $.ajax({
                    url: "/avaliations/get_all_courses/",
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',
                    //$('#container').html(data.html);

                }).done(function (data){
                    //if (dataResult.length) {
                        selectCourses.prop('disabled', true);
                        selectCourses.empty();


                        selectCourses.append('<option selected="" value=""></option>');
                        $.each(data, function (index, row) {
                            selectCourses.append('<option value="' + row.id + '">' + row.current_translation.display_name + '</option>');
                        });

                        selectCourses.prop('disabled', false);
                        selectCourses.selectpicker('refresh');

                        //switchRegimes(selectDiscipline[0]);
                    //}
                });
            }
            function populateSelectWithDepartments(){
                $.ajax({
                    url: "/avaliations/get_all_departments/",
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',
                    //$('#container').html(data.html);

                }).done(function (data){
                    //if (dataResult.length) {
                        selectDepartments.prop('disabled', true);
                        selectDepartments.empty();


                        selectDepartments.append('<option selected="" value=""></option>');
                        $.each(data, function (index, row) {
                            selectDepartments.append('<option value="' + row.id + '">' + row.current_translation.display_name + '</option>');
                        });

                        selectDepartments.prop('disabled', false);
                        selectDepartments.selectpicker('refresh');

                        //switchRegimes(selectDiscipline[0]);
                    //}
                });
            }

            });
        </script>
@endsection
