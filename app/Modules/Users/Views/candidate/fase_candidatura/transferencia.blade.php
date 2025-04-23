@section('title', 'Fases de candidatura')
@extends('layouts.backoffice')

@section('content')

    <div class="content-panel"style="padding: 0">
        @include('Users::candidate.navbar.navbar')
        <div class="content-header">
            <div class="container-fluid">
                <br>
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1>Transferência</h1>
                    </div>
                    <div class="col-sm-6">
                        <ol class="breadcrumb float-right">
                            <li class="breadcrumb-item">
                                <a href="{{route('candidates.index')}}">Candidaturas</a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">transferencia</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main content --}}
        <div class="content pb-3">
            <div class="container-fluid ">
                <div class="row">
                    <div class="col-md-7">

                    </div>
                    <div class="col-md-5">
                        <div class="float-right mr-4" style="width:200px; !important">
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col">
                        <div class="card">
                            <div class="card-body">
                                @include('Users::candidate.fase_candidatura.message')
                                <div class="ml-4 h5" style="text-align: justify;">
                                    Transefência do candidato(a) <strong>{{ $user->name }}</strong>
                                    para fase {{ $lectiveCandidateNext->fase }}, 
                                    caso desejas manter os cursos, deves clicar no botão salvar.
                                </div>
                                <div class="ml-4 h5 d-flex align-items-center">
                                    <input type="checkbox" class="form-control" name="manter" id="manter" style="width: 15px; height: 15px;"  checked/>
                                    <span class="pl-2">Manter os cursos? </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row check-manter d-none">
                    <div class="col-md-6">
                        @isset($courses)
                            <label for="studentidCourse" class="form-group">Seleciona o curso:</label>
                            <select name="coursesCandidates" class="selectpicker form-control form-control-sm"
                                id="studentidCourse" data-selected-text-format="count > 3">
                                @foreach ($courses as $course)
                                    <option value="{{ $course->id }}">{{ $course->currentTranslation->display_name }}</option>
                                @endforeach
                            </select>
                        @endisset
                    </div>
                    <div class="col-md-6">
                        <label for="studentIDturma" class="form-group">Seleciona a turma:</label>
                        <select name="turmas" class="selectpicker form-control form-control-sm" id="studentIDturma"
                            maxlength="2" max="2">
                        </select>
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-primary mt-4 w-100 " id="btn-add-fase">
                            <i class="fas fa-plus"></i>
                            <span>adicionar</span>
                        </button>
                    </div>
                </div>
                <div class="position-relative pb-5 pt-2">
                    <form action="{{route('fase.transferencia')}}" method="POST">
                        @csrf
                        <input type="hidden" name="lective_year" id="lective_year" value="{{$lectiveYearSelected}}"/>
                        <input type="hidden" name="user_id" value="{{$user->user_id}}"/>
                        <input type="hidden" name="faseNowId" value="{{$lectiveCandidate->id}}"/>
                        <input type="hidden" name="faseNextId" value="{{$lectiveCandidateNext->id}}"/>
                        <input type="hidden" name="manterCourseAnClass" id="manterCourseAnClass"  value="yes"/>
                        <table class="table table-hover d-none text-center check-manter" id="table-fase">
                            <thead>
                                <th>curso</th>
                                <th>turma</th>
                                <th colspan="2">acção</th>
                            </thead>
                            <tbody id="tbody-fase"> </tbody>
                        </table>
                        <hr/>
                        <button class="btn btn-success m-2 rounded float-right " id="btn-add" type="submit">
                            <i class="fas fa-plus"></i>
                            <span>salvar</span>
                        </button>
                    </form>
                </div>

            </div>
        </div>
    </div>


@endsection
@section('scripts')
    @parent
    <script>
        let manterCourseAnClass = $('#manterCourseAnClass');
        let tableFase = $('#table-fase');
        let tbody = $('#tbody-fase');
        let selectCourse = $('#studentidCourse');
        let selectClasse = $('#studentIDturma');
        let lectiveYear = $('#lective_year');
        let btnAddFase = $('#btn-add-fase');        
        let checkManter = $('#manter');
        let btnAdd = $('#btn-add');
        let elements = [];

        switchDisciplines(selectCourse.val(), lectiveYear.val());

        checkManter.on('change', function(e){
            let checkManterItems = $('.check-manter');
            if(checkManter.is(":checked")){
                checkManterItems.each(function(index){
                    if(!this.classList.contains('d-none'))
                       this.classList.add('d-none');
                });
                manterCourseAnClass.val("yes");
            }else{
                checkManterItems.each(function(index){
                    if(this.classList.contains('d-none'))
                       this.classList.remove('d-none');
                });    
                elements = [];
                tbody.html('');
                manterCourseAnClass.val("no");
            }
        })

        selectCourse.on('change', function(e) {
            let anoLectivo = lectiveYear.val();
            switchDisciplines(this.value, anoLectivo);
        })

        btnAddFase.on('click', function(e) {

            let classId = selectClasse.val();
            let courseId = selectCourse.val();

            let item = {
                idClass: selectClasse.val(),
                idCourse: selectCourse.val(),
                displayClass: selectClasse.children("option[value='" + classId + "']").html(),
                displayCourse: selectCourse.children("option[value='" + courseId + "']").html(),
            };

            let find = false;

            for (let i = 0; i < elements.length; i++)
                if (elements[i].idCourse == item.idCourse && elements[i].idClass == item.idClass) {
                    find = true;
                    break;
                }

            if (!find) {
                if(item.idClass != null)
                    elements.push(item);
                if (elements.length > 0){
                    createTableItems();
                    createMethodForDelItems();
                }
            }

        });

        function createTableItems() {
            let join = '';
            elements.forEach(item => {
                join += `<tr>
                            <td>${item.displayCourse}</td>
                            <td>${item.displayClass != null ? item.displayClass : '-'}</td>
                            <td>
                                <input type="hidden" name="courseJoinClass[]" value="${item.idCourse}+${item.idClass}"/>
                            </td>
                            <td class="del-item" course="${item.idCourse}"  turma="${item.idClass}">
                                <button class="btn btn-danger">
                                    <i class="fas fa-times"></i>
                                    <span>eliminar</span>
                                </button>
                            </td>
                        </tr>`;
            });

            if (elements.length > 0) {
                if (tableFase.hasClass('d-none'))
                    tableFase.removeClass('d-none');
            } else {
                if (!tableFase.hasClass('d-none'))
                    tableFase.addClass('d-none');
            }
            tbody.html(join);
        }

        function createMethodForDelItems() {
            let btnDelItems = document.querySelectorAll('.del-item');

            btnDelItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    elements = removeItem(item.getAttribute('course'), item.getAttribute('turma'));
                    createTableItems();
                    if (elements.length > 0)
                        createMethodForDelItems();
                    else{
                        //if(!btnAdd.hasClass('d-none')) btnAdd.addClass('d-none');
                    }
                });
            })

            function removeItem(course, turma) {
                let arrayItems = [];
                for (let i = 0; i < elements.length; i++)
                    if (elements[i].idCourse != course && elements[i].idClass != turma) {
                        arrayItems.push(elements[i]);
                        delete elements[i];
                    }
                return arrayItems;
            }

        }

        function switchDisciplines(courseId, lectiveYearId) {
            if (courseId != "" && lectiveYearId != "") {
                $.ajax({
                    url: '/pt/grades/teacher_disciplines/' + courseId + '/' + lectiveYearId
                }).done(function(response) {
                    selectClasse.empty();
                    selectClasse.append('<option value=""></option>');
                    if (response['turma'].length > 0) {
                        selectClasse.prop('disabled', true);
                        selectClasse.empty();
                        response['turma'].forEach(function(turma) {
                            var turmaId = turma.id;
                            var turmaName = turma.display_name;
                            selectClasse.append('<option value="' + turmaId + '">' + turmaName +
                                '</option>');
                        });
                        selectClasse.prop('disabled', false);
                        selectClasse.selectpicker('refresh');
                    } else {
                        alert("Nenhuma  turma foi encontrada para este curso");
                        selectClasse.empty();
                        selectClasse.html('');
                        selectClasse.prop('disabled',true);
                    }

                });
            } else {
                selectClasse.empty();
                selectClasse.prop('disabled',true);
            }
        }
    </script>
@endsection
