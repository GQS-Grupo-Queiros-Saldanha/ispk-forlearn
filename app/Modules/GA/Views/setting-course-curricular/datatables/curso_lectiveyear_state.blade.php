<style>

    .estado-aberto {
        background-color: #28a745;
        padding: 2%;
        text-align: center;
        color: white !important;
    }

    .estado-fechado {
        background-color: #b81919;
        padding: 2%;
        text-align: center;
        color: white !important;
    }

</style>



@if ($item->state == 0)

    <p class="estado-aberto"> Aberto </p> 

@else

<p class="estado-fechado"> Fechado </p> 

@endif