@if (auth()->user()->hasRole(['superadmin']))
    <a href="{{ route('delete.matriculation_finalista', ['id' => $item->id_matriculation_finalist]) }}"
        class="btn btn-info btn-sm btn-delete-finalista"><i class="fas fa-trash-alt"></i></a>
@endif
<a target="_blank" href="{{ route('boletim.finalista', ['id' => $item->id_matriculation_finalist]) }}"
    class="btn btn-info btn-sm btn-delete-finalista"><i class="fas fa-file-pdf" aria-hidden="true"></i></a>
@foreach ($getStudent as $value)
    @if ($value->user_id == $item->user_id)
        <a target="_blank" class="btn btn-sm  btn-info" href="{{ route('user_requests', $value->id_matricula) }}"><i
                class="fa-solid fa-t"></i>
    @endif
@endforeach
</a>

<a target="_blank" class="btn btn-sm  btn-warning" href="{{ route('users.show', $item->user_id) }}">
    <i class="fa fa-user" aria-hidden="true"></i>
</a>

<a target="_blank" class="btn btn-sm  btn-info" href="{{ route('academic-path.percurso', $item->user_id) }}">
    <i class="fa-solid fa-p"></i>
</a>

@if (auth()->user()->hasAnyPermission(['Anular_matricula']) ||
        auth()->user()->hasAnyRole(['superadmin']))
    <button title="Anualação de matrícula" data-user="{{ $item->user_id }}" data-ident="{{ $item->id_matriculation_finalist }}"
        data-name="{{ $item->name_full }}" data-code="{{ $item->num_confirmaMatricula }}" class="btn btn-sm btn-danger anular"
        data-toggle="modal" data-type="anular_matricula" data-target="#anulate_matricula" type="submit">
        <i class="fas fa-user-times"></i>
    </button>
@endif

{{-- <button  data-target="#editar_imposto" class="btn btn-warning btn-sm btn-editar-imposto"><i class="fas fa-edit"></i></button> --}}
{{-- <a href="{{ route('user_requests', $value->id_matricula) }}" class="btn btn-dark btn-sm"><i class="fas fa-t"></i></a>     --}}
<script>
    $(".anular").click(function(e) {

        var matricula_id = $("#matricula_id");
        var nome_completo = $("#nome_completo");
        var turma = $("#turma-vw");
        var n_confirmacao = $("#n_confirmacao");
        $(".boxObservation").empty();

        var getUser = $(this).attr('data-user');
        var getName = $(this).attr('data-name');
        var getCode = $(this).attr('data-code');

        matricula_id.val($(this).attr('data-ident'));
        nome_completo.val(getName);
        n_confirmacao.val(getCode);

        if(!turma.hasClass('d-none'))
            turma.addClass('d-none');

        // var getLectiveYear=$(this).attr('data-lective');
        var getLectiveYear = $("#lective_years")[0].selectedOptions[0].text;

        $("#nome").text(getName);
        $("#n_mat").text(getCode);
        $("#ano_lectivo").text(getLectiveYear);

        $('#processar_anaulacao').attr('action','{{route('anulate.matriculation_finalist.store')}}');
    });
</script>