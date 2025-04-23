@section('title',__('Turmas'))
@extends('layouts.backoffice')

@section('content')

<!-- Calendar filter -->
<div class="content-panel">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1 class="m-0 text-dark">@lang('Documentos')</h1>
                </div>
                <div class="col-sm-6">
                    {{-- {{ Breadcrumbs::render('profile') }} --}}
                </div>
            </div>
        </div>
    </div>

    {{-- Main content --}}
    <div class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="search-bar">
                        <input type="text" id="pesquisa" placeholder="Pesquisar"/>
                        <i class="fas fa-search"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Calendar -->
<div class="content-panel" style="margin-bottom: 10px;">
    <div class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">

                <div class="col-sm-6">
                </div>
            </div>
        </div>
    </div>

    {{-- Main content --}}
    <div class="content">
        <div class="container-fluid">
                <div class="col-12">
                        <table class="document table">
                            <thead>
                                <tr>
                                    <th style="width: 100px;">
                                        <span>ficheiro</span>
                                    </th>
                                    <th style="width: 100px;">
                                        <span>nome do documento</span>
                                    </th>
                                    <th style="width: 100px;">
                                        <span>ver</span>
                                    </th>
                                    <th style="width: 100px;">
                                        <span>transferir</span>
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="documents">


                            </tbody>
                        </table>
                    </div>
            <div class="row">
            </div>
        </div>
    </div>
</div>
<div style="float: right;">
<button type="submit" class="btn forlearn-btn add" >@icon('fas fa-edit')Editar</button>
<button type="submit" class="btn forlearn-btn add" ><i class="fas fa-print"></i>Imprimir</button>
</div>
@endsection
@section('scripts')
@parent

<script>

    let dados = [
        {type: 'pdf', title: 'Blue Silence', ext: 'pdf'},
        {type: 'word', title: 'The Wanton Stream', ext: 'doc'},
        {type: 'pdf', title: 'Ice of Gift', ext: 'pdf'},
        {type: 'pdf', title: 'The Visions Edge', ext: 'pdf'},
        {type: 'excel', title: 'The Word of the Moons', ext: 'xls'},
        {type: 'pdf', title: 'Visions in the Slave', ext: 'pdf'},
        {type: 'pdf', title: 'Companions Without Duty', ext: 'pdf'},
        {type: 'pdf', title: 'Sword Of Wind', ext: 'pdf'},
        {type: 'word', title: 'Leading The End', ext: 'doc'},
        {type: 'pdf', title: 'Learning From Myself', ext: 'pdf'},
        {type: 'pdf', title: 'Achievement Of Joy', ext: 'pdf'},
        {type: 'excel', title: 'Death Of The Forest', ext: 'xls'},
        {type: 'excel', title: 'Blacksmith Of Despair', ext: 'xls'},
        {type: 'pdf', title: 'Witches And Cats', ext: 'pdf'},
    ];
    	$("#pesquisa").on("keyup", function(){
            let text = $(this).val();
            pesquisa(text);
        });

        function pesquisa(text){
            let html = "";
            let result = $.grep(dados, function(value){
                return value.title.toLocaleLowerCase().indexOf(text.toLocaleLowerCase()) > -1;
            });

            $.each(result, function(k,v){
                html += "<tr><td><i class='fas fa-file-"+v.type+"'></i></td><td>"+v.title+"</td><td><a target='_blank' href='http://ispm.hocnet.org/developer2/dummy/test."+ v.ext +"'><i class='fas fa-eye'></a></i></td><td><a download href='http://ispm.hocnet.org/developer2/dummy/test."+ v.ext +"'><i class='fas fa-save'></i></a></td></tr>";
            });

            $("#documents").html(html);

        }

        pesquisa("");
</script>

@endsection
