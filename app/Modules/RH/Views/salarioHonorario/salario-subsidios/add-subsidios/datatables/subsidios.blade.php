<style>
    .see-more{
        transition: all 0.5s;
    }
    .see-more:hover{
        cursor: pointer;
        background: rgb(255, 255, 255);
        font-size: 0.9pc;
        transition: all 0.5s;
        padding: 1.5px;
    }
</style>
@php $sub; @endphp
[ 
@foreach ($getSubsidio as $element)
    @if ($element->id_funcionario==$id_funcionario && $element->id_funcionario_cargo==$item->id_funcionario_cargo)
        @php $sub=substr($element->display_name, 0, 5); 
        @endphp
        <a data-toggle="modal" data-target="#delete_subsidio" data-idCargo="{{$item->id_funcionario_cargo}}" data-idFun="{{$id_funcionario}}" data-idSub="{{$element->id}}" data-toggle="tooltip" data-placement="bottom" title="Descrição: {{$element->discricao}}&#013;&#010;valor:{{number_format($element->valor, 2, ',', '.') }}Kz&#013;&#010;Obs:&#013;&#010;Clica no subsídio para poder eliminar."  class="see-more btn-subsidio">{{$element->display_name}},</a>
    @endif
@endforeach
]

<script>
    var getIdCargo;
    var getIdFun;
    var getIdSub;
    var getIdSubsidio;
    var getIdfuncionario;
    $(".btn-subsidio").click(function (e) { 
        getIdCargo=$(this).attr('data-idCargo');
        getIdFun=$(this).attr('data-idFun');
        getIdSub=$(this).attr('data-idSub');
        getIdSubsidio = getIdFun +','+ getIdCargo +','+ getIdSub;
        getIdfuncionario=$(this).attr('data-idFun');
    });
    $(".btn-delete-subsidio").click(function (e) { 
        $("#delete_subsidio").modal('hide')
        $('#cargo-subsidioFuncionario').DataTable().clear().destroy();
        ajaxSubsidio(getIdfuncionario);
        $.ajax({
            url: 'recurso_deleteSubsidioFuncionario/'+getIdSubsidio,
            type: "GET",
            data: {
                _token: '{{ csrf_token() }}'
            },
            cache: false,
            dataType: 'json',
        }).done(function(data){
            console.log(data)
            if (data['response']==0) {
                $(".alert").attr('hidden',false)
                setTimeout(() => {
                    $(".alert").attr('hidden',true)
                }, 2500);
            } else {
                $(".alert").attr('hidden',true)
                
            }
        });
    });
</script>

