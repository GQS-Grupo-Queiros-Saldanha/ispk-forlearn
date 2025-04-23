<button  data-date="{{$item->data}}" value="{{$item->id_funcionario}}" type="button"  data-toggle="modal" data-target="#modal-detalhe-hora" class="btn btn-info btn-sm btn-ver-hora-trabalho"><i class="fas fa-eye"></i></button>

<script>
    var timePass=0;
    $(".btn-ver-hora-trabalho").click(function (e) { 
        var id_funcionario=$(this).val()
        var data=$(this).attr('data-date')
        $("#lista-data-day").empty();
        timePass++;
        $(".link-gerar-pdf").attr('href','');
        $(".link-gerar-pdf").attr('href','recurso-humano-controlo-tornique-PDF/'+id_funcionario+'/'+data);
        $.ajax({
            url: "controlo-catraca-day-funcionario/"+id_funcionario+"/"+data,
            type: "GET",
            data: {
                _token: '{{ csrf_token() }}'
            },
            cache: false,
            dataType: 'json',
        }).done(function(response){ 
            console.log(response)
            var tr=null;
            var i=0;
            var data=response['day_data'];
            var entrada="Não marcou a entrada";
            var saida="Não marcou a saída";
            var data_month=data.split('-')
            data_month=data_month[0]+'-'+data_month[1];
            $("#lista-data-day").empty();
            $("#total-dia").text(response['day_data']);
            $("#total-data-month").text(data_month);
            $("#total-dia-hora").text(response['total_day']+'hr');
            $("#total-month").text(response['taotal_month']+'hr');
            $(".nome-funcionario").text(response['data'][0].nome_funcionario);
            $.each(response['data'], function (index, item) { 
                if (item.data==response['day_data']) {
                    i++;
                    if (item.hora_entrada!=null) { 
                        entrada=item.hora_entrada;}
                    if(item.hora_saida!=null){
                        saida=item.hora_saida }
                    tr+="<tr><td>"+i+"</td><td>"+item.data+"</td><td>"+entrada+"</td><td>"+saida+"</td><td>"+item.total_Entrada+"hr </td>"
                    tr+="</tr>"
                }
            });

            $("#lista-data-day").append(tr);
        })
    });
</script>


