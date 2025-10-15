<style>
    .div-top {
        height:99px;
        text-transform: uppercase;
        position: relative;

        margin-bottom: 15px;
        /*background-color: rgb(240, 240, 240);*/
        /*background-image: url('{{ asset('img/CABECALHO_CINZA01GRANDE.png') }}');*/
        /*background-image: url('/img/CABECALHO_CINZA01GRANDE.png');*/            
        background-image: url('https://forlearn.ispm.ao/storage/{{$institution->logotipo}}');
        background-position: left;
        background-repeat: no-repeat;
        background-size: 10%;
    }
     .h1-title_Com {
        padding: 0;
        margin-bottom: 0;
        font-size:4.3em;
        font-weight: bold;
       
       
       
    }
    

</style>


<div class="div-top" style="">
    <table class="table m-0 p-0">
                
            <td class="td-declaracao" rowspan="12" style="background-color: transparent; height:96px; left:200px;">
               
                 <h1 class="h1-title_Com" style="padding-top:9px; text-align: right; font-size:30pt;">
                    Orçamento nº {{$orcamento->id}}
                 <p style="font-size:18pt; margin-top:-15px; text-align: right;margin-right: 0!important"> {{$orcamento->name}}</p>
                </h1>
             
               
            </td>

        </tr>


    </table>
    
     {{-- <label style="float:right; font-size:12pt;position:absulute; margin-top:0px;">Decreto Presidencial nº 168/12 de 24 de Julho</label> --}}
</div>