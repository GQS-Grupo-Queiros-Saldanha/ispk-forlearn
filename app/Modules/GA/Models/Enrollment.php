<?php

namespace App\Modules\GA\Models;

use App\Model;
use App\Modules\Users\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

/**
 * App\Modules\GA\Models\Enrollment
 *
 * @property int $id
 * @property int $students_id
 * @property int $candidate_id
 * @property int $study_plan_editions_id
 * @property int $access_type_id
 * @property int $status
 * @property int|null $year
 * @property int $partial_time
 * @property int $created_by
 * @property int|null $updated_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read AccessType $accessType
 * @property-read User $createdBy
 * @property-read Collection|Discipline[] $disciplines
 * @property-read Collection|EnrollmentStateType[] $stateTypes
 * @property-read Student $student
 * @property-read StudyPlanEdition $studyPlanEdition
 * @property-read User|null $updatedBy
 * @method static Builder|Enrollment newModelQuery()
 * @method static Builder|Enrollment newQuery()
 * @method static Builder|Enrollment query()
 * @method static Builder|Enrollment whereAccessTypeId($value)
 * @method static Builder|Enrollment whereCandidateId($value)
 * @method static Builder|Enrollment whereCreatedAt($value)
 * @method static Builder|Enrollment whereCreatedBy($value)
 * @method static Builder|Enrollment whereId($value)
 * @method static Builder|Enrollment wherePartialTime($value)
 * @method static Builder|Enrollment whereStatus($value)
 * @method static Builder|Enrollment whereStudentsId($value)
 * @method static Builder|Enrollment whereStudyPlanEditionsId($value)
 * @method static Builder|Enrollment whereUpdatedAt($value)
 * @method static Builder|Enrollment whereUpdatedBy($value)
 * @method static Builder|Enrollment whereYear($value)
 * @mixin Eloquent
 * @property-read int|null $disciplines_count
 * @property-read int|null $state_types_count
 */
class Enrollment extends Model
{

    protected $fillable = [
        'students_id',
        'candidate_id',
        'study_plan_editions_id',
        'access_type_id',
        'status',
        'year',
        'partial_time',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    //================================================================================
    // Relations
    //================================================================================

    public function student()
    {
        return $this->belongsTo(Student::class, 'students_id');
    }

    public function studyPlanEdition()
    {
        return $this->belongsTo(StudyPlanEdition::class, 'study_plan_editions_id');
    }

    public function accessType()
    {
        return $this->belongsTo(AccessType::class, 'access_type_id');
    }

    public function stateTypes()
    {
        return $this
            ->belongsToMany(EnrollmentStateType::class, 'enrollments_has_enrollment_state_types', 'enrollments_id', 'enrollment_state_types_id')
            ->withPivot(['explanation']);
    }

    public function disciplines()
    {
        return $this
            ->belongsToMany(Discipline::class, 'enrollments_has_disciplines', 'enrollments_id', 'disciplines_id')
            ->withPivot([
                'id',
                'status',
                'optional_groups_id'
            ]);
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
}
