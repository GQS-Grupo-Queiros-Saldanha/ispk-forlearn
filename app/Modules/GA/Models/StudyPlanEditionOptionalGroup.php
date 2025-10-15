<?php

namespace App\Modules\GA\Models;

use App\Model;
use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Query\Builder;

/**
 * App\Modules\GA\Models\StudyPlanEditionOptionalGroup
 *
 * @property int $id
 * @property int $spe_id
 * @property int $optional_groups_id
 * @property int $period_types_id
 * @property int $year
 * @property-read Collection|Discipline[] $disciplines
 * @property-read OptionalGroup $optionalGroup
 * @property-read PeriodType $periodType
 * @property-read StudyPlanEdition $studyPlanEdition
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanEditionOptionalGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanEditionOptionalGroup newQuery()
 * @method static Builder|StudyPlanEditionOptionalGroup onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanEditionOptionalGroup query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanEditionOptionalGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanEditionOptionalGroup whereOptionalGroupsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanEditionOptionalGroup wherePeriodTypesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanEditionOptionalGroup whereSpeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|StudyPlanEditionOptionalGroup whereYear($value)
 * @method static Builder|StudyPlanEditionOptionalGroup withTrashed()
 * @method static Builder|StudyPlanEditionOptionalGroup withoutTrashed()
 * @mixin Eloquent
 * @property-read int|null $disciplines_count
 */
class StudyPlanEditionOptionalGroup extends Model
{
    protected $table = 'spe_has_optional_groups';

    public $timestamps = false;

    protected $fillable = [
        'spe_id',
        'optional_groups_id',
        'period_types_id',
        'year',
    ];

    //================================================================================
    // Relations
    //================================================================================

    public function disciplines()
    {
        return $this->hasMany(StudyPlanEditionOptionalGroupDiscipline::class, 'spe_has_og_id', 'id');
    }

    public function studyPlanEdition()
    {
        return $this->belongsTo(StudyPlanEdition::class, 'study_plan_editions_id', 'id');
    }

    public function optionalGroup()
    {
        return $this->belongsTo(OptionalGroup::class, 'optional_groups_id', 'id');
    }

    public function periodType()
    {
        return $this->belongsTo(PeriodType::class, 'period_types_id', 'id');
    }
}
