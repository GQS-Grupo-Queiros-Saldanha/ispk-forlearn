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
 * App\Modules\GA\Models\Schedule
 *
 * @property int $id
 * @property int $spe_id
 * @property int $schedule_type_id
 * @property int $discipline_class_id
 * @property string $code
 * @property int $created_by
 * @property int $updated_by
 * @property int $deleted_by
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read DisciplineClass $disciplineClass
 * @property-read StudyPlanEdition $studyPlanEdition
 * @property-read ScheduleTranslation $translation
 * @property-read Collection|ScheduleTranslation[] $translations
 * @property-read int|null $translations_count
 * @property-read ScheduleType $type
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule query()
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereDisciplineClassId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereScheduleTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereSpeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Schedule whereUpdatedBy($value)
 * @mixin Eloquent
 * @method static bool|null forceDelete()
 * @method static Builder|Schedule onlyTrashed()
 * @method static bool|null restore()
 * @method static Builder|Schedule withTrashed()
 * @method static Builder|Schedule withoutTrashed()
 * @property string $start
 * @property string $end
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Modules\GA\Models\ScheduleEvent[] $events
 * @property-read int|null $events_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\GA\Models\Schedule whereEnd($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\GA\Models\Schedule whereStart($value)
 */
class Schedule extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'start_at',
        'end_at',
        'spe_id',
        'schedule_type_id',
        'discipline_class_id',
        'period_type_id',
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
        return $this->hasMany(ScheduleTranslation::class);
    }

    public function translation()
    {
        return $this->hasOne(ScheduleTranslation::class);
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

    public function studyPlanEdition()
    {
        return $this->belongsTo(StudyPlanEdition::class, 'spe_id');
    }

    public function disciplineClass()
    {
        return $this->belongsTo(DisciplineClass::class);
    }

    public function type()
    {
        return $this->belongsTo(ScheduleType::class, 'schedule_type_id');
    }

    public function events()
    {
        return $this->hasMany(ScheduleEvent::class);
    }

    //================================================================================
    // Aliases
    //================================================================================
    public function study_plan_edition()
    {
        return $this->studyPlanEdition();
    }

    public function class()
    {
        return $this->belongsTo(Classes::class, 'discipline_class_id', 'id');
    }

    public function schedule_type()
    {
        return $this->belongsTo(ScheduleType::class, 'schedule_type_id', 'id');
    }

    public function period_type()
    {
        return $this->belongsTo(PeriodType::class, 'period_type_id', 'id');
    }

    //================================================================================
    // Creators
    //================================================================================

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }


}
