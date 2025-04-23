<a href="#" class="btn btn-warning btn-sm btn-up mx-1" chave="{{ $item->id }}" title="Editar informações">
    @icon('fas fa-edit')
</a>
<a href="#" class="btn btn-danger btn-sm" data-id="{{ $item->id }}"
    onclick="event.preventDefault(); deleteItem(this);" title="Excluir categoria">
    @icon('fas fa-trash')
</a>
<style>
    .custom-modal .modal-dialog {
        position: absolute;
        top: 30%;
        left: 30%;
        transform: translate(-50%, 0);
        width: 100%;
        max-width: 500px;
    }

    a {
        margin: 1.5px;
    }
</style>

<!-- Modal de Confirmação -->
<div class="modal fade custom-modal" id="modal_confirm" tabindex="-1" role="dialog" aria-labelledby="ModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="ModalLabel">Confirmar Exclusão</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <span style="color:black;"> Tem certeza que deseja excluir este item?</span>
                <span class="modal-confirm-text"></span>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn forlearn-btn" id="confirmDelete">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-check" viewBox="0 0 16 16">
                        <path
                            d="M10.97 4.97a.75.75 0 0 1 1.07 1.05l-3.99 4.99a.75.75 0 0 1-1.08.02L4.324 8.384a.75.75 0 1 1 1.06-1.06l2.094 2.093 3.473-4.425z" />
                    </svg>
                    Ok
                </button>
                <button type="button" class="btn forlearn-btn" data-dismiss="modal">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                        class="bi bi-x" viewBox="0 0 16 16">
                        <path
                            d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708" />
                    </svg>
                    Cancelar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Script -->
<script class="script-page">
    function deleteItem(link) {
        var id = $(link).data('id');
        var deleteUrl = '{{ route('articles.categoria.destroy', ':id') }}'.replace(':id', id);

        // Mostra o modal de confirmação
        $('#modal_confirm').modal('show');

        // Remove qualquer evento click anterior para evitar múltiplas chamadas
        $('#confirmDelete').off('click').on('click', function() {
            $.ajax({
                url: deleteUrl,
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    _method: 'DELETE' // Adiciona o método DELETE para simular a exclusão
                },
                success: function(response) {
                    if (response.success) {
                        location.reload();
                    } else {
                        console.error('Erro ao tentar excluir a fase:', response.message);
                    }
                },
                error: function(xhr) {
                    console.error('Erro ao tentar excluir a fase:', xhr.responseText);
                }
            });
            $('#modal_confirm').modal('hide');
        });
    }

    eliminasScript();
    var btnUp = $('.btn-up');

    btnUp.click(function(e) {
        let objSelected = $(this);
        let html = "";
        let options = [];
        let row = objSelected.parent().parent().children();

        let categoria_name = row[1].innerHTML.trim();
        categoria.val(categoria_name)
        // let tempDiv = document.createElement('div');
        // tempDiv.innerHTML = row[2].innerHTML.trim();
        // categoria.val(categoria_name)

        // // Obtém o primeiro elemento span
        // let spanElement = tempDiv.querySelector('span');

        // // Verifica se o spanElement existe
        // if (spanElement) {
        //     // Obtém o valor do estilo inline diretamente
        //     let inlineStyle = spanElement.style.backgroundColor;

        //     // Se o estilo inline não estiver definido, tente usar o estilo computado
        //     if (!inlineStyle) {
        //         inlineStyle = window.getComputedStyle(spanElement).backgroundColor;
        //     }

        //     // Converte RGB para HEX
        //     function rgbToHex(rgb) {
        //         let rgbArray = rgb.match(/\d+/g);
        //         if (!rgbArray) return ""; // Retorna string vazia se não conseguir fazer a conversão
        //         let r = parseInt(rgbArray[0]);
        //         let g = parseInt(rgbArray[1]);
        //         let b = parseInt(rgbArray[2]);
        //         return "#" + ((1 << 24) + (r << 16) + (g << 8) + b).toString(16).slice(1).toUpperCase();
        //     }

        //     let backgroundColor = inlineStyle || "rgba(0,0,0,0)"; // Valor padrão se não encontrado
        //     let hexColor = rgbToHex(backgroundColor);
        //     cor.val(hexColor); // Exemplo de saída: "#831B1B"
        // }
        modalCategoria.modal('show');
        form.attr('action', '{{ route('articles.categoria.update') }}');
        formMethod.val('PUT');
        $('#chave').val(objSelected.attr('chave'));

    });

    function eliminasScript() {
        let scrips = $('.script-page');
        let tam = scrips.length;
        if (tam > 1)
            for (let i = 0; i <= tam - 1; i++)
                $(scrips[i]).remove();
    };
    $('#modalCategoria').on('hidden.bs.modal', function() {
        $(this).find('form').trigger('reset'); // Limpar todos os campos do formulário
        $(this).find('.alert').addClass('d-none'); // Esconder alerta se estiver visível
        $('#chave').val(''); // Limpar campo chave
    });
</script>
