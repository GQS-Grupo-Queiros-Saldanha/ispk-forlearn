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
 * App\Modules\GA\Models\ScheduleTypeTime
 *
 * @property int $id
 * @property int $schedule_type_id
 * @property string $code
 * @property string $start
 * @property string $end
 * @property int $order
 * @property int $created_by
 * @property int $updated_by
 * @property int $deleted_by
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read ScheduleTypeTimeTranslation $translation
 * @property-read Collection|ScheduleTypeTimeTranslation[] $translations
 * @property-read int|null $translations_count
 * @property-read ScheduleType $type
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTypeTime newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTypeTime newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTypeTime query()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTypeTime whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTypeTime whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTypeTime whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTypeTime whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTypeTime whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTypeTime whereEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTypeTime whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTypeTime whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTypeTime whereScheduleTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTypeTime whereStart($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTypeTime whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleTypeTime whereUpdatedBy($value)
 * @mixin Eloquent
 * @method static bool|null forceDelete()
 * @method static Builder|ScheduleTypeTime onlyTrashed()
 * @method static bool|null restore()
 * @method static Builder|ScheduleTypeTime withTrashed()
 * @method static Builder|ScheduleTypeTime withoutTrashed()
 */
class ScheduleTypeTime extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'schedule_type_id',
        'start',
        'end',
        'order',
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
        return $this->hasMany(ScheduleTypeTimeTranslation::class);
    }

    public function translation()
    {
        return $this->hasOne(ScheduleTypeTimeTranslation::class);
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

    public function type()
    {
        return $this->belongsTo(ScheduleType::class);
    }
}
