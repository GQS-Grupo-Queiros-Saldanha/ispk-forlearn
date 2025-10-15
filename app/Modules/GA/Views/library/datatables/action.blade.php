<th>

   
    <a class="gerar-pdf btn btn-info btn-sm" target="_blank" rel="noopener noreferrer" style="width:30px;" href="{{ route('library-create-pdf',$item->codigo )}}">
        <i class="fas fa-file-pdf"></i>
        <p class="d-none id">{{ $item->codigo }}</p>
    </a>

    <button class="btn btn-info btn-sm b-detalhes" data-target="#modalLivroRequisitados" data-toggle="modal"
        style="width:30px;">
        <i class="fas fa-eye"></i>
        <p class="d-none id">{{ $item->codigo }}</p>
        <p class="d-none nome_leitor">{{ $item->leitor_nome }}</p>
        <p class="d-none cd_referencia">{{ $item->referencia }}</p>
    </button>

    {{-- Criando um metodo para verificar se pertence --}}   

    @switch($item->estado)
        @case('Em curso')
        @if (auth()->user()->hasAnyPermission(['library_manage_item']))
        <button data-target="#modalDevolverLivro" data-toggle="modal" class="btn btn-success btn-sm devolverLivro"
        style="width:30px;">
        <i class="fas fas fa-handshake"></i>
        <p class="d-none name"></p>
        <p class="d-none id">{{ $item->codigo }}</p>
    </button>
    @endif
        @break

        @case('Finalizada')
        @break

        @default
    @endswitch
</th>


<script> 

    $(".devolverLivro").click(

        function() {

            codigo_devolucao = $(this).children(".id").text();
            btn_devolver = $(this); 

            $("#modalDevolverLivro .modal-confirm h4").text('Finalizar requisição?');
            $("#modalDevolverLivro .modal-confirm p").show();
            $("#modalDevolverLivro .material-icons").text("info");
            $("#modalDevolverLivro .btn-cancelar-livro").text("Cancelar");
            $("#modalDevolverLivro .btn-eliminar-livro").show();
            $("#modalDevolverLivro .btn-devolver-livro").show();

        }
    );
 
    $(".b-detalhes").on('click', function() {

        var id = $(this).children(".id").text();


        $(".R-referencia").text("Requisicão Nº: " + $(this).children(".cd_referencia").text());
        $(".leitor-nome").text($(this).children(".nome_leitor").text());

        tabela_livro_lidos(id);


    });

    function tabela_livro_lidos(id) {

        let tabela = $('#tabela-livros-requisitados').DataTable({
            destroy: true,
            searching: false,
            serverSide: false,
            processing: false,
            aLengthMenu: [5],
            orderable: false,
            paging: true,
            buttons: [
                // 'colvis'
                //   ,{
                //       text: "Todos",
                //       className:"btn"
                //     }

            ],
            language: {
                url: '{{ asset('lang/datatables/' . App::getLocale() . '.json') }}',
            },
            "ajax": {
                "url": "library-get_book-loan/" + id,
                "type": "GET"
            },
            columns: [{
                    data: 'DT_RowIndex',
                    orderable: false
                },
                {
                    data: 'codigo_livro',
                    name: 'codigo_livro'
                },
                {
                    data: 'livro_titulo',
                    name: 'livro_titulo'
                },
                {
                    data: 'livro_subtitulo',
                    name: 'livro_subtitulo'
                },
                {
                    data: 'livro_isbn',
                    name: 'livro_isbn'
                }
            ]

        });

        setTimeout(function() {

            $(".lidos").text("Total: " + $("#tabela-livros-requisitados tbody tr").length);

        }, 1000);
    }

</script>
 