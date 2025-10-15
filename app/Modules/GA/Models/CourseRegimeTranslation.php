<?php

namespace App\Modules\GA\Models;

use App\Model;
use App\Modules\Cms\Models\Language;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Modules\GA\Models\CourseRegimeTranslation
 *
 * @property int $id
 * @property int $language_id
 * @property int $course_regimes_id
 * @property string|null $abbreviation
 * @property string|null $display_name
 * @property string|null $description
 * @property int $version
 * @property int|null $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read CourseRegime $course_regime
 * @property-read Language $language
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|CourseRegimeTranslation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CourseRegimeTranslation newQuery()
 * @method static Builder|CourseRegimeTranslation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CourseRegimeTranslation query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|CourseRegimeTranslation whereAbbreviation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseRegimeTranslation whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseRegimeTranslation whereCourseRegimesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseRegimeTranslation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseRegimeTranslation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseRegimeTranslation whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseRegimeTranslation whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseRegimeTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseRegimeTranslation whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseRegimeTranslation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseRegimeTranslation whereVersion($value)
 * @method static Builder|CourseRegimeTranslation withTrashed()
 * @method static Builder|CourseRegimeTranslation withoutTrashed()
 * @mixin Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|CourseRegimeTranslation disableCache()
 * @method static \Illuminate\Database\Eloquent\Builder|CourseRegimeTranslation withCacheCooldownSeconds($seconds = null)
 */
class CourseRegimeTranslation extends Model
{
    use SoftDeletes;
    //use Cachable;

    protected $table = 'course_regime_translations';

    protected $fillable = [
        'course_regimes_id',
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

    public function course_regime()
    {
        return $this->belongsTo(CourseRegime::class, 'course_regimes_id');
    }

    public function language()
    {
        return $this->hasOne(Language::class, 'id', 'language_id');
    }
}
