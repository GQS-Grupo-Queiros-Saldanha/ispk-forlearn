<?php

namespace App\Mail;

use App\Modules\Payments\Models\Payment;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class NewPayment extends Mailable
{
    use Queueable, SerializesModels;

    public $articleName;
    public $transactionUid;
    public $totalValue;
    public $userName;

    /**
     * Create a new message instance.
     *
     * @param Payment $payment
     */
    public function __construct(Payment $payment)
    {
        $payment->load('user', 'article');
        $payment->article->load('currentTranslation');

        $this->articleName = $payment->article->currentTranslation->display_name;
        $this->transactionUid = $this->formatTransactionUid($payment->transaction_uid);
        $this->totalValue = $this->formatTotalValue($payment->total_value);
        $this->userName = $payment->user->name;
    }

    protected function formatTransactionUid($original)
    {
        return substr($original, 0, 3) . ' ' . substr($original, 3, 3) . ' ' . substr($original, 6, 3);
    }

    protected function formatTotalValue($original)
    {
        return number_format($original, 2, ",", ".");
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.new-payment');
    }
}
