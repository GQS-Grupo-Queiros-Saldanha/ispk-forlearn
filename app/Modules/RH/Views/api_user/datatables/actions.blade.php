{{-- <a href="#" class="btn btn-info btn-sm">
    @icon('far fa-eye')
    data-toggle="modal" data-target="#exampleModal"
</a> --}}

<button class="btn btn-warning btn-sm btn-upds" key="{{$item->id}}"  fullname="{{$item->name}}" email="{{$item->email}}" telefone-principal="{{$item->telefone_principal}}" telefone-altenativo="{{$item->telefone_altenativo}}">
    @icon('fas fa-edit')
</button>

{{-- @if(auth()->user()->hasRole('superadmin'))
    <button class='btn btn-sm btn-danger' data-toggle="modal" data-type="delete" data-target="#delete-api-user"
            data-action="{{ json_encode(['route' => ['api.delete_user'], 'method' => 'delete', 'class' => 'd-inline']) }}"
            type="submit">
        @icon('fas fa-trash-alt')
    </button>
@endif --}}

<button value="{{$item->id}}" data-toggle="modal" data-type="delete" data-target="#delete_api_user" class=" p-2 btn btn-info btn-sm btn-delete-api-user">
    <i class="fas fa-trash-alt"></i>
</button>



<script>
    var bank_id=null;
    var btnUpds = $('.btn-upds');
    $('.btn-delete-api-user').click(function (e) { 
        var getId=$(this).val();
        $("#id_api_user").val(getId);
    });

    btnUpds.click(function (e) { 
        let element = $(this);
        $('#chave').val(element.attr('key'));
        $('#full_name_m').val(element.attr('fullname'));
        $('#email_m').val(element.attr('email'));
        $('#telefone_principal_m').val(element.attr('telefone-principal'));
        $('#telefone_altenativo_m').val(element.attr('telefone-altenativo'));
        $('#exampleModal').modal('show');
     });

    // $(".eliminar-user").click(function (e) { 
    //       api_user_id=$(this).val()
    //       $("#id_api_user").val(api_user_id)
    // });


    // $('.btn-delete-api-user').click(function (e) { 

    //     $("#id_api_user").text(api_user_id)
    //     // $("#delete-api-user").modal('hide')


    //     // $.ajax({
    //     //     url: 'recurso_humano-eliminar-banco-funcionario/'+api_user_id,
    //     //     type: "GET",
    //     //     data: {
    //     //         _token: '{{ csrf_token() }}'
    //     //     },
    //     //     cache: false,
    //     //     dataType: 'json',
    //     // }).done(function(data)  {
    //     //     $('.alerta').prop('hidden',false)
    //     //     $('.alerta').text(data)
    //     //     setTimeout(() => {
    //     //         $('.alerta').prop('hidden',true);
    //     //         $('#banks-table').DataTable().clear().destroy();
    //     //         getLista_bank_user()
                
    //     //     }, 2900); 
    //     // })
    
        
    // });


</script>