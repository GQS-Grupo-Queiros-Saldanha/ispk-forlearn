<?php

namespace App\Modules\GA\Models;

use App\Model;
use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;

/**
 * App\Modules\GA\Models\StudyPlanEditionDisciplineDisciplineRegime
 *
 * @property int $spe_has_disciplines_id
 * @property int $discipline_regimes_id
 * @property float|null $hours
 * @property-read DisciplineRegime $disciplineRegimes
 * @property-read Collection|StudyPlanEdition[] $studyPlanEditionDiscipline
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanEditionDisciplineDisciplineRegime newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanEditionDisciplineDisciplineRegime newQuery()
 * @method static Builder|StudyPlanEditionDisciplineDisciplineRegime onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanEditionDisciplineDisciplineRegime query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanEditionDisciplineDisciplineRegime whereDisciplineRegimesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanEditionDisciplineDisciplineRegime whereHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanEditionDisciplineDisciplineRegime whereSpeHasDisciplinesId($value)
 * @method static Builder|StudyPlanEditionDisciplineDisciplineRegime withTrashed()
 * @method static Builder|StudyPlanEditionDisciplineDisciplineRegime withoutTrashed()
 * @mixin Eloquent
 * @property-read DisciplineRegime $regime
 * @property-read int|null $study_plan_edition_discipline_count
 */
class StudyPlanEditionDisciplineDisciplineRegime extends Model
{
    protected $table = 'spe_has_disciplines_has_dr';

    protected $fillable = [
        'spe_has_disciplines_id',
        'discipline_regimes',
        'hours'
    ];

    //================================================================================
    // Relations
    //================================================================================

    public function studyPlanEditionDiscipline()
    {
        return $this
            ->belongsToMany(StudyPlanEdition::class)
            ->using(StudyPlanEditionDiscipline::class)
            ->withPivot([
                'period_types_id',
                'year',
                'optional',
                'total_hours',
            ]);
    }

    public function regime()
    {
        return $this->belongsTo(DisciplineRegime::class, 'discipline_regimes_id', 'id');
    }
}
