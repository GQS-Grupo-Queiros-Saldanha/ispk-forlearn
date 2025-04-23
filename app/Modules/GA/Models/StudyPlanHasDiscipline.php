<?php

namespace App\Modules\GA\Models;

use App\Model;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * App\Modules\GA\Models\StudyPlanHasDiscipline
 *
 * @property int $id
 * @property int $study_plans_id
 * @property int $disciplines_id
 * @property int $discipline_periods_id
 * @property float $total_hours
 * @property int $years
 * @property-read Discipline $discipline
 * @property-read DisciplinePeriod $discipline_period
 * @property-read StudyPlan $studyPlan
 * @property-read Collection|StudyPlanHasDisciplineRegime[] $study_plans_has_discipline_regimes
 * @method static Builder|StudyPlanHasDiscipline newModelQuery()
 * @method static Builder|StudyPlanHasDiscipline newQuery()
 * @method static Builder|StudyPlanHasDiscipline query()
 * @method static Builder|StudyPlanHasDiscipline whereDisciplinePeriodsId($value)
 * @method static Builder|StudyPlanHasDiscipline whereDisciplinesId($value)
 * @method static Builder|StudyPlanHasDiscipline whereId($value)
 * @method static Builder|StudyPlanHasDiscipline whereStudyPlansId($value)
 * @method static Builder|StudyPlanHasDiscipline whereTotalHours($value)
 * @method static Builder|StudyPlanHasDiscipline whereYears($value)
 * @mixin Eloquent
 * @property-read int|null $study_plans_has_discipline_regimes_count
 */
class StudyPlanHasDiscipline extends Model
{
    protected $table = 'study_plans_has_disciplines';

    public $timestamps = false;

    protected $fillable = [
        'study_plans_id',
        'disciplines_id',
        'discipline_periods_id',
        'total_hours',
        'years'
    ];

    //================================================================================
    // Relations
    //================================================================================

    public function discipline()
    {
        return $this->belongsTo(Discipline::class, 'disciplines_id');
    }

    public function discipline_period()
    {
        return $this->belongsTo(DisciplinePeriod::class, 'discipline_periods_id');
    }

    public function studyPlan()
    {
        return $this->belongsTo(StudyPlan::class, 'study_plans_id');
    }

    public function study_plans_has_discipline_regimes()
    {
        return $this->hasMany(StudyPlanHasDisciplineRegime::class, 'sp_has_disciplines_id', 'id');
    }

}
