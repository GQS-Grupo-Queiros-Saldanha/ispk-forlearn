<?php

namespace App\Modules\Payments\Models;

use App\Model;
use App\Modules\Users\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Modules\Payments\Models\ArticleRequest
 *
 * @property int $id
 * @property int $user_id
 * @property int $article_id
 * @property int|null $year
 * @property int|null $month
 * @property float $base_value
 * @property float $extra_fees_value
 * @property int $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Modules\Payments\Models\Article $article
 * @property-read \App\Modules\Users\Models\User $createdBy
 * @property-read \App\Modules\Users\Models\User|null $deletedBy
 * @property-read \App\Modules\Users\Models\User|null $updatedBy
 * @property-read \App\Modules\Users\Models\User $user
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\ArticleRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\ArticleRequest newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Modules\Payments\Models\ArticleRequest onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\ArticleRequest query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\ArticleRequest whereArticleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\ArticleRequest whereBaseValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\ArticleRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\ArticleRequest whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\ArticleRequest whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\ArticleRequest whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\ArticleRequest whereExtraFeesValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\ArticleRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\ArticleRequest whereMonth($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\ArticleRequest whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\ArticleRequest whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\ArticleRequest whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\ArticleRequest whereYear($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Modules\Payments\Models\ArticleRequest withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Modules\Payments\Models\ArticleRequest withoutTrashed()
 * @mixin \Eloquent
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Modules\Payments\Models\Transaction[] $transactions
 * @property-read int|null $transactions_count
 */
class ArticleRequest extends Model
{

    use SoftDeletes;

    protected $table = 'article_requests';

    protected $fillable = [
        'user_id',
        'article_id',
        'year',
        'month',
        'base_value',
        'extra_fees_value',
        'status',
        'deleted_by',
        'meta',
        'discipline_id',
        'metric_id'
    ];

    //================================================================================
    // Relations
    //================================================================================

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function transactions()
    {
        return $this->belongsToMany(Transaction::class, 'transaction_article_requests', 'article_request_id', 'transaction_id')->withPivot(['value']);
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
