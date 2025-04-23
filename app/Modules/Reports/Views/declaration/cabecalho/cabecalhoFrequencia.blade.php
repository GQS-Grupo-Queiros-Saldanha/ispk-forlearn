<br>
<br>
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
        background-position: left;
        background-position-y: 30px;
        background-position-x: 1273px;
        background-repeat: no-repeat;
        background-size: 120px;
        
    }
    .h1-title {
    height: 100px;
    background-image: url("{{$logotipo}}");
    background-size: 380px 80px;
    background-position: center;
    color: transparent;
    background-repeat: no-repeat;
    }
    .watermark {
    opacity: 0.2;
    color: BLACK;
    position: fixed;
    top: 300px;
    background-image: url("{{$logotipo}}");
    background-size: 800px;
    background-position: center;
    background-position-x: 100px;
    background-repeat: no-repeat;
    background-size: 800px; 
    height: 800;
    width: 100%;

}
    
</style>

<div class="div-top" style="">
    <table class="table m-0 p-0">  
        <tr>
            <td class="td-declaracao" rowspan="12"
                style="background-color: transparent; height:96px;">

                <p style="text-align: center;margin-top: 50px;font-weight: 900;font-size: 27px;text-transform: uppercase!important;">
                    <b>{{$institution->nome}}</b> <br> {{$institution->provincia}} - Angola
                </p>
                <p style="text-align: right;margin-top: 10px;font-weight: 600;font-size: 14px;">
                    {{$institution->decreto_instituicao}}
                </p>


            </td>

        </tr>


    </table>

</div>

<div class="div-top_f" style="">
    <table class="table m-0 p-0">
        <tr>
            <td class="td-declaracao" rowspan="12"
                style="background-color: transparent; height:96px;  text-align:center;">

                <h1 class="h1-title">
                  <b> <u> Declaração </u></b>
                </h1>


            </td>

        </tr>


    </table>

</div>
