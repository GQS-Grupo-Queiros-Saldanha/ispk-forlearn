<?php

namespace App\Modules\GA\Models;

use App\Model;
use App\Modules\Cms\Models\Language;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Modules\GA\Models\EventTranslation
 *
 * @property int $id
 * @property int $event_id
 * @property int $language_id
 * @property string $display_name
 * @property string $description
 * @property int $active
 * @property int $version
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Event $event
 * @property-read Language $language
 * @method static bool|null forceDelete()
 * @method static Builder|EventTranslation newModelQuery()
 * @method static Builder|EventTranslation newQuery()
 * @method static \Illuminate\Database\Query\Builder|EventTranslation onlyTrashed()
 * @method static Builder|EventTranslation query()
 * @method static bool|null restore()
 * @method static Builder|EventTranslation whereActive($value)
 * @method static Builder|EventTranslation whereCreatedAt($value)
 * @method static Builder|EventTranslation whereDeletedAt($value)
 * @method static Builder|EventTranslation whereDescription($value)
 * @method static Builder|EventTranslation whereDisplayName($value)
 * @method static Builder|EventTranslation whereEventId($value)
 * @method static Builder|EventTranslation whereId($value)
 * @method static Builder|EventTranslation whereLanguageId($value)
 * @method static Builder|EventTranslation whereUpdatedAt($value)
 * @method static Builder|EventTranslation whereVersion($value)
 * @method static \Illuminate\Database\Query\Builder|EventTranslation withTrashed()
 * @method static \Illuminate\Database\Query\Builder|EventTranslation withoutTrashed()
 * @mixin Eloquent
 * @property int $summary_id
 * @property-read Summary $summary
 * @method static Builder|SummaryTranslation whereSummaryId($value)
 * @property int $summaries_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\GA\Models\SummaryTranslation whereSummariesId($value)
 */
class SummaryTranslation extends Model
{
    use SoftDeletes;
    //use Cachable;

    protected $fillable = [
        'summaries_id',
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

    public function summary()
    {
        return $this->belongsTo(Summary::class);
    }

    public function language()
    {
        return $this->hasOne(Language::class);
    }
}
