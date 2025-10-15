{{-- @extends('layouts.print') --}}
{{-- @section('content') --}}
    
    {{-- <main> --}}
                
        <div class="row">
            <div id="student-table" class="col-md-12 ml-12">
                {{-- <table style="width:100%", class="table table-bordered">
                    <th>Aluno</th>
                    <th style="width:30%">Estado</th>
                    @foreach ($lesson->students as $student)
                        <tr>
                            <td>{{$student->name}}</td>
                            <td>
                                <span class="badge text-uppercase badge-success">presente</span>
                            </td>
                        </tr>                    
                    @endforeach
                </table> --}}
            </div>
        </div>
    {{-- </main> --}}

{{-- @endsection --}}
@section('scripts')
@parent
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        //var studentContainer = $('#student-container');
        var studentTable = $('#student-table');
        var presentStudents = @json($attendance)

        function mountSelectedDiscipline() {
            var disciplineId = parseInt({{ $lesson->discipline_id }});
            var classId = parseInt({{ $lesson->class_id }});

            if (disciplineId && classId) {
                let route = ("{{ route('lessons.discipline-class') }}") + '?discipline=' + disciplineId + '&class=' + classId;
                $.get(route, function (data) {
                    eventData = data;
                    buildStudentsTable();
                });
            }
        }

        function buildStudentsTable() {
            var table = $('<table>', {style: 'width:100%', class: 'table table-bordered'});

            var headerRow = $('<tr>').append(
               
                $('<th>').text('Aluno'),
                $('<th>', {width: '30%'}).text('Estado'),
            );
            table.append(headerRow);

            var count = 1;
            $.each(eventData.students, function (k, v) {                
                if (v != null) {
                    // console.log(v)
                    var name = v.parameters[1].pivot.value ? v.parameters[1].pivot.value : v.name;
                    var displayName = name + ' ( #' + v.parameters[0].pivot.value + ' )';

                    var wasStudentPresent = $.inArray(v.id, presentStudents) !== -1;
                    var stateBadge = wasStudentPresent ? 'badge-success' : 'badge-danger';
                    var stateText = wasStudentPresent ? 'presente' : 'falta';

                    var row = $('<tr>').append(
                    
                        $('<td>').text(displayName),
                        $('<td>').append(
                            $('<span>', {
                                class: 'badge text-uppercase ' + stateBadge,
                                id: 'student-' + v.id + '-state'
                            }).text(stateText)
                        )
                    )
                    table.append(row);
                }
            });

            studentTable.empty().append(table);
        }

        function handleCheckBoxOnStudent(checkbox, studentId) {
            var isChecked = checkbox.checked;
            var stateSpan = $('#student-' + studentId + '-state');

            if (isChecked) {
                stateSpan.removeClass('badge-danger').addClass('badge-success').text('presente');
            } else {
                stateSpan.removeClass('badge-success').addClass('badge-danger').text('falta');
            }
        }

        $(function () {
            mountSelectedDiscipline();
        });
    </script>
@endsection
