@section('title',__('Atribuir notas de trabalho de fim de curso'))
@extends('layouts.backoffice')

@section('styles')
@parent
@endsection

@section('content')

<div class="content-panel">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1>Atribuir notas de trabalho de fim de curso</h1>
                    </h1>
                </div>
                <div class="col-sm-6">
                    <div class="float-right mt-4">
                        <a href="{{ route('old_student.finalGrade') }}" class="btn btn-secondary btn-sm mb-3">
                            Voltar
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <hr>
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('old_student.storeFinalGrade')}}" method="POST">
                        @csrf
                        <table class="table table-striped table-hover" id="students">
                            <thead>
                                <th>Estudante</th>
                                <th>Disciplina</th>
                                <th>Ano Lectivo</th>
                                <th>Trabalho</th>
                                <th>Defesa</th>
                                <th>Média TFC</th>
                            </thead>
                            <tbody>
                                <tr>
                                <td>
                                    <input type="text" name="student_id" value="{{ $student->id }}" hidden>
                                    <input type="text" name="discipline_id" value="{{ $student->discipline_id }}" hidden>
                                     {{ $student->name }}
                                    </td>
                                 <td>{{ $student->display_name }}</td>
                                 <td>
                                     <select name="lective_year" id="" class="form-control">
                                         <option value="2020">2020</option>
                                     </select>
                                 </td>
                                    <td>
                                        <input type="number" min="0" max="20" step="0.01" class="form-control" name="trabalho" id="trabalho" value="{{ $grades->first()->tfc_trabalho ?? 0 }}" required>
                                    </td>
                                    <td>
                                        <input type="number" min="0" max="20" step="0.01" class="form-control" name="defesa" id="defesa" value="{{ $grades->first()->tfc_defesa ?? 0 }}" required>
                                    </td>
                                    <td>
                                        <input type='number' min='0' max='20' step="0.01" class='form-control' name="grade" value="{{ round($grades->first()->grade ?? 0) }}" readonly id="grade">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="float-right">
                            <button type="submit" class="btn btn-success">Inserir nota</button>
                        </div>
                        </form>
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
    $(function()
    {
       $("#trabalho").keyup(function(){
            generateMedia();
       });

       $("#defesa").keyup(function(){
            generateMedia();
       });

       function generateMedia()
       {
           var tfc = 0;

           $("#trabalho").val() != "" ? trabalho = $("#trabalho").val()  : trabalho = 0;
           $("#defesa").val()   != "" ? defesa = $("#defesa").val() : defesa = 0;

           tfc = (Number(trabalho) + Number(defesa)) / 2;
           tfc = Math.round(tfc)
           $("#grade").val(tfc);
       }
    })
</script>
@endsection
