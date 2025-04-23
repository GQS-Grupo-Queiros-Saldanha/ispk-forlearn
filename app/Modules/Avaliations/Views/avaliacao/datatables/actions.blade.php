
@if ($item->avaliacao_lock == 0 and $item->calend_id_avaliacao<0  and  $item->deleted_by == null)
    <button  style="margin-right: 0px; margin-left: -10.5px" class="btn btn-primary btn-sm data_avaliacao my-1" data-toggle="modal" data-target="#insertMetrica"
        data-user_id="{{$item->avaliacao_id}}"><i class="fas fa-plus"></i>
    </button>

@elseif ($item->calend_id_avaliacao >0 and $item->deleted_by == null and $item->avaliacao_lock == 0 )
    <button  style="margin-right: 0px; margin-left: -10.5px" class="btn btn-primary btn-sm data_avaliacao my-1" data-toggle="modal" data-target="#insertMetrica"
        data-user_id="{{$item->avaliacao_id}}"><i class="fas fa-plus"></i>
    </button>
@else
    <button class="btn btn-primary btn-sm my-1 mx-1 btn-open-avaliacao" data-toggle="modal" data-target="#"  data-avaliacao="{{$item->avaliacao_id}}" title="abrir calendário">
        <i class="fab fa-osi"></i>
    </button>
@endif

<button class="btn btn-info btn-sm data_avaliacao my-1 mx-1" data-toggle="modal" data-target="#showMetrica"
    data-user_id="{{$item->avaliacao_id}}">
    <i class="fas fa-eye"></i>
</button>


<button class="btn btn-warning btn-sm data_avaliacao my-1 mx-1" data-toggle="modal" data-target="#editAvaliacao"
    data-user_id="{{$item->avaliacao_id}}">
    <i class="fas fa-edit"></i>
</button>

<button class="btn btn-sm btn-danger my-1 mx-1" data-toggle="modal" data-type="delete" data-target="#modal_confirm"
    data-action="{{ json_encode(['route' => ['avaliacao.destroy', $item->avaliacao_id], 'method' => 'delete', 'class' => 'd-inline']) }}"
    type="submit">
    <i class="fas fa-trash-alt"></i>
</button>
    @if($item->calend_id_avaliacao == true and $item->deleted_by!= null)
        <a  class="btn btn-sm btn-dark my-1 mx-1" target=""
            href="{{ route('avaliacao_data.cadastro', ['id'=>$item->avaliacao_id,'menu_avalicao'=>true]) }}">
            <i class="fas fa-calendar"></i>
        </a>  
    @elseif ($item->calend_id_avaliacao == false)
    <a  class="btn btn-sm btn-dark my-1 mx-1" target=""
        href="{{ route('avaliacao_data.cadastro', ['id'=>$item->avaliacao_id,'menu_avalicao'=>true]) }}">
        <i class="fas fa-calendar"></i>
    </a>
    @else
    <a  class="btn btn-sm btn-light my-1 mx-1" target=""
        href="{{ route('school-exam-calendar.index')}}">
        <i class="fas fa-calendar-alt"></i>
    </a>
@endif


{{-- @if ($item->code_dev!=null)    --}}
    <button class="btn btn-success btn-sm data_avaliacao my-1 mx-1" data-toggle="modal" data-target=""
        data-user_id="{{$item->avaliacao_id}}" onclick="duplicar(this)">
        <i class="fas fa-copy"></i>
    </button>
{{-- @endif --}}


<script class="script-page">
    const btnOpenAvaliacao = $('.btn-open-avaliacao');

    eliminarbtn();

    function eliminarbtn(){
        let scrips = $('.script-page');
        let tam = scrips.length;
        for(let i = 0; i <= tam-1; i++)
            $(scrips[i]).remove();
    }

    btnOpenAvaliacao.on('click',function(e){
        let obj = $(this);
        let avaliacao = obj.attr('data-avaliacao');
        swalRunnable("Tens certeza que desejas abrir de novo este calendário",(()=>{
            $.ajax({
                url: "{{ route('avaliacao.open') }}",
                type: "POST",
                data: {
                    _token: '{{ csrf_token() }}',
                    avaliacao: avaliacao
                },
                success: function(dataResult) {
                    if(dataResult == 1){
                        window.open("{{ route('avaliacao.index') }}", "_self");
                    }
                }
            })
        }))
    })

</script>
