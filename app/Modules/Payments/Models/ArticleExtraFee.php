<?php

namespace App\Modules\Payments\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Modules\Payments\Models\ArticleExtraFee
 *
 * @property-read \App\Modules\Payments\Models\Article $article
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\ArticleExtraFee newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\ArticleExtraFee newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\ArticleExtraFee query()
 * @mixin \Eloquent
 * @property int $id
 * @property int $article_id
 * @property int $fee_percent
 * @property int $max_delay_days
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\ArticleExtraFee whereArticleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\ArticleExtraFee whereFeePercent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\ArticleExtraFee whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\ArticleExtraFee whereMaxDelayDays($value)
 */
class ArticleExtraFee extends Model
{
    public $timestamps = false;

    protected $fillable = ['fee_percent', 'max_delay_days'];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}
