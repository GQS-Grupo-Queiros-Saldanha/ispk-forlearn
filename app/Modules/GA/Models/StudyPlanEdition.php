<?php

namespace App\Modules\GA\Models;

use App\Helpers\LanguageHelper;
use App\Model;
use App\Modules\Users\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;
use Staudenmeir\EloquentHasManyDeep\HasRelationships;

/**
 * App\Modules\GA\Models\StudyPlanEdition
 *
 * @property int $id
 * @property int $study_plans_id
 * @property int $lective_years_id
 * @property int $year_transition_rules_id
 * @property int $average_calculation_rules_id
 * @property string $start_date
 * @property string $end_date
 * @property int|null $block_enrollments
 * @property int|null $max_enrollments
 * @property-read AverageCalculationRule $averageCalculationRule
 * @property-read User $createdBy
 * @property-read User $deletedBy
 * @property-read LectiveYear $lectiveYear
 * @property-read Collection|OptionalGroup[] $optionalGroups
 * @property-read PeriodTypeTranslation $translation
 * @property-read Collection|StudyPlanEditionTranslation[] $translations
 * @property-read User $updatedBy
 * @property-read YearTransitionRule $yearTransitionRule
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanEdition newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanEdition newQuery()
 * @method static Builder|StudyPlanEdition onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanEdition query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanEdition whereAverageCalculationRulesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanEdition whereBlockEnrollments($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanEdition whereEndDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanEdition whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanEdition whereLectiveYearsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanEdition whereMaxEnrollments($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanEdition whereStartDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanEdition whereStudyPlansId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanEdition whereYearTransitionRulesId($value)
 * @method static Builder|StudyPlanEdition withTrashed()
 * @method static Builder|StudyPlanEdition withoutTrashed()
 * @mixin Eloquent
 * @property-read Collection|StudyPlanEditionDiscipline[] $disciplines
 * @property-read Collection|Precedence[] $precedences
 * @property-read StudyPlan $studyPlan
 * @property-read Collection|StudyPlanEditionAccessType[] $accessTypes
 * @property-read Collection|DisciplineCurricula[] $discipline_curricula
 * @property-read Collection|DisciplineAbsenceConfiguration[] $absences
 * @property-read int|null $absences_count
 * @property-read int|null $access_types_count
 * @property-read int|null $discipline_curricula_count
 * @property-read int|null $disciplines_count
 * @property-read int|null $optional_groups_count
 * @property-read int|null $precedences_count
 * @property-read int|null $translations_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Modules\GA\Models\DisciplineClass[] $classes
 * @property-read int|null $classes_count
 * @property int $course_year
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\GA\Models\StudyPlanEdition whereCourseYear($value)
 */
class StudyPlanEdition extends Model
{
    use HasRelationships;

    public $timestamps = false;

    protected $fillable = [
        'study_plans_id',
        'lective_years_id',
        'year_transition_rules_id',
        'average_calculation_rules_id',
        'block_enrollments',
        'start_date',
        'end_date',
        'block_enrollments',
        'max_enrollments',
        'course_year',
        'period_type_id'
    ];

    //================================================================================
    // Translations
    //================================================================================

    public function translations()
    {
        return $this->hasMany(StudyPlanEditionTranslation::class, 'study_plan_editions_id', 'id');
    }

    public function translation()
    {
        return $this->hasOne(StudyPlanEditionTranslation::class, 'study_plan_editions_id', 'id');
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

    public function studyPlan()
    {
        return $this->belongsTo(StudyPlan::class, 'study_plans_id', 'id');
    }

    public function lectiveYear()
    {
        return $this->belongsTo(LectiveYear::class, 'lective_years_id', 'id');
    }

    public function yearTransitionRule()
    {
        return $this->belongsTo(YearTransitionRule::class, 'year_transition_rules_id', 'id');
    }

    public function averageCalculationRule()
    {
        return $this->belongsTo(AverageCalculationRule::class, 'average_calculation_rules_id', 'id');
    }

    public function disciplines()
    {
        return $this->belongsToMany(Discipline::class, 'study_plan_edition_disciplines', 'study_plan_edition_id', 'discipline_id');
    }

    public function accessTypes()
    {
        return $this->hasMany(StudyPlanEditionAccessType::class, 'spe_id', 'id');
    }

    public function precedences()
    {
        return $this->hasMany(Precedence::class, 'study_plan_editions_id', 'id');
    }

    public function discipline_curricula()
    {
        return $this->hasMany(DisciplineCurricula::class, 'study_plan_editions_id', 'id');
    }

    public function absences()
    {
        return $this->hasMany(DisciplineAbsenceConfiguration::class, 'study_plan_editions_id', 'id');
    }

    public function classes()
    {
        return $this->hasMany(DisciplineClass::class, 'study_plan_editions_id');
    }

    public function periodTypes()
    {
        return $this->BelongsTo(PeriodType::class, 'period_type_id', 'id');
    }
}

