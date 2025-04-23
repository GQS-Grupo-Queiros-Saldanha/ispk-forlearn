<style>
  
    .card-tools{
        display: none;
    }
    .error{
        font-size: 15pt;
    }
    p{
        font-size: 15pt;
    }
    #img{
        width:60px;
        height:60px;
        border-radius:30px;
        margin:4px;
        border:none;
        background-color: #999;
    }
    #img:hover{
       cursor: pointer;
    }
    #li_entrada{
        background-color: black;
    }
     #li_entrada a{
        color: white;
     }
    </style>
    <?php
        
        if(isset($singleNotification)){
            deletar_restaurar_ler($array[]=$singleNotification->id,"ler");
            
      ?>

        <div class="col-12">

            <div class="card">

              <div class="card-header">

                <img id="img" src="{{asset('storage/attachment/'.$singleNotification->image)}}" alt="imagem do remitente" class=" float-left">
                <h3 class="card-title">Assunto:<b> {{$singleNotification->subject}}</b> </h3>
                <h3 class="card-title">{{$central_control=="to"?"Destinatário":"De"}}: <b>{{$singleNotification->name}} << <small> {{$singleNotification->email}}</small> >>
                </b></h3>
                <h3 class="card-title">Data: <b>{{$singleNotification->date_sent}}</b></h3>

              </div>

              <div class="card-body" >
                 <div class="form-group">
                     <label for="">Mensagem</label>
                     <div id="Mensagem">
                        {{$singleNotification->body_messenge}}

                     </div>
                
                    </br>
                     @if ($singleNotification->file!=null)
                     <p> 
                         <a target="_blank" href="{{ asset($singleNotification->file) }}" class="btn btn-primary" style="border-radius:4px; color:white;">
                            <i class="fas fa-paperclip">
                                Abrir Anexo
                            </i>
                         </a>
                    </p>
                    <iframe src="{{$singleNotification->file}}#toolbar=0" width="100%" height="500px">

                    </iframe>  
                    @endif
                </div>
               
              </div>
              
              @if ($central_control=="trash_view")
                <div class="card-footer p-0">
                    <div class="mailbox-controls" style="padding: 2px;">
                
                    <div class="btn-group">
                        <button type="button" class="btn btn-default btn-sm" id="trash_id_restart" title="Clique neste botão para restaurar mensagem para sua caixa de entrada!" data-id="{{$singleNotification->id}}">
                        <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                    
                    </div>
              @endif

              </div>
            <?php

             }
            
            else{
                        
            ?>
            <center>
                <p >Ocorreu um erro na visualização da notificação. Tente novamente!</p>
                <h1>😥</h1>
                <a href="{{route('central-notification.index')}}" class="btn btn-primary" style="border-radius:4px; color:white; padding:10px 30px;">Voltar</a>
            </center> 
  
            <?php
                 }
            ?>
            
         
        </div>
            @section('scripts')
            @parent
            <script>
                

                var texto=$("#Mensagem").text();
                    $("#Mensagem").text(""); 
                    $("#Mensagem").html(texto)
               

                                                                                                        
                    
            </script>
            @endsection