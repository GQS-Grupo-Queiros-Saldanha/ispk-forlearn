                    <style>

                        .card-tools{
                            display: none;
                        }
                        .error{
                            font-size: 15pt;
                        }
                        #li_lixeira{
                          background-color: black;
                         
                        }
                        #li_lixeira a{
                          /*background-color: black;*/
                          color: white;
                        }
                    </style>
                  <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <td>Marcar</td>    
                            <td></td>     
                            <td>Assunto</td>    
                            <td>Mensagem</td>  
                            <td>ficheiro</td>  
                         
                            <td>Data de exclusão</td>  
                        </tr>     
                    </thead> 
                    <tbody>
                  @php
                      $i=0;
                  @endphp
                    @forelse ($notificacao=deletar_restaurar_ler(null,"paginate")  as $key => $item) 
                    <tr>
                      <td>
                        <div class="icheck-primary">
                          <input type="checkbox" value="{{$item->id}}" id="check1">
                          @if ($item->state_read===null)
                           <label for="check1"><i class="fas fa-envelope text-warning"></i></label>
                           @endif
                        </div>
                      </td>
                      <td class="mailbox-star"><a href="#"><i class="fas fa-star" style="color:#999;"></i></a></td>

                      {{--<td class="mailbox-name"><a href="#" onclick="abrir(this)" data-id="{{$item->id}}">{{$item->name}}</a></td> --}}

                      <td class="mailbox-name" onclick="abrir(this)" data-id="{{$item->id}}" ><a href="#">{{$item->subject}}</a></td>

                      <td class="mailbox sms_{{$i++}}" onclick="abrir(this)" data-id="{{$item->id}}"">
                          {{ nl2br (mb_strimwidth($item->body_messenge, 0, 190, '...')) }}
                      </td>

                      <td class="mailbox-attachment"> 
                        @if($item->file!=null)
                          <a href="">
                            <i class="fas fa-paperclip"></i>
                          </a>
                        @endif
                      </td>
                      <td class="mailbox-date" onclick="abrir(this)" data-id="{{$item->id}}">{{$item->deleted_at}}</td>
                    </tr>

                    @empty
                    <center>
                        <p id="error">A lixeira encontra-se vazia.</p>
                    </center>
                    @endforelse  

                    </tbody>
                  </table>
                  <!-- /.table -->
                  <div class="card-footer p-0">
                    <div class="mailbox-controls" style="padding: 9px;">
                        
                      <button type="button" class="btn btn-default btn-sm">
                        {{$notificacao->links()}}
                        <i class="fas fa-sync-alt"></i>
                      </button>
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
      
                     
                   
      
                                                                                                              
                          
                  </script>
                  @endsection