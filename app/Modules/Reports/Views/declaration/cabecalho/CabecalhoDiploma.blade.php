
 
@php
    $logotipo = 'https://' . $_SERVER['HTTP_HOST'] . '/instituicao-arquivo/' . $institution->logotipo;
    
    $logotipo = link_storage($logotipo);
    
@endphp
<style>
    .div-top {
        height: 150px;
        text-transform: none;
        background-color: white;
        background-image: url("{{$logotipo}}");
        background-position: center;
        background-repeat: no-repeat;
        background-size: 150px;
        margin-top:25px;
    }
    
    
    .h1-title {
        height: 100px;
        background-position: center;
        color: black;
        background-repeat: no-repeat;
        margin-top:0px;
        font-size:50px;
        letter-spacing:1px;
        text-transform:uppercase !important;
        font-weight:bolder;
    }
    
    .watermark {
    opacity: 0.2;
    color: black;
    position: fixed;
    top: 150px;
    background-image: url("{{$logotipo}}");
    background-position: center;
    background-repeat: no-repeat;
    background-size: 500px;
    height: 800px;
    width: 100%;
    left: -5px;
    pointer-events: none; /* Garante que a marca d'água não interfira na interação com outros elementos */
}

    
     .div-top_f {
         margin-top: 0px!important;
         margin-bottom: -30px!important;
     }


#decreto {
          padding:0px!important;
          margin-left:120px!important;
      }
    
</style>

<div class="div-top" style="">
</div>

<div class="div-top_f" style="">
  
    <table class="table m-0 p-0 top">
        <tr>
         
            <td class="td-declaracao" rowspan="12"
                style="background-color: transparent; text-align:center;">

                <h1 class="h1-title">
                    Diploma
                </h1>


            </td>

        </tr>


    </table>

</div>
