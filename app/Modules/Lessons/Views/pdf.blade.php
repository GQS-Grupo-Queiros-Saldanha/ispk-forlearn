@extends('layouts.print')
@section('content')

    <style>
        html, body {

        }

        body {
            font-family: Montserrat, sans-serif;
        }

        .table td,
        .table th {
            padding: 0;
            border: 0;
        }

        .form-group, .card, label {
            display: block !important;
        }

        .form-group {
            margin-bottom: 1px;
            font-weight: normal;
            line-height: unset;
            font-size: 0.75rem;
        }

        .h1-title {
            padding: 0;
            margin-bottom: 0;
        }

        .img-institution-logo {
            width: 50px;
            height: 50px;
        }


        table{
           width: 100%; 
        }

        table thead{
           background-color: #222;
           color:white;
           padding: 10px;
        }

        .div-top {
            font-family: Montserrat, sans-serif;
            color:grey;
            text-transform: uppercase;
            position:50px;
            margin-bottom:2px;
            background-color: rgb(240, 240, 240);
            background-image: url('https://dev.forlearn.ao/storage/{{$institution->logotipo}}'); 
            /* background-image: url('{{ asset('img/CABECALHO_CINZA01GRANDE.png') }}');  */
            background-position: 100%;
            background-repeat: no-repeat;
            background-size:6.5%;
            padding-left: 3px;
        }

        .td-institution-name {
            vertical-align: middle !important;
            font-weight: bold;
            text-align: right;
        }

        .td-institution-logo {
            vertical-align: middle !important;
            text-align: center;
        }

        .td-parameter-column {
            padding-left: 5px !important;
        }

        label {
            font-weight: bold;
            font-size: .75rem;
            color: #000;
            margin-bottom: 0;
        }

        input, textarea, select {
            display: none;
        }

        .pl-1 {
            padding-left: 1rem !important;
        }

        .header-user {
            padding: 10 !important;
            text-align: left !important;
            font-size: 0.7rem;
        }

    </style>

    @php($now = \Carbon\Carbon::now())
    <main>
        <div class="div-top " style="height:60px;">
            <table class="table m-0 p-0">
                <tr>
                    <td class="pl-1">
                        <h1 class="h1-title" style="font-size: 24px">
                            Aula
                        </h1>
                    </td>
        
                </tr>
                <tr>
                    <td class="pl-1" style="font-size: 10px">
                        Documento gerado a
                        <b>{{ $now->format('d/m/Y')}}</b>
                    </td>
                </tr>
            </table>
        </div>

        <table class="" >
            <thead class="">
                <tr>
                    <th class="">Docente</th>
                    <th class="">Data</th>
                </tr>
            </thead>
            <tbody class="">
                <tr>
                    <td class="" style="font-size: 10pt;">{{ $lesson->teacher->name }}</td>
                    <td class="" style="font-size: 10pt;">{{ $lesson->occured_at }}</td>
                </tr>
            </tbody>
        </table>
         <table class="">
            <thead class="">
                <tr>
                    <th class="">Disciplina</th>
                    <th class="">Regime</th>
                </tr>
            </thead>
            <tbody class="">
                <tr>
                    <td class="" style="font-size: 10pt;">{{ $lesson->discipline->currentTranslation->display_name . ' - ' . $lesson->class->display_name }}</td>
                    <td class="" style="font-size: 10pt;">{{ $lesson->regime->currentTranslation->display_name }}</td>
                </tr>
            </tbody>
        </table>
         <table class="">
            <thead class="">
                <tr>
                    <th class="">Sumário</th>
                    <th class="">Observação</th>
                </tr>
            </thead>
            <tbody class="">
                <tr>
                    <td class="" style="font-size: 10pt;">{{ $lesson->summary->currentTranslation->display_name }}</td>
                    <td class="" style="font-size: 10pt;">{{ $lesson->observations }}</td>
                </tr>
            </tbody>
        </table>

        <table class="">
            <thead class="">
            <th class="">Lista de presença</th>
            </thead>
        </table>
        <div
            style="position: absolute; top: 8px; right: 100px; width: 350px; font-family: Impact; padding-top: 2.5px;">
            <h4><b>
                    @if (isset($institution->nome))
                        {{ $institution->nome }}
                    @else
                        Instituição sem nome
                    @endif
                </b></h4>
        </div>
        
        <div class="row">
            <div id="student-table" class="col-md-12 ml-12">
                <table style="width:100%", class="table table-bordered">
                    <th>Aluno</th>
                    <th style="width:30%">Estado</th>
                    @foreach ($students as $list)
                        @if($list != null)
                            @if($list->presenca === "Presente")
                                <tr>
                                    @foreach ($list->parameters as $parameters)
                                        @if ($parameters->id === 1)
                                            <td> {{$parameters->pivot->value}} </td>
                                        @endif
                                    @endforeach
                                    <td>
                                        <span class="badge text-uppercase badge-success">presente</span>
                                    </td>
                                </tr> 
                            @endif

                            @if($list->presenca === "Falta")
                                <tr>
                                    @foreach ($list->parameters as $parameters)
                                        @if ($parameters->id === 1)
                                            <td> {{$parameters->pivot->value}} </td>
                                        @endif
                                    @endforeach
                                    <td>
                                        <span class="badge text-uppercase badge-danger">falta</span>
                                    </td>
                                </tr> 
                            @endif
                        @endif                 
                    @endforeach
                </table>
            </div>
        </div>

    </main>

@endsection
    @section('scripts')
@parent
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        //var studentContainer = $('#student-container');

        // var studentTable = $('#student-table');
        // var presentStudents = @json($attendance)

        // function mountSelectedDiscipline() {
        //     var disciplineId = parseInt({{ $lesson->discipline_id }});
        //     var classId = parseInt({{ $lesson->class_id }});

        //     if (disciplineId && classId) {
        //         let route = ("{{ route('lessons.discipline-class') }}") + '?discipline=' + disciplineId + '&class=' + classId;
        //         $.get(route, function (data) {
        //             eventData = data;
        //             buildStudentsTable();
        //         });
        //     }
        // }

        // function buildStudentsTable() {
        //     var table = $('<table>', {style: 'width:100%', class: 'table table-bordered'});

        //     var headerRow = $('<tr>').append(
               
        //         $('<th>').text('Aluno'),
        //         $('<th>', {width: '30%'}).text('Estado'),
        //     );
        //     table.append(headerRow);

        //     var count = 1;
        //     $.each(eventData.students, function (k, v) {                
        //         if (v != null) {
        //             var name = v.parameters[1].pivot.value ? v.parameters[1].pivot.value : v.name;
        //             var displayName = name + ' ( #' + v.parameters[0].pivot.value + ' )';

        //             var wasStudentPresent = $.inArray(v.id, presentStudents) !== -1;
        //             var stateBadge = wasStudentPresent ? 'badge-success' : 'badge-danger';
        //             var stateText = wasStudentPresent ? 'presente' : 'falta';

        //             var row = $('<tr>').append(
                    
        //                 $('<td>').text(displayName),
        //                 $('<td>').append(
        //                     $('<span>', {
        //                         class: 'badge text-uppercase ' + stateBadge,
        //                         id: 'student-' + v.id + '-state'
        //                     }).text(stateText)
        //                 )
        //             )
        //             table.append(row);
        //         }
        //     });

        //     // studentTable.empty().append(table);
        // }

        // function handleCheckBoxOnStudent(checkbox, studentId) {
        //     var isChecked = checkbox.checked;
        //     var stateSpan = $('#student-' + studentId + '-state');

        //     if (isChecked) {
        //         stateSpan.removeClass('badge-danger').addClass('badge-success').text('presente');
        //     } else {
        //         stateSpan.removeClass('badge-success').addClass('badge-danger').text('falta');
        //     }
        // }

        // $(function () {
        //     mountSelectedDiscipline();
        // });
    </script>
@endsection
