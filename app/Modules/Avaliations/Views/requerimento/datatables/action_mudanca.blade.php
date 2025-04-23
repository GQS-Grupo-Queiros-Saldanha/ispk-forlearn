
@if ($item->status == 'total')

@else
<center> 
<button class='btn btn-sm btn-danger delete_budget' onclick="pegar(this)" data="{{ $item->id }}" data-type="mudanca_curso" data-toggle="modal"
    data-target="#Modalconfirmar" type="submit">
    @icon('fas fa-trash-alt')
</button>
</center>
@endif