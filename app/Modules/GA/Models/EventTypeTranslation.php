<?php

namespace App\Modules\GA\Models;

use App\Model;
use App\Modules\Cms\Models\Language;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Modules\GA\Models\EventTypeTranslation
 *
 * @property int $id
 * @property int $event_type_id
 * @property int $language_id
 * @property string $display_name
 * @property string $description
 * @property int $active
 * @property int $version
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read EventType $eventType
 * @property-read Language $language
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|EventTypeTranslation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventTypeTranslation newQuery()
 * @method static Builder|EventTypeTranslation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventTypeTranslation query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|EventTypeTranslation whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTypeTranslation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTypeTranslation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTypeTranslation whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTypeTranslation whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTypeTranslation whereEventTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTypeTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTypeTranslation whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTypeTranslation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventTypeTranslation whereVersion($value)
 * @method static Builder|EventTypeTranslation withTrashed()
 * @method static Builder|EventTypeTranslation withoutTrashed()
 * @mixin Eloquent
 */
class EventTypeTranslation extends Model
{
    use SoftDeletes;
    //use Cachable;

    protected $table = 'event_type_translations';

    protected $fillable = [
        'event_type_id',
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

    public function eventType()
    {
        return $this->belongsTo(EventType::class);
    }

    public function language()
    {
        return $this->hasOne(Language::class, 'id', 'language_id');
    }
}
