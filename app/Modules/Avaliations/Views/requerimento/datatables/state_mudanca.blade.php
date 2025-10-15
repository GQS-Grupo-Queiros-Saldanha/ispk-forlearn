
    @if ($status_change->status_change == 'done')
        <span class='bg-success p-1 text-white'>CONCLUIDO</span>
    @elseif($status_change->status_change == 'pending')
        <span class='bg-info p-1'>ESPERA</span>
    @endif

   

