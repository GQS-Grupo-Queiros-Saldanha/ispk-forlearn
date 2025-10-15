<?php

namespace App\Modules\GA\Models;

use App\Model;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Modules\GA\Models\StudyPlanEditionAccessType
 *
 * @property int $spe_id
 * @property int $access_type_id
 * @property int|null $max_enrollments
 * @property-read AccessType $accessType
 * @property-read StudyPlanEdition $studyPlanEdition
 * @method static Builder|StudyPlanEditionAccessType newModelQuery()
 * @method static Builder|StudyPlanEditionAccessType newQuery()
 * @method static Builder|StudyPlanEditionAccessType query()
 * @method static Builder|StudyPlanEditionAccessType whereAccessTypeId($value)
 * @method static Builder|StudyPlanEditionAccessType whereMaxEnrollments($value)
 * @method static Builder|StudyPlanEditionAccessType whereSpeId($value)
 * @mixin Eloquent
 */
class StudyPlanEditionAccessType extends Model
{
    protected $table = 'spe_has_access_types';

    public $timestamps = false;

    protected $fillable = [
        'study_plan_editions_id',
        'access_type_id',
        'max_enrollments'
    ];

    //================================================================================
    // Relations
    //================================================================================

    public function studyPlanEdition()
    {
        return $this->belongsTo(StudyPlanEdition::class, 'study_plan_editions_id', 'id');
    }

    public function accessType()
    {
        return $this->belongsTo(AccessType::class, 'access_type_id', 'id');
    }

}
