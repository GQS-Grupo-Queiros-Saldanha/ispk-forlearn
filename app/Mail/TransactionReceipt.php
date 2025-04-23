<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class TransactionReceipt extends Mailable
{
    use Queueable, SerializesModels;

    public $receipt;
    public $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($receipt, $user)
    {
        $this->receipt = $receipt;
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $receiptCode = Carbon::parse($this->receipt->created_at)->format('y') . '-' . $this->receipt->code;
        $attachment = public_path($this->receipt->path);

        $data = [
            'user' => $this->user
        ];

        return $this
            ->subject("Recibo #$receiptCode")
            ->markdown('emails.payments.receipt', $data)
            ->attach($attachment);
    }
}
