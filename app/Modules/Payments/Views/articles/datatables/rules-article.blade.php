@if(auth()->user()->hasRole('superadmin'))
    <button class='btn btn-sm btn-danger' data-toggle="modal" data-type="delete" data-target="#modal_confirm" type="submit"
        onclick="createRouteDelete('{{ route('del.RegraEmolumento', $item->id_ruleArtc) }}')">
        @icon('fas fa-trash-alt')
    </button>
@endif

<script>
    function createRouteDelete(url){
        $('#form_modal_confirm').attr('action',url);
        $('#form_modal_confirm [name="_method"]').val("DELETE");
    }
</script>