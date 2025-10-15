
<button data-id="{{$item->id}}" data-nome="{{$item->display_name}}" data-code="{{$item->code}}" data-toggle="modal" data-target="#editarbanco"  class="btn btn-warning btn-sm btn-editar">
    <i class="fas fa-edit"></i>
</button>
{{--
<a class="btn btn-sm btn-danger" href="{{ route('recursoHumano.deleteBanco', ['id'=>$item->id]) }}">
    <i class="fas fa-trash-alt"></i>
</a>
--}}

<script>
    $(".btn-editar").click(function (e) { 
        var getid =$(this).attr('data-id');
        var getnome=$(this).attr('data-nome'); 
        var getcode=$(this).attr('data-code');

        $("#id_bank").val(getid);
        $("#nome").val(getnome);
        $("#code").val(getcode); 
    });

</script>
