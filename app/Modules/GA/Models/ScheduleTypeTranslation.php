<?php

namespace App\Modules\GA\Models;

use App\Model;
use App\Modules\Cms\Models\Language;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Modules\GA\Models\ScheduleTypeTranslation
 *
 * @property int $id
 * @property int $schedule_type_id
 * @property int $language_id
 * @property string $display_name
 * @property string $description
 * @property string $abbreviation
 * @property int $version
 * @property int $active
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 * @property int|null $deleted_at
 * @property-read Language $language
 * @property-read ScheduleType $scheduleType
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTypeTranslation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTypeTranslation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTypeTranslation query()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTypeTranslation whereAbbreviation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTypeTranslation whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTypeTranslation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTypeTranslation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTypeTranslation whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTypeTranslation whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTypeTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTypeTranslation whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTypeTranslation whereScheduleTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTypeTranslation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTypeTranslation whereVersion($value)
 * @mixin Eloquent
 * @method static bool|null forceDelete()
 * @method static Builder|ScheduleTypeTranslation onlyTrashed()
 * @method static bool|null restore()
 * @method static Builder|ScheduleTypeTranslation withTrashed()
 * @method static Builder|ScheduleTypeTranslation withoutTrashed()
 */
class ScheduleTypeTranslation extends Model
{
    use SoftDeletes;

    //================================================================================
    // Relations
    //================================================================================

    public function scheduleType()
    {
        return $this->belongsTo(ScheduleType::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
