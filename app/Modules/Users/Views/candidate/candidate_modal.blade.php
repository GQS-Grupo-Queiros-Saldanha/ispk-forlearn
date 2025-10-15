<!-- Modal -->
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-dialog-centered" role="document">
    <div class="modal-content">
      <div class="modal-header bg-danger text-light">
        <h5 class="modal-title" id="exampleModalLongTitle">Atenção candidado a estudante. </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div>
          <p class="text-danger" style="font-weight:bold; !important">
            A sua pré-candidatura foi realizada com sucesso.
          </p>
        </div>
         <br>
      <p>
        Obrigado por teres concluído o preenchimento do formulário de candidatura que apresentamos em anexo
      </p>
      <br>
      <p>
        Por favor, imprima o respectivo formulário, e em conjunto com os documentos originais desloque-se ao <b> ISPM</b> para proceder ao pagamento do emolumento.
      </p>
      <br>
        <p>
            Sem mais de momento,<br>
            Com os melhores cumprimentos,
        </p>
        <p>
        <br>
        <b>O seu staff no ISPM.</b>
        </p>
      </div>
      <div class="modal-footer">
        <a href="{{ route('candidate.generate_pdf', $user->id)}}" class="btn btn-danger" target="_blank">
            <i class="fas fa-plus-square"></i>
            Gerar documento .pdf
        </a>

        {{-- <button type="button" class="btn btn-primary">Entendí</button> --}}
      </div>
    </div>
  </div>
</div>