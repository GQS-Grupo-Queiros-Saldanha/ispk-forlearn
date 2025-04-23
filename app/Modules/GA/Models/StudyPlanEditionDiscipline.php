<?php

namespace App\Modules\GA\Models;

use App\Model;
use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;

/**
 * App\Modules\GA\Models\StudyPlanEditionDiscipline
 *
 * @property int $id
 * @property int $study_plan_editions_id
 * @property int $disciplines_id
 * @property int $period_types_id
 * @property int $year
 * @property int|null $optional
 * @property float|null $total_hours
 * @property-read Discipline $discipline
 * @property-read Collection|DisciplineRegime[] $disciplineRegimes
 * @property-read PeriodType $periodType
 * @property-read StudyPlanEdition $studyPlanEdition
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanEditionDiscipline newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanEditionDiscipline newQuery()
 * @method static Builder|StudyPlanEditionDiscipline onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanEditionDiscipline query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanEditionDiscipline whereDisciplinesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanEditionDiscipline whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanEditionDiscipline whereOptional($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanEditionDiscipline wherePeriodTypesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanEditionDiscipline whereStudyPlanEditionsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanEditionDiscipline whereTotalHours($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanEditionDiscipline whereYear($value)
 * @method static Builder|StudyPlanEditionDiscipline withTrashed()
 * @method static Builder|StudyPlanEditionDiscipline withoutTrashed()
 * @mixin Eloquent
 * @property-read Collection|StudyPlanEditionDisciplineDisciplineRegime[] $regimes
 * @property-read Collection|StudyPlanEditionDisciplineModule[] $modules
 * @property-read int|null $modules_count
 * @property-read int|null $regimes_count
 */
class StudyPlanEditionDiscipline extends Model
{
    protected $table = 'spe_has_disciplines';

    public $timestamps = false;

    protected $fillable = [
        'study_plan_editions_id',
        'disciplines_id',
        'period_types_id',
        'year',
        'optional',
        'total_hours',
    ];

    //================================================================================
    // Relations
    //================================================================================

    public function discipline()
    {
        return $this->belongsTo(Discipline::class, 'disciplines_id', 'id');
    }

    public function studyPlanEdition()
    {
        return $this->belongsTo(StudyPlanEdition::class, 'study_plan_editions_id', 'id');
    }

    public function periodType()
    {
        return $this->belongsTo(PeriodType::class, 'period_types_id', 'id');
    }

    public function regimes()
    {
        return $this->hasMany(StudyPlanEditionDisciplineDisciplineRegime::class, 'spe_has_disciplines_id', 'id');
    }

    public function modules()
    {
        return $this->hasMany(StudyPlanEditionDisciplineModule::class, 'spe_has_disciplines_id', 'id');
    }

}
