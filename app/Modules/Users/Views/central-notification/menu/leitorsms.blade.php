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
        @isset($_GET['order'] )
        @php
            $position=base64_decode($_GET['order']);
            @endphp 
        <?php
            
        if(isset($notificacao[$position]->id)){
            deletar_restaurar_ler($array[]=$notificacao[$position]->id,"ler");

         ?>
        <div class="col-12">
            <div class="card">
              <div class="card-header">
                <img id="img" src="{{asset('storage/attachment/'.$notificacao[$position]->image)}}" alt="imagem do remitente" class=" float-left">
                <h3 class="card-title">Assunto:<b> {{$notificacao[$position]->subject}}</b> </h3>
                <h3 class="card-title">De: <b>{{$notificacao[$position]->name}} << <small> {{$notificacao[$position]->email}}</small> >>
                </b></h3>
                <h3 class="card-title">Data: <b>{{$notificacao[$position]->date_sent}}</b></h3>
              
              </div>
                
              <div class="card-body" >
                 <div class="form-group">
                     <label for="">Mensagem</label>
                     <div id="Mensagem">
                        {{$notificacao[$position]->body_messenge}}

                     </div>
                
                    </br>
                     @if ($notificacao[$position]->file!=null)
                     <p> 
                         <a target="_blank" href="{{ asset($notificacao[$position]->file) }}" class="btn btn-primary" style="border-radius:4px; color:white;">
                            <i class="fas fa-paperclip">
                                Abrir Anexo
                            </i>
                         </a>
                    </p>
                    <iframe src="{{$notificacao[$position]->file}}#toolbar=0" width="100%" height="500px">

                    </iframe>  
                    @endif
                </div>
               
              </div>
      
              </div>
            <?php

            }
            
            else{
                        
            ?>
            <center>
                <p >Ocorreu um erro na visualização da notificação. Tente novamente!</p>
                <h1>😥</h1>
                <a href="?central-control=inbox" class="btn btn-primary" style="border-radius:4px; color:white; padding:10px 30px;">Voltar</a>
            </center> 
  
            <?php
                 }
            ?>
            
            @endisset

            @if (!isset($_GET['order']))

            <center>
                <p >Ocorreu um erro na visualização da notificação. Tente novamente!</p>
                <h1>😥</h1>
                <a href="?central-control=inbox" class="btn btn-primary" style="border-radius:4px; color:white; padding:10px 30px;">Voltar</a>
            </center>  

            @endif
        </div>
            @section('scripts')
            @parent
            <script>
                

                var texto=$("#Mensagem").text();
                    $("#Mensagem").text(""); 
                    $("#Mensagem").html(texto)
               

                                                                                                        
                    
            </script>
            @endsection