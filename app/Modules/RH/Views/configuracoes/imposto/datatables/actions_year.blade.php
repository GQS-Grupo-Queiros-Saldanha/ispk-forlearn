@php $id=null @endphp
@php $getIds=$item->id_impostoYear.','.$item->id_imposto @endphp

@if ($item->status=='panding')
<button value="{{$item->id_impostoYear}}" data-toggle="modal" data-type="delete" data-target="#delete_imposto" class="btn btn-info btn-sm btn-delete-impostoYear"><i class="fas fa-trash-alt"></i></button>
    <button value="{{$item->id_impostoYear}}" data-toggle="modal" data-type="editar" data-target="#editar_imposto" class="btn btn-warning btn-sm btn-editar-impostoYear"><i class="fas fa-edit"></i></button>
@endif
@foreach ($getTaxaYearImposto as $element)
    @if($element->id_impostoYear == $item->id_impostoYear && $id==null)
        @php $id=$element->id_impostoYear @endphp
      <button  data-year="{{$item->year_month}}"  data-id="{{$item->id_impostoYear}}" id="copyImpostoYear" class="btn btn-success btn-sm copyImpostoYear"><i class="fas fa-copy"></i></button>
    @endif
@endforeach
<a href="{{ route('recurso.taxa_impostos', ['id'=>$getIds]) }}" class="btn btn-dark btn-sm "><i class="fas fa-t"></i></a>
<script>
    $(".copyImpostoYear").click(function (e) { 
        var getId=$(this).attr('data-id')
        var year=$(this).attr('data-year')
        var formCopyImposto=$("#formCopyImposto")
        var idImpostoCopy=$("#idImpostoCopy")
        idImpostoCopy.val(getId)
        $("#copyYear").text(year)
        formCopyImposto.attr('hidden',false)
        get_impostoData()
    });
    $(".btn-delete-impostoYear").click(function (e) { 
        var getId=$(this).val();
        $("#formRoute_delete-imposto").attr('action','{{ route('recurso.deleteImpostoYear')}}')
        $("#getId").val(getId)
    });

    $(".btn-editar-impostoYear").click(function (e) { 
        var getId=$(this).val();
        $("#editarImpostoYear").attr('hidden',false)
        $("#formRoute-Edita-impostoYear").attr('action','{{ route('recurso.Edita-impostoYear')}}')
        $("#idyearImposto").val(getId)
    });
</script>

