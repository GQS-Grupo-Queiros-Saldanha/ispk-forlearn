<?php

namespace App\Modules\GA\Models;

use App\Model;
use App\Modules\Users\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Modules\GA\Models\Precedence
 *
 * @property int $id
 * @property int|null $precedence_id
 * @property int|null $discipline_id
 * @property int $study_plan_editions_id
 * @property int $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read User $createdBy
 * @property-read User|null $deletedBy
 * @property-read Discipline|null $discipline
 * @property-read Precedence|null $parent
 * @property-read StudyPlanEdition $studyPlanEdition
 * @property-read User|null $updatedBy
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|Precedence newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Precedence newQuery()
 * @method static Builder|Precedence onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Precedence query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|Precedence whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Precedence whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Precedence whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Precedence whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Precedence whereDisciplineId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Precedence whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Precedence wherePrecedenceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Precedence whereStudyPlanEditionsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Precedence whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Precedence whereUpdatedBy($value)
 * @method static Builder|Precedence withTrashed()
 * @method static Builder|Precedence withoutTrashed()
 * @mixin Eloquent
 */
class Precedence extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'precedence_id',
        'discipline_id',
        'study_plan_editions_id',
        'created_by',
        'updated_by',
        'deleted_by',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

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

    //================================================================================
    // Relations
    //================================================================================

    public function parent()
    {
        return $this->belongsTo(Discipline::class, 'precedence_id', 'id');
    }

    public function discipline()
    {
        return $this->belongsTo(Discipline::class, 'discipline_id', 'id');
    }

    public function studyPlanEdition()
    {
        return $this->belongsTo(StudyPlanEdition::class, 'study_plan_editions_id', 'id');
    }


}
