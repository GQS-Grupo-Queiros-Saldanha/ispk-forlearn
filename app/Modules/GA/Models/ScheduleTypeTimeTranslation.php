<?php

namespace App\Modules\GA\Models;

use App\Model;
use App\Modules\Cms\Models\Language;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Modules\GA\Models\ScheduleTypeTimeTranslation
 *
 * @property int $id
 * @property int $schedule_type_time_id
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
 * @property-read ScheduleTypeTime $scheduleTypeTime
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTypeTimeTranslation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTypeTimeTranslation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTypeTimeTranslation query()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTypeTimeTranslation whereAbbreviation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTypeTimeTranslation whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTypeTimeTranslation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTypeTimeTranslation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTypeTimeTranslation whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTypeTimeTranslation whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTypeTimeTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTypeTimeTranslation whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTypeTimeTranslation whereScheduleTypeTimeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTypeTimeTranslation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTypeTimeTranslation whereVersion($value)
 * @mixin Eloquent
 * @method static bool|null forceDelete()
 * @method static Builder|ScheduleTypeTimeTranslation onlyTrashed()
 * @method static bool|null restore()
 * @method static Builder|ScheduleTypeTimeTranslation withTrashed()
 * @method static Builder|ScheduleTypeTimeTranslation withoutTrashed()
 */
class ScheduleTypeTimeTranslation extends Model
{
    use SoftDeletes;

    //================================================================================
    // Relations
    //================================================================================

    public function scheduleTypeTime()
    {
        return $this->belongsTo(ScheduleTypeTime::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }

}
