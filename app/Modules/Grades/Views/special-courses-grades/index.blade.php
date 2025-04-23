<title>Avaliações | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'LANÇAR NOTAS DE CURSO PROFISSIONAL')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('panel_avaliation') }}">Avaliações</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Lançar notas de curso profissional</li>
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
    <div class="col">
    {!! Form::open(['route' => ['scg.store']]) !!}
    @csrf
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
        <div class="card">
            <div class="row">
            <div class="col-6">
                    <div class="form-group col">
                        <label>@lang('GA::courses.course')</label>
                        {{ Form::bsLiveSelect('course', $courses, null, ['required', 'placeholder' => 'Selecione o curso']) }}
                    </div>     
            </div>

            <div class="col-6" id="editions-container">
                        <div class="form-group col">
                            <label>Edição</label>
                            {{ Form::bsLiveSelectEmpty('edition', [], null, ['required', 'placeholder' => 'Selecione a edição']) }}
                        </div>
            </div>

        
        </div>
        <hr>
        <div class="card">
        <div class="row">
            <div class="col-12">
            <table class="table table-hover" id="grades-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nº de inscrição</th>
                        <th>Nome</th>
                        <th>Nota</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
            </div>
            <div class="col-12">
                
                <button type="submit" class="btn btn-success float-right ml-3">Guardar notas</button>
                <a href="" class="btn btn-primary float-right" onclick="generateUrl(this)" target="_blank">
                    @icon('fa fa-file-pdf')
                    Gerar PDF
                </a>
            </div>
        </div>
        </div>
        {!! Form::close() !!}

                


    </div>
@endsection
@section('scripts-new')
    @parent
    <script>
        let lective_year = $('#lective_year').val();
        let selectCourse = $('#course');
        let selectEdition = $('#edition');
     
            function generateUrl(obj)  {
           
            let url = "{{ route('scg.pdf', '__ID__') }}"
            .replace('__ID__', selectEdition.val());
                
            $(obj).attr('href', url);
         
        
        }
      
       

        selectCourse.change(function() {
            if(selectCourse.val() == ''){
                selectEdition.val() = '';
            }
            else{
                getEditions()
            }
           

        });

        selectEdition.change(function() {
            if(selectEdition.val() == ''){
                $('#grades-table tbody').empty();
            }
            else{
                getStudentsGrades()
            }
           

        });

        function getEditions() {
            
            $.ajax({
                    url: '/pt/grades/get_editions/' + selectCourse.val() + '/' + lective_year,
                    type: "GET",
                    data: {
                            _token: '{{ csrf_token() }}'
                        },
                    cache: false,
                    dataType: 'json',
                }).done(function(response) {
                  //  selectEdition.append('<option value="" selected>Selecione a edição<option>')
                    if(response.editions.length > 0){
                        response.editions.forEach(function(item){
                            selectEdition.append('<option value="' + item.id + '">' + item.number +
                                '</option>');
                        });
                    }
                    selectEdition.selectpicker('refresh');
                  
                });

        }

        function getStudentsGrades() {
            $('#grades-table tbody').empty();
    $.ajax({
        url: '/pt/grades/get_students_grades/' + selectEdition.val(),  // URL sem lective_year
        type: "GET",
        data: {
            _token: '{{ csrf_token() }}'
        },
        cache: false,
        dataType: 'json',
    }).done(function(response) {
        // Limpar a tabela antes de adicionar os novos dados
        let data = '';
      
        // Verificar se há dados
        if (response.data.length > 0) {
            // Iterar sobre os dados e adicionar uma linha para cada item
            response.data.forEach(function(item,index) {
               
                data +=    '<tr>' +
                       '<td>' + (index + 1) + '</td>' + 
                       '<td>' + item.code + '</td>' + 
                        '<td>' + item.student_name + '</td>' +  // Exibindo o nome do estudante
                        '<td><input type="number" value="' + item.grade + '" class="form-control" name="grades[]" min="0" max="100" style="width:120px"></td>' +  // Exibindo o campo 'grade' dentro de um <input>
                    '<input type="hidden" value="' + item.id + '" class="form-control" name="students[]">'
                    +'</tr>';

                
            });

        

            
       


            $('#grades-table tbody').append(data);
        
            
        } else {
            // Se não houver dados, adicionar uma linha informando
            $('#grades-table tbody').append(
                '<tr><td colspan="4"><h3>Nenhuma estudante encontrado.</h3></td></tr>'
            );
        }
    });
    }
    </script>

@endsection