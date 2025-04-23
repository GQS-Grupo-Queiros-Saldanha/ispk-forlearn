<style>

    #li_entrada{
        background-color: black;
    }
     #li_entrada a{
        color: white;
     }
</style>    


                  <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <td>Marcar</td>    
                            <td></td>    
                            <td>Remetente</td>    
                            <td>Assunto</td>    
                            <td>Mensagem</td>  
                            <td><i class="fas fa-paperclip"></i></td>  
                            <td>Data</td>  
                        </tr>     
                    </thead> 
                    <tbody id="Tabela_lista_mensagem">



                  <form action="{{route('apagar_notificacao')}}" method="post" id="form_notify">
                    @csrf   
                    @php
                        $i=0;
                    @endphp
                    @forelse ($notificacao  as $key => $item) 
                    <tr>
                      <td>
                        <div class="icheck-primary">
                         <input type="checkbox" value="{{$item->id}}" class="check1"  name=deletar[] >    
                           
                           @if ($item->state_read===null)
                           <label for="check1"><i class="fas fa-envelope text-warning"></i></label>
                           @endif
                        </div>
                      </td>
                      
                    @php
                        $class=$item->star==null?"fas fa-star":"fas fa-star";
                        $cor=$item->star==null?"color:#999;":"color:#ffed4a!important;";
                    @endphp
                      <td class="mailbox-star"><a href="#" title="Marcar como favorita" onclick="marcar(this)" data-id="{{$item->id}}" ><i class="{{$class}} estrela_{{$item->id}}" style="{{$cor}}" ></i></a></td>
                      <td class="mailbox-name"><a href="#" onclick="abrir(this)" data-id="{{$item->id}}">{{$item->name}}</a></td>
                      <td class="mailbox-name"><a href="#" onclick="abrir(this)" data-id="{{$item->id}}">{{$item->subject}}</a></td>
                      <td class="mailbox sms_{{$i++}}" onclick="abrir(this)" data-id="{{$item->id}}">
                          {{ nl2br (mb_strimwidth($item->body_messenge, 0, 190, '...')) }}
                      </td>
                      <td class="mailbox-attachment"> 
                        @if($item->file!=null)
                          <a href="{{asset($item->file)}}">
                            <i class="fas fa-paperclip"></i>
                          </a>
                        @endif
                      </td>
                      <td class="mailbox-date" onclick="abrir(this)"  data-id="{{$item->id}}">{{$item->date_sent}}</td>
                    </tr>

                    @empty
                    <center>
                        <p>Sem notificações</p>
                    </center>
                    @endforelse  
              
                  </form>
                    </tbody>
                  </table>
                  <!-- table -->
                  <div class="card-footer p-0">
                    <div class="mailbox-controls" style="padding: 2px;">
                      <!-- Check all button -->
                      <button type="button" class="btn btn-default btn-sm " title="Marcar todos">
                        <input type="checkbox"  id="check_all"  >
                      </button>
                      <div class="btn-group">
                        <button type="button" class="btn btn-default btn-sm" id="trash_id" title="Excluir">
                          <i class="fas fa-trash"></i>
                        </button>
                        {{$notificacao->links()}}
                      </div>
                     
                      </div>


                      @section('scripts')
                      @parent
                      <script>
                        var count='{{$i}}';
                       
                        for (let index = 0; index < count; index++) {
                                var texto=$(".sms_"+index).text();
                                $(".sms_"+index).text("");
                                $(".sms_"+index).html(texto)
                                //depois tirar o espaço e incluir
                                var format= $(".sms_"+index).text().replace(/\s/g,' ');
                                $(".sms_"+index).html(format)
                             }
          
                        
                        $("#Pesquisar").click(function (e) { 

                            buscar();    
                        });
                        $("#caixa_pesquisar").bind( "change keypress",function (e) { 
                            buscar();    
                        });





                    function buscar(){
                      
                          var pesquisa = $("#caixa_pesquisar").val();
                          var token = $(this).data("token");
                          $.ajax({
                            url: "{{route('pesquisar_notificacao')}}",
                            data: {
                               "pesquisa": pesquisa,
                               "_token": token,
                             },
                            dataType: "json",

                            beforeSend:function(){
                             if(pesquisa==""){
                             
                               console.log("sem nada na caixa");
                              return false;
                             }
                             
                            },
                            success: function (e) {
                              var body = '';
                              if(e.length>0){
                                $("#Tabela_lista_mensagem").empty();
                                body += "<tr>"
                                  $.each(e, function (index, valor) { 
                                    body+="<td>" ;
                                  });
                                  showData += "</tr>"
                                $("#Tabela_lista_mensagem").html(e);
                                console.log(e) 
                             }else {
                              console.log("vazio")
                             }

                            }
                          });
                        }
                      </script>
                      @endsection