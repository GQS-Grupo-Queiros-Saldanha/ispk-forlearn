<style>
    #li_entrada {
        background-color: black;
    }

    #li_entrada a {
        color: white;
    }
</style>

@if(isset($notification["notification"]) && (count($notification["notification"])>0))

    <table class="table table-hover table-striped">
        <thead>
            <tr>
                <td style="width:10px!important;">#</td>
                <td>Assunto</td>
                <td>Mensagem</td>
                <td><i class="fas fa-paperclip"></i></td>
                <td>Data e hora</td>
            </tr>
        </thead>
        <tbody id="Tabela_lista_mensagem">


            @php
                $i = 0;
                $count = 0;
            @endphp
            @forelse ($notification["notification"]  as $item)
                <tr>
                    <td style="width:10px!important;@if(!($item->state_read == '')) {{ "font-weight: normal!important; "}} @endif ">
                
                    {{ ++$count }}
                    </td>

                    @php
                        $class = $item->star == null ? 'fas fa-star' : 'fas fa-star';
                        $cor = $item->star == null ? 'color:#999;' : 'color:#ffed4a!important;';
                    @endphp
                    <td class="mailbox-name" style="@if(!($item->state_read == '')) {{ "font-weight: normal!important; "}} @endif " >
                        <a style="text-decoration: none;color:black;" target="_blank" href="central-notification/{{ $item->id }}">
                            @if ($item->state_read == '')
                                <i class="fa-solid fa-envelope"></i>
                                {{ $item->state_read }}
                            @endif
                            {{ $item->subject }}
                        </a>
                    </td>
                    <td class="mailbox sms_{{ $i++ }}" onclick="abrir(this)" data-id="{{ $item->id }}" style="@if(!($item->state_read == '')) {{ "font-weight: normal!important; "}} @endif " >
                        {{ nl2br(mb_strimwidth($item->body_messenge, 0, 190, '...')) }}
                    </td>
                    <td class="mailbox-attachment"
                        style="@if(!($item->state_read == '')) {{ "font-weight: normal!important; "}} @endif " >
                        @if ($item->file != null)
                            <a href="{{ asset($item->file) }}" target="_blank">
                                <i class="fas fa-paperclip"></i>
                            </a>
                        @endif
                    </td>
                    <td class="mailbox-date" onclick="abrir(this)" data-id="{{ $item->id }}"
                        style="@if(!($item->state_read == '')) {{ "font-weight: normal!important; "}} @endif " >
                        {{ $item->date_sent }}</td>
                </tr>

            @empty
                <center>
                    <p>Sem notificações</p>
                </center>
            @endforelse


        </tbody>
    </table>
    @section('scripts')
    @parent
    <script>
        var count = '{{ $i }}';

        for (let index = 0; index < count; index++) {
            var texto = $(".sms_" + index).text();
            $(".sms_" + index).text("");
            $(".sms_" + index).html(texto)
            //depois tirar o espaço e incluir
            var format = $(".sms_" + index).text().replace(/\s/g, ' ');
            $(".sms_" + index).html(format)
        }


        $("#Pesquisar").click(function(e) {

            buscar();
        });
        $("#caixa_pesquisar").bind("change keypress", function(e) {
            buscar();
        });





        function buscar() {

            var pesquisa = $("#caixa_pesquisar").val();
            var token = $(this).data("token");
            $.ajax({
                url: "{{ route('pesquisar_notificacao') }}",
                data: {
                    "pesquisa": pesquisa,
                    "_token": token,
                },
                dataType: "json",

                beforeSend: function() {
                    if (pesquisa == "") {

                        console.log("sem nada na caixa");
                        return false;
                    }

                },
                success: function(e) {
                    var body = '';
                    if (e.length > 0) {
                        $("#Tabela_lista_mensagem").empty();
                        body += "<tr>"
                        $.each(e, function(index, valor) {
                            body += "<td>";
                        });
                        showData += "</tr>"
                        $("#Tabela_lista_mensagem").html(e);
                        console.log(e)
                    } else {
                        console.log("vazio")
                    }

                }
            });
        }
    </script>
@endsection
@else 





<div class="alert alert-warning text-dark font-bold">Sem notificações! </div>
@endif
