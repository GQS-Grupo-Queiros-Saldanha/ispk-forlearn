<?php

namespace App\Modules\GA\Models;

use App\Helpers\LanguageHelper;
use App\Model;
use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Modules\GA\Models\ScheduleType
 *
 * @property int $id
 * @property int $spe_id
 * @property int $schedule_type_type_id
 * @property int $discipline_class_id
 * @property string $code
 * @property int $created_by
 * @property int $updated_by
 * @property int $deleted_by
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read Collection|ScheduleTypeTime[] $times
 * @property-read int|null $times_count
 * @property-read ScheduleTypeTranslation $translation
 * @property-read Collection|ScheduleTypeTranslation[] $translations
 * @property-read int|null $translations_count
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleType query()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleType whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleType whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleType whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleType whereDisciplineClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleType whereScheduleTypeTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleType whereSpeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleType whereUpdatedBy($value)
 * @mixin Eloquent
 * @method static bool|null forceDelete()
 * @method static Builder|ScheduleType onlyTrashed()
 * @method static bool|null restore()
 * @method static Builder|ScheduleType withTrashed()
 * @method static Builder|ScheduleType withoutTrashed()
 */
class ScheduleType extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'schedule_id',
        'created_by',
        'updated_by',
        'deleted_by',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    //================================================================================
    // Translations
    //================================================================================

    public function translations()
    {
        return $this->hasMany(ScheduleTypeTranslation::class);
    }

    public function translation()
    {
        return $this->hasOne(ScheduleTypeTranslation::class);
    }

    public function currentTranslation()
    {
        return $this
            ->translation()
            ->whereActive(true)
            ->whereLanguageId(LanguageHelper::getCurrentLanguage());
    }

    //================================================================================
    // Relations
    //================================================================================

    public function times()
    {
        return $this->hasMany(ScheduleTypeTime::class);
    }
}
