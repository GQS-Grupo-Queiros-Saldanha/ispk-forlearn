<a href="#" class="btn btn-warning btn-sm btn-up" chave="{{$item->id}}" lective_yer="{{$item->id_years}}" title="editar informações">
    @icon('fas fa-edit')
</a>

<a href="{{route('fase.candidatura.users',$item->id)}}" class="btn btn-info btn-sm" title="listar candidatos">
    @icon('fas fa-users')
</a>

<script class="script-page">

    eliminasScript();
    var btnUp = $('.btn-up');

    btnUp.click(function(e){
        let objSelected = $(this);
        let html="";
        let options = [];
        let row =  objSelected.parent().parent().children();

        let year = row[1].innerHTML;
        let data_inicio = row[2].innerHTML;
        let data_fim = row[3].innerHTML;
        let fase_n = row[4].innerHTML;
        
        const dataEnd = $('#data_end');
        const dataStart = $('#data_start');

        lectiveYear.children().each((index,item)=>{
            options.push({value:item.value, innerHTML:item.innerHTML});
        });

        lectiveYear.html('');
        options.forEach(item => {
            html += `<option value="${item.value}" ${item.innerHTML == year ? 'selected' :''}>${item.innerHTML}</option>`;
        });
        lectiveYear.html(html);
        
        numFase.val(fase_n);//.attr('min', fase_n);
        dataStart.val(data_inicio);//.attr('min', data_inicio);
        dataEnd.val(data_fim);//.attr('min', data_fim);
        
        modalFase.modal('show');
        form.attr('action', '{{ route('fase.candidatura.update') }}');
        $("#form [name='_method']").val('PUT');
        $('#chave').val(objSelected.attr('chave'));
        $('#lective_year').val(objSelected.attr('lective_yer'));
    });

    function eliminasScript(){
        let scrips = $('.script-page');
        let tam = scrips.length;
        if(tam > 1)
            for(let i = 0; i <= tam-1; i++)
                $(scrips[i]).remove();
    };
    
</script>