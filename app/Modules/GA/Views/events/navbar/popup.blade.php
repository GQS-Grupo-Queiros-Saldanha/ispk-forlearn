  <!-- Modal para o controlo de session -->
  <div class="modal fade" id="ModalSession" data-backdrop="static" data-keyboard="false" tabindex="-1"
      aria-labelledby="ModalSession" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
          <div class="modal-content">
              <div class="modal-header">
                  <h3 class="modal-title" id="staticBackdropLabel">Sessão</h3>

              </div>
              <div class="modal-body">
                  <center><i class="fas fa-key"
                          style="font-size: 50px;padding: 30px;border: 1px solid #eed28b;border-radius: 60px;color: #000000ba;background-color: #eed28b;"
                          aria-hidden="true"></i></center>
                  <p style="font-size: 25px;text-align: center;">
                      Informamos que a sua sessão está prestes a terminar!!!
                  </p>
              </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-success" style="border-radius:5px;"
                      data-dismiss="modal">Continuar</button>

                  <a href="/logout" type="button" class="btn btn-danger" style="border-radius:5px;">Terminar</a>
              </div>
          </div>
      </div>
  </div>
