<?php

namespace App\Modules\GA\Models;

use App\Model;
use App\Modules\Cms\Models\Language;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Modules\GA\Models\CourseTranslation
 *
 * @property int $id
 * @property int $language_id
 * @property int $courses_id
 * @property string|null $abbreviation
 * @property string|null $display_name
 * @property string|null $description
 * @property int $version
 * @property int|null $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read Course $course
 * @property-read Language $language
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|CourseTranslation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CourseTranslation newQuery()
 * @method static Builder|CourseTranslation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CourseTranslation query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|CourseTranslation whereAbbreviation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseTranslation whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseTranslation whereCoursesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseTranslation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseTranslation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseTranslation whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseTranslation whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseTranslation whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseTranslation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseTranslation whereVersion($value)
 * @method static Builder|CourseTranslation withTrashed()
 * @method static Builder|CourseTranslation withoutTrashed()
 * @mixin Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|CourseTranslation disableCache()
 * @method static \Illuminate\Database\Eloquent\Builder|CourseTranslation withCacheCooldownSeconds($seconds = null)
 */
class CourseTranslation extends Model
{
    use SoftDeletes;
    //use Cachable;

    protected $table = 'courses_translations';

    protected $fillable = [
        'courses_id',
        'language_id',
        'display_name',
        'description',
        'abbreviation',
        'version',
        'active',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    //================================================================================
    // Relations
    //================================================================================

    public function course()
    {
        return $this->belongsTo(Course::class, 'courses_id');
    }

    public function language()
    {
        return $this->hasOne(Language::class, 'id', 'language_id');
    }
}
