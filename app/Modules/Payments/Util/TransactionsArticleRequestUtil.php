<?php

namespace App\Modules\Payments\Util;

use App\Modules\Payments\Models\ArticleRequest;
use App\Modules\Payments\Models\Transaction;
use Carbon\Carbon;
use Exception;
use DB;

class TransactionsArticleRequestUtil
{
    public function articleRequests($articleRequests, $transaction)
    {
        foreach ($articleRequests['requests'] as $articleRequestId => $articleRequestData) {
            if ($articleRequestData['paid'] > 0) {
                $thi->parcial($articleRequestId, $articleRequestData, $transaction);
            }
        }
    }

    public function parcial($articleRequestId, $articleRequestData, $transaction)
    {
        $consultaArt = DB::table('article_requests')->where('article_requests.id', '=', $articleRequestId)->first();
        if ($consultaArt->extra_fees_value > 0) {
            if ($consultaArt->extra_fees_value < $articleRequestData['tax']) {
                $ar = ArticleRequest::findOrFail($articleRequestId);
                $ar->extra_fees_value = (float) $articleRequestData['tax'] ? $articleRequestData['tax'] : $ar->extra_fees_value;
                $ar->status = $articleRequestData['state'];
                $ar->estado_extra_fees = 2;
                $ar->save();
            } else {
                $ar = ArticleRequest::findOrFail($articleRequestId);
                $ar->extra_fees_value = (float) $articleRequestData['tax'] ? $articleRequestData['tax'] : $ar->extra_fees_value;
                $ar->status = $articleRequestData['state'];
                $ar->estado_extra_fees = 3;
                $ar->save();
            }
        } else {
            $ar = ArticleRequest::findOrFail($articleRequestId);
            $ar->extra_fees_value = (float) $articleRequestData['tax'] ? $articleRequestData['tax'] : $ar->extra_fees_value;
            $ar->status = $articleRequestData['state'];
            $ar->estado_extra_fees = $articleRequestData['tax'] == 0 ? 0 : 1;
            $ar->save();
        }

        $transaction->article_request()->attach($ar->id, ['value' => $articleRequestData['paid']]);

        if ($articleRequestData['cretid_saldo'] == false) {
            $qtd_saldoPagamento = $user->credit_balance - $articleRequests['user'];
            $historic_user_balance = DB::table('historic_user_balance')->insert([
                'id_user' => $userId,
                'id_transaction' => $transaction->id,
                'valor_credit' => $qtd_saldoPagamento,
                'data_from' => null,
            ]);
        }

        if ($articleRequestData['applyTax'] && $articleRequestData['state'] != 'total') {
            $taxTransaction = Transaction::create([
                'type' => 'debit',
                'value' => $ar->extra_fees_value,
                'notes' => 'Débito do valor de taxa aplicado.',
                'data_from' => 'Multa',
            ]);
            $taxTransaction->article_request()->attach($ar->id, ['value' => $ar->extra_fees_value]);
        }
    }
    
     public function total($articleRequestId, $articleRequestData, $transaction)
     {   
         $ar = ArticleRequest::findOrFail($articleRequestId);
         $ar->update(['status' => 'total']);
     }
    
}
