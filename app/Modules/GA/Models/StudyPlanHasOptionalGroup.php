<?php

namespace App\Modules\GA\Models;

use App\Model;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Modules\GA\Models\StudyPlanHasOptionalGroup
 *
 * @property int $id
 * @property int $study_plans_id
 * @property int $optional_groups_id
 * @property int $discipline_periods_id
 * @property int $year
 * @property-read DisciplinePeriod $discipline_period
 * @property-read OptionalGroup $optional_group
 * @property-read StudyPlan $studyPlan
 * @method static Builder|StudyPlanHasOptionalGroup newModelQuery()
 * @method static Builder|StudyPlanHasOptionalGroup newQuery()
 * @method static Builder|StudyPlanHasOptionalGroup query()
 * @method static Builder|StudyPlanHasOptionalGroup whereDisciplinePeriodsId($value)
 * @method static Builder|StudyPlanHasOptionalGroup whereId($value)
 * @method static Builder|StudyPlanHasOptionalGroup whereOptionalGroupsId($value)
 * @method static Builder|StudyPlanHasOptionalGroup whereStudyPlansId($value)
 * @method static Builder|StudyPlanHasOptionalGroup whereYear($value)
 * @mixin Eloquent
 */
class StudyPlanHasOptionalGroup extends Model
{
    protected $table = 'study_plans_has_optional_groups';

    protected $fillable = [
        'study_plans_id',
        'optional_groups_id',
        'discipline_periods_id',
        'year'
    ];

    //================================================================================
    // Relations
    //================================================================================

    public function discipline_period()
    {
        return $this->belongsTo(DisciplinePeriod::class, 'discipline_periods_id');
    }

    public function optional_group()
    {
        return $this->belongsTo(OptionalGroup::class, 'optional_groups_id');
    }

    public function studyPlan()
    {
        return $this->belongsTo(StudyPlan::class, 'study_plans_id');
    }
}
