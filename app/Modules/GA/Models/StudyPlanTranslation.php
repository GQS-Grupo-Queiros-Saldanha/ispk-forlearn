<?php

namespace App\Modules\GA\Models;

use App\Model;
use App\Modules\Cms\Models\Language;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;


/**
 * App\Modules\GA\Models\StudyPlanTranslation
 *
 * @property int $id
 * @property int $language_id
 * @property int $study_plans_id
 * @property string|null $display_name
 * @property string|null $description
 * @property string|null $abbreviation
 * @property int $version
 * @property int|null $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read Language $language
 * @property-read StudyPlan $study_plan
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanTranslation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanTranslation newQuery()
 * @method static Builder|StudyPlanTranslation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanTranslation query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanTranslation whereAbbreviation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanTranslation whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanTranslation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanTranslation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanTranslation whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanTranslation whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanTranslation whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanTranslation whereStudyPlansId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanTranslation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanTranslation whereVersion($value)
 * @method static Builder|StudyPlanTranslation withTrashed()
 * @method static Builder|StudyPlanTranslation withoutTrashed()
 * @mixin Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanTranslation disableCache()
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanTranslation withCacheCooldownSeconds($seconds = null)
 */
class StudyPlanTranslation extends Model
{

    use SoftDeletes;
    //use Cachable;

    protected $table = 'study_plan_translations';

    protected $fillable = [
        'study_plans_id',
        'language_id',
        'display_name',
        'description',
        'abbreviation',
        'version',
        'active',
        'abbreviation',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    //================================================================================
    // Relations
    //================================================================================

    public function study_plan()
    {
        return $this->belongsTo(StudyPlan::class, 'study_plans_id');
    }

    public function language()
    {
        return $this->hasOne(Language::class, 'id', 'language_id');
    }

}
