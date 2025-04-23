
<style>
    .see-more{
        transition: all 0.5s;
        border-radius: 2px;
    }
    .see-more:hover{
        cursor: pointer;
        background: rgb(255, 255, 255);
        font-size: 1pc;
        transition: all 0.5s;
        padding: 1.5px;
    }
</style>
[
@foreach ($banks as $bank)
    @if($item->id_user === $bank->id_user)
        <button data-toggle="modal" data-target="#delete-bank" value="{{$bank->id}}"   class="see-more eliminar-bank" title="Nº da Conta: {{$bank->conta}}&#013;&#010;IBAN: {{$bank->iban}}&#013;&#010;Entrada em vigor: {{$bank->created_at}}&#013;&#010;OBS: Click para eliminar este banco.">
           &nbsp; {{$bank->banco}},
        </button>
    @endif
@endforeach
]

<script>
    var bank_id=null;
    $(".eliminar-bank").click(function (e) { 
          bank_id=$(this).val()
    });
    $('.btn-delete-bank-user').click(function (e) { 
       $("#delete-bank").modal('hide')
        $.ajax({
                url: 'recurso_humano-eliminar-banco-funcionario/'+bank_id,
                type: "GET",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                cache: false,
                dataType: 'json',
            }).done(function(data)  {
                $('.alerta').prop('hidden',false)
                $('.alerta').text(data)
                setTimeout(() => {
                    $('.alerta').prop('hidden',true);
                    $('#banks-table').DataTable().clear().destroy();
                    getLista_bank_user()
                    
                }, 2900); 
            })
    
        
    });
</script>
