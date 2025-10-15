
<button data-toggle="modal"  value="{{$item->id}}" data-type="delete" data-target="#delete-configuracao" class="btn btn-info btn-sm btn-delete-configDivida"><i class="fas fa-trash-alt"></i></button>
@if ($item->status!="ativo")
   <a href="{{ route('ativar.config_divida', ['id'=>$item->id]) }}" class="btn btn-dark btn-sm" data-toggle="tooltip" data-placement="right" title="Definir Regra"><i class="fas fa-star"></i></a> 
@else
   <a href="#" class="btn btn-warning btn-sm" data-toggle="tooltip" data-placement="right" title="Regra actual"><i class="fas fa-star"></i></a> 
    
@endif

<script>
   $(".btn-delete-configDivida").click(function (e) { 
      var getId=$(this).val();

      $("#formRoute-delete-confiDivida").prop('action','{{ route("delete-divida.configuracao")}}');
      $("#getId").val(getId);
      
   });
</script>