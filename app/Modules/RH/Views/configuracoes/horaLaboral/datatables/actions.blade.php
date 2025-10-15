

{{-- <button data-toggle="modal" data-type="editar" data-target="#editar_horasLaboral" class="btn btn-warning btn-sm btn-editar-subsidio"><i class="fas fa-edit"></i></button> --}}
<button data-toggle="modal" value-id="{{$item->id}}" value-dias="{{$item->dias_trabalho}}" value-entrada_1="{{$item->entrada_1}}" value-saida_1="{{$item->saida_1}}" value-entrada_2="{{$item->entrada_2}}" value-saida_2="{{$item->saida_2}}" data-type="editar" data-target="#editar_horasLaboral" class="btn btn-warning btn-sm btn-editar-horasLaboral"><i class="fas fa-edit"></i></button>


{{--
<button data-toggle="modal"  value="{{$item->id}}" data-type="delete" data-target="#delete_horasLaboral"  class="btn btn-info btn-sm btn-delete-horasLaboral"><i class="fas fa-trash-alt"></i></button>
--}}

<script>
    
    $(".btn-delete-horasLaboral").click(function (e) { 
        var getId=$(this).attr('value');
        $("#formRoute_delete-horasLaboral").attr('action','delet-horas-laboral/'+getId)
        $("#getId").val(getId)

    });


    $(".btn-editar-horasLaboral").click(function (e) { 
        // console.log(1254);
        
        var getId=$(this).attr('value-id');
        var getDias=$(this).attr('value-dias');
        var getEntrada_1=$(this).attr('value-entrada_1');
        var getSaida_1=$(this).attr('value-saida_1');
        var getEntrada_2=$(this).attr('value-entrada_2');
        var getSaida_2=$(this).attr('value-saida_2');
        
        
        $("#idHorasLaboral").val(getId)
        $("#dias_trabalho").val(getDias)
        $("#entrada_1").val(getEntrada_1)
        $("#saida_1").val(getSaida_1)
        $("#entrada_2").val(getEntrada_2)
        $("#saida_2").val(getSaida_2)

        // console.log(descricao)
        // $("#editarSubsidio").attr('hidden',false)^        
        
        $("#editarhorasLaboral").attr('hidden',false)
        $("#formRoute-Edita-horasLaboral").attr('action','edit-horas-laboral')
        // console.log(getId)
        
    });


</script>

