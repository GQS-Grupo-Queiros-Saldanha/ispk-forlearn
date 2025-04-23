<?php

namespace App\Modules\GA\Models;

use App\Model;
use App\Modules\Cms\Models\Language;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Modules\GA\Models\CourseCycleTranslation
 *
 * @property int $id
 * @property int $language_id
 * @property int $course_cycles_id
 * @property string|null $abbreviation
 * @property string|null $display_name
 * @property string|null $description
 * @property int $version
 * @property int|null $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read CourseCycle $course_cycle
 * @property-read Language $language
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|CourseCycleTranslation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|CourseCycleTranslation newQuery()
 * @method static Builder|CourseCycleTranslation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|CourseCycleTranslation query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|CourseCycleTranslation whereAbbreviation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseCycleTranslation whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseCycleTranslation whereCourseCyclesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseCycleTranslation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseCycleTranslation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseCycleTranslation whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseCycleTranslation whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseCycleTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseCycleTranslation whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseCycleTranslation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|CourseCycleTranslation whereVersion($value)
 * @method static Builder|CourseCycleTranslation withTrashed()
 * @method static Builder|CourseCycleTranslation withoutTrashed()
 * @mixin Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|CourseCycleTranslation disableCache()
 * @method static \Illuminate\Database\Eloquent\Builder|CourseCycleTranslation withCacheCooldownSeconds($seconds = null)
 */
class CourseCycleTranslation extends Model
{
    use SoftDeletes;
    //use Cachable;

    protected $table = 'course_cycle_translations';

    protected $fillable = [
        'course_cycles_id',
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

    public function course_cycle()
    {
        return $this->belongsTo(CourseCycle::class, 'course_cycles_id');
    }

    public function language()
    {
        return $this->hasOne(Language::class, 'id', 'language_id');
    }
}
