<?php

namespace App\Modules\GA\Models;

use App\Model;
use App\Modules\Cms\Models\Language;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Modules\GA\Models\ScheduleTranslation
 *
 * @property int $id
 * @property int $schedule_id
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
 * @property-read Schedule $schedule
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTranslation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTranslation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTranslation query()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTranslation whereAbbreviation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTranslation whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTranslation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTranslation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTranslation whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTranslation whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTranslation whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTranslation whereScheduleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTranslation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTranslation whereVersion($value)
 * @mixin Eloquent
 * @method static bool|null forceDelete()
 * @method static Builder|ScheduleTranslation onlyTrashed()
 * @method static bool|null restore()
 * @method static Builder|ScheduleTranslation withTrashed()
 * @method static Builder|ScheduleTranslation withoutTrashed()
 */
class ScheduleTranslation extends Model
{
    use SoftDeletes;

    //================================================================================
    // Relations
    //================================================================================

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
