<style>
    .see-more{transition: all 0.5s;}
    .see-more:hover{cursor: pointer;background: rgb(255, 255, 255);font-size: 0.9pc;transition: all 0.5s;padding: 1.5px;
    }
</style>
@php $sub; @endphp
[ 
@foreach ($subsidiosImposto as $element)
    @if ($element->id_subsidio==$item->subsidio_id)
        @php $sub=substr($element->display_name, 0, 5); @endphp
        <a data-toggle="modal" data-id_imposto="{{$element->id_imposto}}" data-target="#delete_subsidioImposto" style="text-decoration: none;" data-idSubsidio="{{$element->id_subsidio}}" href="#" class="see-more btn-subsidio">{{$element->display_name}},</a>    
    @endif
@endforeach
]

<script>
    var getIdSubsidio;
    var getId_imposto;
    var setGets;
    $('.btn-subsidio').click(function() {
         getIdSubsidio=$(this).attr('data-idSubsidio');
         getId_imposto=$(this).attr('data-id_imposto');
         setGets=getIdSubsidio+','+getId_imposto;
    });

    $(".btn-deleteSubImposto").click(function (e) { 
        $("#delete_subsidioImposto").modal('hide')
       
       $.ajax({
            url: 'configuracoes-deletedSubsidio_withImposto/' + setGets,
            type: "GET",
            data: {
                _token: '{{ csrf_token() }}'
            },
            cache: false,
            dataType: 'json',
        }).done(function (data) {
            // setTimeout(() => {
            //     location.reload(true);
            // }, 400);
            console.log(data)    
        }); 
    });
</script>