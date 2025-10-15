<?php

namespace App\Modules\Payments\Models;

use App\Model;
use App\Modules\Users\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Modules\Payments\Models\Transaction
 *
 * @property int $id
 * @property int $article_request_id
 * @property string $type
 * @property float $value
 * @property string|null $notes
 * @property int $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Modules\Payments\Models\ArticleRequest $article_request
 * @property-read \App\Modules\Users\Models\User $createdBy
 * @property-read \App\Modules\Users\Models\User|null $deletedBy
 * @property-read \App\Modules\Users\Models\User|null $updatedBy
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\Transaction newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\Transaction newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Modules\Payments\Models\Transaction onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\Transaction query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\Transaction whereArticleRequestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\Transaction whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\Transaction whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\Transaction whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\Transaction whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\Transaction whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\Transaction whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\Transaction whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\Transaction whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\Transaction whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\Transaction whereValue($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Modules\Payments\Models\Transaction withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Modules\Payments\Models\Transaction withoutTrashed()
 * @mixin \Eloquent
 * @property-read \App\Modules\Payments\Models\TransactionInfo $transaction_info
 */
class Transaction extends Model
{
    use SoftDeletes;

    protected $table = 'transactions';

    protected $fillable = [
        'type',
        'value',
        'notes',
        'data_from'
    ];

    //================================================================================
    // Relations
    //================================================================================

    public function article_request()
    {
        return $this
            ->belongsToMany(ArticleRequest::class, 'transaction_article_requests', 'transaction_id', 'article_request_id')
            ->withPivot(['value']);
    }

    public function transaction_info()
    {
        return $this->hasOne(TransactionInfo::class);
    }

    public function transaction_receipts()
    {
        return $this->hasOne(TransactionReceipt::class, 'transaction_id', 'id');
    }

    //================================================================================
    // Creators
    //================================================================================

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

}
