<style>

    .card-tools{
        display: none;
    }
    .error{
        font-size: 15pt;
    }
    #li_favourite{
        background-color: black;
    }
     #li_favourite a{
        color: white;
     }
</style>
@php
$i=0;
@endphp

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
<tbody>
@forelse ($notificacao=deletar_restaurar_ler(null,"favourite")  as $key=> $item) 
<tr>
  <td>
    <div class="icheck-primary">
      <input type="checkbox" value="{{$item->id}}" id="check1">
      <label for="check1"></label>
    </div>
  </td>
  @php
  $class=$item->star==null?"fas fa-star":"fas fa-star";
  $cor=$item->star==null?"color:#999;":"color:#ffed4a!important;";
@endphp
  <td class="mailbox-star" ><a href="#" onclick="marcar(this)"   data-id="{{$item->id}}">
  <i  class="{{$class}} estrela_{{$item->id}}" style="{{$cor}}""></i></a></td>
  <td class="mailbox-name"><a href="#" onclick="abrir(this)" data-id="{{$item->id}}">{{$item->name}}</a></td>
  <td class="mailbox-name"><a href="#" onclick="abrir(this)" data-id="{{$item->id}}">{{$item->subject}}</a></td>
  <td class="mailbox sms_{{$i++}}" onclick="abrir(this)" data-id="{{$item->id}}" >
      {{ nl2br (mb_strimwidth($item->body_messenge, 0, 500, '...')) }}
  </td>
  <td class="mailbox-attachment"> 
    @if($item->file!=null)
      <a href="{{$item->file}}" target="_blank">
        <i class="fas fa-paperclip"></i>
      </a>
    @endif
  </td>
  <td class="mailbox-date" onclick="abrir(this)" data-id="{{$item->id}}" >{{$item->date_sent}}</td>
</tr>

@empty
<center>
    <p id="error">Nenhuma notificação marcada.</p>
</center>
@endforelse  

</tbody>
</table>


<div class="card-footer p-0">
    <div class="mailbox-controls" style="padding: 2px;">
      <!-- Check all button -->
      <button type="button" class="btn btn-default btn-sm " title="Marcar todos">
        <input type="checkbox"  id="check_all"  >
      </button>
      <div class="btn-group">
        <button type="button" class="btn btn-default btn-sm" id="" title="estrelar todas">
          <i class="fas fa-star"></i>
          

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

  </script>
  @endsection