        <style>
  
        .card-tools{
            display: none;
        }
        .error{
            font-size: 15pt;
        }

        </style>
        <div class="col-12">
            <div class="card card-primary ">
              <div class="card-header">
                <h3 class="card-title">Escrever nova mensagem</h3>
              </div>
              <form action="{{route('central-notification.store')}}"  method="POST">
                @csrf
                
               
              <!-- /.card-header -->
              <div class="card-body" >
                  {{-- {{$notificacao[2]->id}} --}}
                @php 
                    $is_apoio = isset($apoio) && $apoio;  
                @endphp
                @if (auth()->user()->hasAnyPermission(['estudante_envia_mensagem']))
                <div class="form-group">
                   <h3>Apoio ao estudante</h3>
                  <input class="form-control"   placeholder="Destinatário: Instituição de ensino" name="studant" readonly title="Este campo representa a entidade em que será enviada a mensagem do estudante." value="studant" type="hidden">
                </div>
                @else 
                  <div class="form-group col pl-0">
                    <small>Canal</small>
                    <select name="canal" id="meio" class="selectpicker form-control autor"
                      data-actions-box="false" data-selected-text-format="count > 10"
                      data-live-search="false" required placeholder="Para:"
                      onchange="controlSubjectField()">
                      {{-- <option value="">Nenhum seleccionado</option> --}}
                      <option value="0">forLEARN®</option>
                      <option value="1">Whatsapp</option>
                      <option value="2">SMS</option>
                  </select>

                   <div class="form-group">
                      <small>Destinatário(s)</small>
                      <select name="to[]" id="destinarios" multiple  class="selectpicker form-control autor" data-actions-box="true" data-selected-text-format="count > 10" data-live-search="true" required placeholder="Para:" >
                          @foreach ($users as $user)
                            <option value="{{ $user->user_whatsapp }}">
                              @if(isset($user->user_whatsapp) && $user->user_whatsapp !== '')
                                {{ $user->name }} ({{ $user->user_whatsapp }})
                              @endif
                            </option>
                          @endforeach
                      </select>
                   </div>  
                @endif
                
                
                <div class="form-group">
                  <input class="form-control" placeholder="Assunto:" name="subject" required>
                  <input type="file" class="form-control" placeholder="@arquivo:" name="file" id="file">
                </div>
                <div class="form-group">
                    {{-- <textarea id="compose-textarea" class="form-control" style="height: 300px ; font-size:14pt;"  name="body">
                      
                    </textarea> --}}

                    <textarea class="form-control" id="mensagem-ckeditor" name="body" placeholder="Escreve a sua mensagem aqui!😊" >
                     
                  </textarea>
                </div>
               
              </div>
              <!-- /.card-body -->
              <div class="card-footer">
                <div class="float-right">
                  <button type="submit" class="btn btn-primary" id="btn-sms" apoio="{{ $is_apoio ? 1 : 0 }}">
                      <i class="far fa-envelope"></i> Enviar
                 </button>

                </div>
              </div>
                <input type="hidden" id="ticket" name="ticket" value=""/>
            </form>
              <!-- /.card-footer -->
            </div>

            


@section('scripts')
@parent
<script src="https://cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
<script>

  const fileButton = document.getElementById('file');
    fileButton.addEventListener('click', function (event) {
    event.preventDefault();
    alert('Função de upload de ficheiro ainda não implementada');
  });


  (()=>{
  CKEDITOR.replace( 'mensagem-ckeditor');

  const destinarios = document.querySelector('#destinarios');
  const btnSms = document.querySelector("#btn-sms");
  const ticket = document.querySelector("#ticket");
  
  verifered();
  
  @if($is_apoio)
      ajaxGenerotorTick();
  @endif
  
  btnSms.addEventListener("click",(e)=>{
      let all = destinarios.querySelectorAll('option:checked')
      all.forEach(item => {
          let email = item.innerHTML.trim()
          if(email.includes("{{ $email_apoio }}")){
              ajaxMessage();
          }
      })
  })
  
  destinarios.addEventListener("change",(e) => {
      let all = destinarios.querySelectorAll('option:checked')
      all.forEach(item => {
          let email = item.innerHTML.trim()
          if(email.includes("{{ $email_apoio }}")){
              ajaxGenerotorTick();
          }
      })
  })
  
  function ajaxGenerotorTick(){
      axios.get("{{ route('generator_ticker') }}")
      .then((response) => {
          ticket.value = response.data.code;
      })
  }
  
  function ajaxMessage(){
          const name = $('#name-info').html();
          const email = $('#email-info').html();
          const abrev = $('#abrev-info').html();
          const assunto = $("[name='subject']").val();
          const destinarios = $('#destinarios');
          const mensagem = CKEDITOR.instances['mensagem-ckeditor'].getData();
          
          axios.post("https://forlearn.ao/send_for_message.php", {
              nome: name,
              email: email,
              assunto: assunto,
              mensagem: mensagem,
              instituicao: abrev,
              ticket: ticket.value,
          }, {
              headers: {
                  'Content-Type': 'application/x-www-form-urlencoded'
              }
          }).then((response) =>{
              //localStorage.setItem("enviado",response.data.enviado);
          })
  }
  
  function verifered(){
          const enviado = localStorage.getItem("enviado");
          if(enviado === "yes"){
              Swal.fire('Successo!','A mensagem foi enviada com','success');
          }else if(enviado === "no" || enviado === "no-en"){
              Swal.fire('Erro!','Não possível enviar a mensagem!','error');
          }     
          localStorage.removeItem("enviado");
  }
  })();
</script>
@endsection
