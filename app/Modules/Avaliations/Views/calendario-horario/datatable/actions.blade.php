@php
    use App\Modules\Avaliations\util\CalendarioProvaHorarioUtil;
    $jurisProva = CalendarioProvaHorarioUtil::juris($item->id_horario,$item->periodo);
@endphp

<a href="{{ route('calendario_prova_horario.edit', ['id' => $item->id_horario]) }}" class="btn btn-warning btn-sm">
    @icon('fas fa-edit')
</a>

@if (sizeof($jurisProva) > 0)
    <button class='btn btn-sm btn-success btn-prova-horario' data-toggle="modal" data-type="delete"
        data-target="#exampleModalJuris" url="{{ route('ajax.juris') }}?prova_horario={{ $item->id_horario }}&periodo={{ $item->periodo}}"
        alt="Visualizar Juris">
        @icon('fas fa-users')
    </button>
@endif

<button class='btn btn-sm btn-danger btn-delete-prova' data-toggle="modal" data-type="delete" data-target="#exampleModalProva"
 prova_horario="{{ $item->id_horario }}">
    @icon('fas fa-trash-alt')
</button>

<script class="script-juri">
    const btnDeleteProva = $(".btn-delete-prova");
    const btnProvaHorarios = $(".btn-prova-horario");

    function scriptJuriClear() {
        const scripts = document.querySelectorAll(".script-juri");
        if (scripts.length > 1) {
            for (let i = 1; i < scripts.length; i++) {
                scripts[i].remove();
            }
        }
    }

    scriptJuriClear();

    btnProvaHorarios.on('click', function(e) {
        $.ajax({
            url: $(this).attr('url'),
        }).done((data) => {
            const tbodyJuris = $('#tbody-juris');
            let html = "";
            data.forEach(item => {
                html += `<tr>
                            <td>${item.name}</td>
                            <td>${item.email}</td>
                            <td>
                                <button class="btn btn-sm btn-danger" type="submit" name="juri" value="${item.juri_id}">
                                    <i class="fas fa-times"></i>
                                </button>
                            </td>
                        </tr>`;
            });
            tbodyJuris.html(html);
        }).fail((jqXHR, status, error) => {

        });
    });

    btnDeleteProva.on('click', function(e){
        const inputProva = $("#exampleModalProva #prova_horario");
        inputProva.val($(this).attr('prova_horario'));
    });

</script>
