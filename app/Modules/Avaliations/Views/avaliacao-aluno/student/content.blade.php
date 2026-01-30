{{-- ANO LECTIVO --}}
{{-- @foreach ($avaliacaos as $itemavaliacaos)
    @foreach ($metricas as $item)
        {{$item->nome}}<br>
        
        {{$itemavaliacaos->nome}}
    @endforeach
  
@endforeach --}}

<p class="bg-light p-1 mb-2 mr-2 " style="font-family: calibri"><b>FREQUÊNCIA DO ANO CURRICULAR</b></h2>
<div class="d-flex m-0 mb-3 ">  
    <div class="mr-1 mh-100 ">     
        <table class="table table-bordered h-100">
                <thead  style="text-center background-color: #F5F3F3; !important; width:100%"><th style=" padding: 4px; !important">Ano</th></thead>
                <tbody><tr class="p-0 table-info text-center"> <td><h5>{{$ano_courso}}</h5></td></tr></tbody> 
        </table>
    </div>
    {{-- DADOS DO ANO LECTIVO E AS SUAS NOTAS --}}    
    <div class="w-100  mr-2 mh-100">
        <table class="table table-bordered h-100">
            <thead  style="background-color: #F5F3F3; !important; width:100%">
                <th style="padding: 4px; !important">Código</th>
                <th style="padding: 4px; !important">Regime</th>
                <th style="padding: 4px; !important">Disciplinas</th>
                @foreach ($avaliacaos as $avaliacao)
                    @foreach ($metricas as $metrica)
                        @if($avaliacao->avaliacaos_id == $metrica->avaliacao_id)
                            @if ($metrica->nome != "Exame")
                                <th style="padding: 4px; !important">
                                    {{ $metrica->nome }}
                                </th>
                            @endif
                        @endif
                    @endforeach
                    <th style="padding: 4px; !important">
                        @if ($metrica->nome!=$avaliacao->nome)
                            {{$avaliacao->nome}}  
                        @else
                        {{$metrica->nome }}                         
                        @endif
                       
                    </th>
                @endforeach
                <th style="padding: 4px; !important">Observações</th>
            </thead>
            <tbody>         
                @foreach ($ext_disciplina as $tem_disciplina)
                    <tr> 
                        <td>{{ $tem_disciplina->code}}</td>
                        <td>{{ $tem_disciplina->disc_code}}</td>
                        <td>{{ $tem_disciplina->display_name}}</td>
                    </tr>
                @endforeach                    
            </tbody>   
        </table>
    </div>
</div>

{{-- DISCIPLINAS EM ATRASO --}}
@empty(!$ext_cadeira)
    <div class="m-0">
        <button id="moreDiscipline" class="btn btn-light mb-2" type="button"  data-id="1" value="0">
            Disciplina /as em atraso <i  id="btnDisciplina" class="fa fa-chevron-right"></i> 
        </button>
        <div class="d-flex m-0 mb-3" id="verDisciplina_atraso" style="visibility: hidden;">
            {{-- DADOS DO ANO LECTIVO E AS SUAS NOTAS --}}    
            <div class="w-100  mr-2 mh-100">
                <table class="table table-bordered h-100">
                    <thead  style="background-color: #F5F3F3; !important; width:100%">
                        <th style=" background-color: #F5F3F3; padding: 4px; !important">Ano</th>
                        <th style="padding: 4px; !important">Código</th>
                        <th style="padding: 4px; !important">Regime</th>
                        <th style="padding: 4px; !important">Disciplinas</th>
                        @foreach ($avaliacaos as $avaliacao)
                        @foreach ($metricas as $metrica)
                            @if($avaliacao->avaliacaos_id == $metrica->avaliacao_id)
                                @if ($metrica->nome != "Exame")
                                    <th style="padding: 4px; !important">
                                        {{ $metrica->nome }}
                                    </th>
                                @endif
                            @endif
                        @endforeach
                        <th style="padding: 4px; !important">
                            {{$avaliacao->nome}}
                        </th>
                    @endforeach
                    
                        <th style="padding: 4px; !important">Observações</th>
                    </thead>
                    <tbody>

                        @foreach ($ext_cadeira as $tem_disCadeira)
                            <tr> 
                                <td style=" background-color: #d6e9f9;">{{ $tem_disCadeira->years}}</td>
                                <td>{{ $tem_disCadeira->discipli_code}}</td>
                                <td>{{ $tem_disCadeira->code}}</td>
                                <td>{{ $tem_disCadeira->display_name}}</td>
                            </tr>
                        @endforeach                    
                    </tbody>   
                </table>
            </div>
        </div>
    </div>
@endempty
    


<script>
    $(document).ready(function() {
        $("#moreDiscipline").click(function (e) {
            var moreDiscipline=$("#moreDiscipline").val();
            if (moreDiscipline==0){
                $("#moreDiscipline").val(1) 
                $("#verDisciplina_atraso").css("visibility"," visible")
                $("#btnDisciplina").attr("class","fa fa-chevron-down")}
            else{
                $("#verDisciplina_atraso").css("visibility","hidden")
                $("#moreDiscipline").val(0) 
                $("#btnDisciplina").attr("class","fa fa-chevron-right")}});})
</script>



tgg