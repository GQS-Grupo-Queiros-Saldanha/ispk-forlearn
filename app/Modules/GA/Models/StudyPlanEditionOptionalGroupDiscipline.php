<?php

namespace App\Modules\GA\Models;

use App\Model;
use Eloquent;
use Illuminate\Database\Query\Builder;

/**
 * App\Modules\GA\Models\StudyPlanEditionOptionalGroupDiscipline
 *
 * @property int $spe_has_og_id
 * @property int $disciplines_id
 * @property-read Discipline $disciplines
 * @property-read StudyPlanEditionOptionalGroup $studyPlanEditionOptionalGroup
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanEditionOptionalGroupDiscipline newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanEditionOptionalGroupDiscipline newQuery()
 * @method static Builder|StudyPlanEditionOptionalGroupDiscipline onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanEditionOptionalGroupDiscipline query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanEditionOptionalGroupDiscipline whereDisciplinesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanEditionOptionalGroupDiscipline whereSpeHasOgId($value)
 * @method static Builder|StudyPlanEditionOptionalGroupDiscipline withTrashed()
 * @method static Builder|StudyPlanEditionOptionalGroupDiscipline withoutTrashed()
 * @mixin Eloquent
 * @property-read Discipline $discipline
 * @property-read \App\Modules\GA\Models\OptionalGroup $optionalGroup
 */
class StudyPlanEditionOptionalGroupDiscipline extends Model
{
    protected $table = 'spe_has_optional_groups_has_disciplines';

    protected $fillable = [
        'spe_has_og_id',
        'disciplines_id'
    ];

    //================================================================================
    // Relations
    //================================================================================

    public function discipline()
    {
        return $this->belongsTo(Discipline::class, 'disciplines_id', 'id');
    }

    public function studyPlanEditionOptionalGroup()
    {
        return $this->belongsTo(StudyPlanEditionOptionalGroup::class, 'spe_has_og_id', 'id');
    }

    public function optionalGroup()
    {
        return $this->hasOneThrough(OptionalGroup::class, StudyPlanEditionOptionalGroup::class, 'optional_groups_id', 'id');
    }
}
