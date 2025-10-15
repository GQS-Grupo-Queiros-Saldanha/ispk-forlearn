@foreach ($requerimento as $item)
    @if (($state->codigo_estudante == $item->user_id) && ($state->art_id==$item->article_id))
        @if ($state->status == 'total')
            <center>
                @if ($item->codigo_documento==5)
                <button class='btn btn-sm btn-warning'  onclick="word(this)" data-user="{{ $item->user_id }}" data-toggle="modal"
                    data-target="#ModalWord" type="submit">
                    @icon('fas fa-edit')
                </button>
                @endif
            </center>
        @else
        <center>
            <button class='btn btn-sm btn-danger delete_budget' onclick="pegar(this)" data="{{ $item->id }}" data-type="emolumento" data-toggle="modal"
                data-target="#Modalconfirmar" type="submit">
                @icon('fas fa-trash-alt')
            </button>
        </center>
        @endif
    @endif
@endforeach