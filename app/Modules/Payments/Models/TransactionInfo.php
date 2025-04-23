<?php

namespace App\Modules\Payments\Models;

use App\Model;
use App\Modules\Users\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Modules\Payments\Models\TransactionInfo
 *
 * @property int $id
 * @property int $transaction_id
 * @property int $bank_id
 * @property string|null $fulfilled_at
 * @property string|null $reference
 * @property-read \App\Modules\Payments\Models\Bank $bank
 * @property-read \App\Modules\Payments\Models\Transaction $transaction
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\TransactionInfo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\TransactionInfo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\TransactionInfo query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\TransactionInfo whereBankId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\TransactionInfo whereFulfilledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\TransactionInfo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\TransactionInfo whereReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\TransactionInfo whereTransactionId($value)
 * @mixin \Eloquent
 */
class TransactionInfo extends Model
{
    public $timestamps = false;

    protected $table = 'transaction_info';

    protected $fillable = [
        'transaction_id',
        'bank_id',
        'fulfilled_at',
        'reference',
        'bank_1_id',
        'bank_2_id',
        'reference_1',
        'reference_2',
        'fulfilled_at_1',
        'fulfilled_at_2',
        'value'
    ];

    //================================================================================
    // Relations
    //================================================================================

    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

}
