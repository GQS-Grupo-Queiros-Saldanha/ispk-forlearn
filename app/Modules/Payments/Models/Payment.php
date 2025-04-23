<?php

namespace App\Modules\Payments\Models;

use App\Model;
use App\Modules\Users\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Modules\Payments\Models\Payments
 *
 * @property int $id
 * @property int $enrollments_id
 * @property int $users_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\Payment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\Payment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\Payment query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\Payment whereEnrollmentsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\Payment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\Payment whereUsersId($value)
 * @mixin \Eloquent
 * @property int $article_id
 * @property string|null $transaction_uid
 * @property string|null $fulfilled_at
 * @property float $total_value
 * @property float $base_value
 * @property float|null $extra_fee
 * @property int $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Modules\Payments\Models\Article $article
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\App\Modules\Payments\Models\Payment onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\Payment whereArticleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\Payment whereBaseValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\Payment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\Payment whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\Payment whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\Payment whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\Payment whereExtraFee($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\Payment whereFulfilledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\Payment whereTotalValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\Payment whereTransactionUid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\Payment whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\Payment whereUpdatedBy($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Modules\Payments\Models\Payment withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Modules\Payments\Models\Payment withoutTrashed()
 * @property-read \App\Modules\Users\Models\User $createdBy
 * @property-read \App\Modules\Users\Models\User|null $deletedBy
 * @property-read \App\Modules\Users\Models\User|null $updatedBy
 * @property int $user_id
 * @property int|null $month
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\Payment whereMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\Payment whereUserId($value)
 * @property-read \App\Modules\Users\Models\User $user
 * @property float $total_paid
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\Payment whereTotalPaid($value)
 * @property string|null $free_text
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\Payment whereFreeText($value)
 */
class Payment extends Model
{

    use SoftDeletes;

    protected $table = 'payments';

    protected $fillable = [
        'article_id',
        'user_id',
        'year',
        'month',
        'total_value',
        'base_value',
        'extra_fee',
        'free_text'
    ];

    //================================================================================
    // Relations
    //================================================================================

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
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
