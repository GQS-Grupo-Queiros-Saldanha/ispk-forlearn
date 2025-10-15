<?php

namespace App\Modules\GA\Models;

use App\Model;
use App\Modules\Cms\Models\Language;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
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
 * @method static \Illuminate\Database\Eloquent\Builder|EventTranslation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventTranslation newQuery()
 * @method static Builder|EventTranslation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventTranslation query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|EventTranslation whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTranslation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTranslation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTranslation whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTranslation whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTranslation whereEventId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTranslation whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTranslation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTranslation whereVersion($value)
 * @method static Builder|EventTranslation withTrashed()
 * @method static Builder|EventTranslation withoutTrashed()
 * @mixin Eloquent
 */
class EventTranslation extends Model
{
    use SoftDeletes;
    //use Cachable;

    protected $fillable = [
        'event_id',
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

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function language()
    {
        return $this->hasOne(Language::class);
    }
}
