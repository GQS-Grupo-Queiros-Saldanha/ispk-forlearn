<?php

namespace App\Modules\Payments\Models;

use App\Helpers\LanguageHelper;
use App\Model;
use App\Modules\Users\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Modules\GA\Models\Course
 *
 * @property int $id
 * @property string $code
 * @property int|null $active
 * @property int $years
 * @property int $duration_value
 * @property int $departments_id
 * @property int $course_cycles_id
 * @property int $course_regimes_id
 * @property int $degrees_id
 * @property int $duration_types_id
 * @property int $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read CourseCycle $course_cycle
 * @property-read CourseRegime $course_regime
 * @property-read User $createdBy
 * @property-read Degree $degree
 * @property-read User|null $deletedBy
 * @property-read Department $department
 * @property-read DurationType $duration_type
 * @property-read CourseTranslation $translation
 * @property-read Collection|CourseTranslation[] $translations
 * @property-read User|null $updatedBy
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|Course newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Course newQuery()
 * @method static Builder|Course onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Course query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereCourseCyclesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereCourseRegimesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereDegreesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereDepartmentsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereDurationTypesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereDurationValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Course whereYears($value)
 * @method static Builder|Course withTrashed()
 * @method static Builder|Course withoutTrashed()
 * @mixin Eloquent
 * @property-read Collection|CourseRegime[] $course_regimes
 * @property-read int|null $course_regimes_count
 * @property-read int|null $translations_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Modules\GA\Models\Discipline[] $disciplines
 * @property-read int|null $disciplines_count
 * @property-read \App\Modules\GA\Models\StudyPlan $studyPlans
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Modules\GA\Models\Classes[] $classes
 * @property-read int|null $classes_count
 */
class DisciplineArticle extends Model
{
    //use SoftDeletes;
    protected $table = 'disciplines_articles';
    protected $fillable = [
        'discipline_id',
        'article_request_id',
        'user_id'
    ];


    public function discipline()
    {
        return $this->hasOne('App\Modules\GA\Models\Discipline', 'id', 'discipline_id');
    }
}
