{{--<style>
    .t-color {
        color: #fc8a17;
    }

    .signature-forlearn{
        margin-top: 1%;
    }
</style>
<div class="row signature-forlearn">

    <div class="col-6 text-left sign-date">
        <p><as style="text-transform: capitalize;"> {{ $institution->municipio }}</as>,
        @php
            $m = date('m');
            $mes = ['01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março', '04' => 'Abril', '05' => 'Maio', '06' => 'Junho', '07' => 'Julho', '08' => 'Agosto', '09' => 'Setembro', '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro'];
            echo date('d') . ' de ' . $mes[$m] . ' de ' . date('Y');
        @endphp</p>
    </div>
    <div class="col-6 text-right"><span class="t-color"> Powered by</span> <b
            style="color:#243f60;font-size: 20px;margin-top:10px;">forLEARN <sup>®</sup></b>
    </div>

</div>
--}}