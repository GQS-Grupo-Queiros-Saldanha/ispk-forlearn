@component('mail::message')
Caro(a) {{ $user->name }},<br>
<br>
Segue em anexo uma cópia do recibo pedido.<br>
<br>
Com os melhores cumprimentos,<br>
ISPM
@endcomponent
