<?php

namespace App\Modules\GA\Models;

use App\Model;
use App\Modules\Cms\Models\Language;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Modules\GA\Models\StudyPlanEditionTranslation
 *
 * @property int $id
 * @property int $study_plan_editions_id
 * @property int $language_id
 * @property string|null $display_name
 * @property string|null $description
 * @property int $version
 * @property int|null $active
 * @property string|null $abbreviation
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Language $language
 * @property-read StudyPlanEdition $studyPlanEdition
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanEditionTranslation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanEditionTranslation newQuery()
 * @method static Builder|StudyPlanEditionTranslation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanEditionTranslation query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanEditionTranslation whereAbbreviation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanEditionTranslation whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanEditionTranslation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanEditionTranslation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanEditionTranslation whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanEditionTranslation whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanEditionTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanEditionTranslation whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanEditionTranslation whereStudyPlanEditionsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanEditionTranslation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanEditionTranslation whereVersion($value)
 * @method static Builder|StudyPlanEditionTranslation withTrashed()
 * @method static Builder|StudyPlanEditionTranslation withoutTrashed()
 * @mixin Eloquent
 */
class StudyPlanEditionTranslation extends Model
{
    use SoftDeletes;
    //use Cachable;

    protected $table = 'study_plan_edition_translations';

    protected $fillable = [
        'study_plan_editions_id',
        'language_id',
        'display_name',
        'description',
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

    public function studyPlanEdition()
    {
        return $this->belongsTo(StudyPlanEdition::class, 'study_plan_editions_id');
    }

    public function language()
    {
        return $this->hasOne(Language::class, 'id', 'language_id');
    }
}
