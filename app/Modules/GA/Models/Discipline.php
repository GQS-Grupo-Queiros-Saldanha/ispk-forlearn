<?php

namespace App\Modules\GA\Models;

use App\Helpers\LanguageHelper;
use App\Model;
use App\Modules\Grades\Models\Grade;
use App\Modules\Users\Models\Matriculation;
use App\Modules\Users\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Modules\GA\Models\Discipline
 *
 * @property int $id
 * @property int $discipline_profiles_id
 * @property int $discipline_areas_id
 * @property string|null $code
 * @property int $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read User $createdBy
 * @property-read User|null $deletedBy
 * @property-read DisciplineArea $disciplineArea
 * @property-read DisciplineProfile $disciplineProfile
 * @property-read User|null $updatedBy
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|Discipline newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Discipline newQuery()
 * @method static Builder|Discipline onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Discipline query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|Discipline whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Discipline whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Discipline whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Discipline whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Discipline whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Discipline whereDisciplineAreasId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Discipline whereDisciplineProfilesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Discipline whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Discipline whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Discipline whereUpdatedBy($value)
 * @method static Builder|Discipline withTrashed()
 * @method static Builder|Discipline withoutTrashed()
 * @mixin Eloquent
 * @property-read DisciplineTranslation $translation
 * @property-read Collection|DisciplineTranslation[] $translations
 * @property-read Collection|StudyPlanHasDiscipline[] $study_plans_has_disciplines
 * @property-read Collection|DisciplineArea[] $disciplineAreas
 * @property-read int|null $discipline_areas_count
 * @property-read int|null $study_plans_has_disciplines_count
 * @property-read int|null $translations_count
 * @property int|null $courses_id
 * @property-read \App\Modules\GA\Models\Course|null $course
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Modules\Grades\Models\Grade[] $grades
 * @property-read int|null $grades_count
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\GA\Models\Discipline whereCoursesId($value)
 */
class Discipline extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'courses_id',
        'discipline_profiles_id',
        'discipline_areas_id',
        'code',
        'uc',
        'maximum_absence',
        'mandatory_discipline',
        'percentage',
        'created_by',
        'updated_by',
        'deleted_by',
        'created_at',
        'updated_at',
        'deleted_at',
        'tfc'
    ];

    //================================================================================
    // Translations
    //================================================================================

    public function translations()
    {
        return $this->hasMany(DisciplineTranslation::class, 'discipline_id', 'id');
    }

    public function translation()
    {
        return $this->hasOne(DisciplineTranslation::class, 'discipline_id', 'id');
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

    public function disciplineProfile()
    {
        return $this->belongsTo(DisciplineProfile::class, 'discipline_profiles_id');
    }

    public function disciplineAreas()
    {
        return $this->belongsToMany(DisciplineArea::class, 'discipline_has_areas');
    }

    public function study_plans_has_disciplines()
    {
        return $this->hasMany(StudyPlanHasDiscipline::class, 'disciplines_id', 'id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class, 'courses_id', 'id', 'courses');
    }

    public function grades()
    {
        return $this->hasMany(Grade::class, 'discipline_id', 'id');
    }

    public function matriculations()
    {
        return $this
            ->belongsToMany(Matriculation::class, 'matriculation_disciplines', 'discipline_id', 'matriculation_id')
            ->withPivot('exam_only');
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
