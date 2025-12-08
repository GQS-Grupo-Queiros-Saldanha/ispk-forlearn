<title>BOLETIM DE NOTAS | forLEARN® by GQS</title>
@extends('layouts.generic_index_new')
@section('page-title', 'BOLETIM DE NOTAS')
@section('breadcrumb')
    <li class="breadcrumb-item">
        <a href="/">Home</a>
    </li>
    <li class="breadcrumb-item">
        <a href="{{ route('panel_avaliation') }}">Avaliações</a>
    </li>
    <li class="breadcrumb-item active" aria-current="page">Boletim de notas</li>
@endsection
@section('selects')
    <div class="mb-2">
        <label for="lective_year">Selecione o ano lectivo</label>
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
    <style>
        .boletim_text{
            font-weight: normal !important;
            font-size: 14px !important; 
        }
        .table{
            margin-bottom: 1px;
            padding-bottom: 1px;
        }
    </style>
    
   <div id="table_student" class="mt-2">
       
   </div>
@endsection

@section('scripts-new')
    @parent
    <script>
        $(document).ready(function() {

           

            
            getStudentBoletim($("#lective_year").val()); 
            
            $("#lective_year").change(function(){
                getStudentBoletim($(this).val());
            });
            
            function getStudentBoletim(lective_year) {
                   $("#table_student").html("");
                $.ajax({
                   url: "/pt/get_boletim_student/"+lective_year,
                    type: "GET",
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    cache: false,
                    dataType: 'json',
                }).done(function(data) {

                    
                    $("#table_student").html(data);

                    // let thead = $('#thead-semestre1');
                    // let tbody = $('#tbody-semestre1');
                    
                    // $('#thead-semestre1,#tbody-semestre1').remove(); 
                    $('#tabela_pauta_student').append('<tbody><tr style="background-color: white!important;"><td  style="background-color: white!important;" colspan="22"><hr style="margin-top: 2px!important;margin-bottom: 2px!important;background-color: ##FF9800 !important;color: #da8500;font-size: 6px;border: 4px solid #e79411;"></td></tr></tbody>'); 
                     $('#tabela_pauta_student').append(thead);
                     $('#tabela_pauta_student').append(tbody); 
                     
                    
                });
            }

           
        })
    </script>
@endsection
