@if ($item->is_duplicate == 0)
    <button class="btn btn-sm btn-info btn-copy" href="{{ route('copy.graduado', $item->is_user) }}"
        onclick="confirmCopyGraduado('{!! route('copy.graduado', $item->is_user) !!}')">
        @icon('fas fa-users')
    </button>

    <script class="scripts">
        removeScripts();

        function removeScripts() {
            const scripts = document.querySelectorAll(".scripts");
            const tam = scripts.lenght;
            for (let i = 1; i < tam; i++) {
                scripts[i].remove();
            }
        }

        function confirmCopyGraduado(url) {

            Swal.fire({
                title: 'Confirmação?',
                text: "Tens certeza que desejas fazer a candidatura deste graduado",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sim, confirmo!',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.open(url, "_self");
                }
            })

        }
    </script>
@endif
