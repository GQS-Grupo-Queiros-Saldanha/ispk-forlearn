<?php

namespace App\Modules\GA\Models;

use App\Model;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Modules\GA\Models\DisciplineAbsenceConfiguration
 *
 * @property int $id
 * @property int $study_plan_editions_id
 * @property int|null $discipline_regimes_id
 * @property int $disciplines_id
 * @property int|null $max_absences
 * @property int $is_total
 * @property-read Discipline $discipline
 * @property-read DisciplineRegime|null $discipline_regime
 * @property-read StudyPlanEdition $study_plan_edition
 * @method static Builder|DisciplineAbsenceConfiguration newModelQuery()
 * @method static Builder|DisciplineAbsenceConfiguration newQuery()
 * @method static Builder|DisciplineAbsenceConfiguration query()
 * @method static Builder|DisciplineAbsenceConfiguration whereDisciplineRegimesId($value)
 * @method static Builder|DisciplineAbsenceConfiguration whereDisciplinesId($value)
 * @method static Builder|DisciplineAbsenceConfiguration whereId($value)
 * @method static Builder|DisciplineAbsenceConfiguration whereIsTotal($value)
 * @method static Builder|DisciplineAbsenceConfiguration whereMaxAbsences($value)
 * @method static Builder|DisciplineAbsenceConfiguration whereStudyPlanEditionsId($value)
 * @mixin Eloquent
 */
class DisciplineAbsenceConfiguration extends Model
{
    public $timestamps = false;

    protected $table = 'discipline_absence_configuration';

    protected $fillable = [
        'study_plan_editions_id',
        'discipline_regimes_id',
        'disciplines_id',
        'max_absences',
        'is_total',
    ];

    //================================================================================
    // Relations
    //================================================================================

    public function study_plan_edition()
    {
        return $this->belongsTo(StudyPlanEdition::class, 'study_plan_editions_id', 'id');
    }

    public function discipline()
    {
        return $this->belongsTo(Discipline::class, 'disciplines_id', 'id');
    }

    public function discipline_regime()
    {
        return $this->belongsTo(DisciplineRegime::class, 'discipline_regimes_id', 'id');
    }


}
