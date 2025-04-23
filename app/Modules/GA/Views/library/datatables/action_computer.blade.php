<th>


    <a class="gerar-pdf btn btn-info btn-sm" target="_blank" rel="noopener noreferrer" style="width:30px;"
        href="{{ route('library-computer-pdf', $item->codigo) }}">
        <i class="fas fa-file-pdf"></i>
        <p class="d-none id">{{ $item->codigo }}</p>
    </a>



    {{-- Criando um metodo para verificar se pertence --}}

    @switch($item->estado_requisicao)
        @case('Em curso')
            @if (auth()->user()->hasAnyPermission(['library_manage_request']))
                <button data-target="#modalComputadorFinalizar" data-toggle="modal"
                    class="card-box-footer btn btn-success btn-sm finalizar-computador" style="width:30px;height: 30px;">
                    <i class="fas fa-handshake"></i>
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
    $(".finalizar-computador").click(

        function() {

            codigo_devolucao = $(this).children(".id").text();
            btn_devolver_computador = $(this);
            $(".modal-confirm h4").text('Finalizar requisição?');
            $("#modalComputadorFinalizar .modal-confirm p").show();
            $("#modalComputadorFinalizar .material-icons").text("info");
            $("#modalComputadorFinalizar .btn-cancelar-computador").text("Cancelar");
            $("#modalComputadorFinalizar .btn-finalizar").show();

        }
    );
</script>
