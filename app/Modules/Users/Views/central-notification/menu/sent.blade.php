<style>

    .card-tools{
        display: none;
    }
    .error{
        font-size: 15pt;
    }
    #li_enviada{
        background-color: black;
    }
     #li_enviada a{
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
            <td>Enviado(a) para</td>   
            <td>Assunto</td>    
            <td>Mensagem</td>  
            <td><i class="fas fa-paperclip"></i></td>  
            <td>Data</td>  
        </tr>     
    </thead> 
<tbody>
@forelse ($notificacao=enviada()  as $key=> $item) 
<tr>
  <td>
    <div class="icheck-primary">
      <input type="checkbox" value="{{$item->id}}" id="check1">
      <label for="check1"></label>
    </div>
  </td>
  <td class="mailbox-star "><a href="#"><i class="{{$item->star!=null ? "fas fa-star text-warning":"fas fa-star-o text-warning"}}"></i></a></td>
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
    <p id="error">Lixeira encontra vazia.</p>
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
        <button type="button" class="btn btn-default btn-sm" id="trash_id" title="Excluir">
          <i class="fas fa-rotate-right"></i>
          

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