<?php

namespace App\Modules\Payments\Models;

use App\Model;
use App\Modules\Cms\Models\Language;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Modules\Payments\Models\ArticleTranslation
 *
 * @property-read \App\Modules\Payments\Models\Article $article
 * @property-read \App\Modules\Cms\Models\Language $language
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\ArticleTranslation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\ArticleTranslation newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Modules\Payments\Models\ArticleTranslation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\ArticleTranslation query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Query\Builder|\App\Modules\Payments\Models\ArticleTranslation withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Modules\Payments\Models\ArticleTranslation withoutTrashed()
 * @mixin \Eloquent
 * @property int $id
 * @property int $article_id
 * @property int $language_id
 * @property string $abbreviation
 * @property string $display_name
 * @property string|null $description
 * @property int $version
 * @property int $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\ArticleTranslation whereAbbreviation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\ArticleTranslation whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\ArticleTranslation whereArticleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\ArticleTranslation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\ArticleTranslation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\ArticleTranslation whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\ArticleTranslation whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\ArticleTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\ArticleTranslation whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\ArticleTranslation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\ArticleTranslation whereVersion($value)
 */
class ArticleTranslation extends Model
{
    use SoftDeletes;

    protected $table = 'article_translations';

    protected $fillable = [
        'article_id',
        'language_id',
        'display_name',
        'description',
        'version',
        'active',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    //================================================================================
    // Relations
    //================================================================================

    public function article()
    {
        return $this->belongsTo(Article::class, 'article_id');
    }

    public function language()
    {
        return $this->hasOne(Language::class, 'id', 'language_id');
    }
}
