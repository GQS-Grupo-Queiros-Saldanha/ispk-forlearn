<?php

namespace App\Modules\GA\Models;

use App\Helpers\LanguageHelper;
use App\Model;
use App\Modules\Users\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Modules\GA\Models\CourseRegime
 *
 * @property int $id
 * @property string $code
 * @property int $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read Collection|Course[] $courses
 * @property-read User $createdBy
 * @property-read User|null $deletedBy
 * @property-read CourseRegimeTranslation $translation
 * @property-read Collection|CourseRegimeTranslation[] $translations
 * @property-read User|null $updatedBy
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|CourseRegime newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CourseRegime newQuery()
 * @method static Builder|CourseRegime onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CourseRegime query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|CourseRegime whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseRegime whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseRegime whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseRegime whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseRegime whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseRegime whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseRegime whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseRegime whereUpdatedBy($value)
 * @method static Builder|CourseRegime withTrashed()
 * @method static Builder|CourseRegime withoutTrashed()
 * @mixin Eloquent
 * @property-read int|null $courses_count
 * @property-read int|null $translations_count
 */
class CourseRegime extends Model
{
    use SoftDeletes;

    protected $fillable = [
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
        return $this->hasMany(CourseRegimeTranslation::class, 'course_regimes_id', 'id');
    }

    public function translation()
    {
        return $this->hasOne(CourseRegimeTranslation::class, 'course_regimes_id', 'id');
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

    public function courses()
    {
        return $this->hasMany(Course::class, 'course_cycles_id', 'id');
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
