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
 * App\Modules\GA\Models\StudyPlan
 *
 * @property int $id
 * @property int $courses_id
 * @property string $code
 * @property int $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read Course $course
 * @property-read Collection|StudyPlanHasDiscipline[] $study_plans_has_disciplines
 * @property-read Collection|StudyPlanHasOptionalGroup[] $study_plans_has_optional_groups
 * @property-read StudyPlanTranslation $translation
 * @property-read Collection|StudyPlanTranslation[] $translations
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlan newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlan newQuery()
 * @method static Builder|StudyPlan onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlan query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlan whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlan whereCoursesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlan whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlan whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlan whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlan whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlan whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlan whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlan whereUpdatedBy($value)
 * @method static Builder|StudyPlan withTrashed()
 * @method static Builder|StudyPlan withoutTrashed()
 * @mixin Eloquent
 * @property-read int|null $study_plans_has_disciplines_count
 * @property-read int|null $study_plans_has_optional_groups_count
 * @property-read int|null $translations_count
 */
class StudyPlan extends Model
{
    use SoftDeletes;

    protected $table = 'study_plans';

    protected $fillable = [
        'courses_id',
        'code',
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
        return $this->hasMany(StudyPlanTranslation::class, 'study_plans_id', 'id');
    }

    public function translation()
    {
        return $this->hasOne(StudyPlanTranslation::class, 'study_plans_id', 'id');
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

    public function course()
    {
        return $this->belongsTo(Course::class, 'courses_id');
    }

    public function study_plans_has_optional_groups()
    {
        return $this->hasMany(StudyPlanHasOptionalGroup::class, 'study_plans_id', 'id');
    }

    public function study_plans_has_disciplines()
    {
        return $this->hasMany(StudyPlanHasDiscipline::class, 'study_plans_id', 'id');
    }
}
