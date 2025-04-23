<?php

namespace App\Modules\GA\Models;

use App\Model;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Modules\GA\Models\StudyPlanHasDisciplineRegime
 *
 * @property int $sp_has_disciplines_id
 * @property int $discipline_regimes_id
 * @property float $hours
 * @property-read DisciplineRegime $discipline_regime
 * @property-read StudyPlanHasDiscipline $study_plan_has_discipline
 * @method static Builder|StudyPlanHasDisciplineRegime newModelQuery()
 * @method static Builder|StudyPlanHasDisciplineRegime newQuery()
 * @method static Builder|StudyPlanHasDisciplineRegime query()
 * @method static Builder|StudyPlanHasDisciplineRegime whereDisciplineRegimesId($value)
 * @method static Builder|StudyPlanHasDisciplineRegime whereHours($value)
 * @method static Builder|StudyPlanHasDisciplineRegime whereSpHasDisciplinesId($value)
 * @mixin Eloquent
 */
class StudyPlanHasDisciplineRegime extends Model
{
    protected $table = 'sp_has_discipline_regimes';

    protected $fillable = [
        'sp_has_disciplines_id',
        'discipline_regimes_id',
        'hours'
    ];

    //================================================================================
    // Relations
    //================================================================================

    public function discipline_regime()
    {
        return $this->belongsTo(DisciplineRegime::class, 'discipline_regimes_id');
    }

    public function study_plan_has_discipline()
    {
        return $this->belongsTo(StudyPlanHasDiscipline::class, 'sp_has_disciplines_id');
    }

}
