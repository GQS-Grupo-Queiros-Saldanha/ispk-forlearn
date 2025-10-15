<?php

namespace App\Modules\GA\Models;

use App\Model;
use App\Modules\Users\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Modules\GA\Models\DisciplineCurricula
 *
 * @property int $id
 * @property int $disciplines_id
 * @property int $study_plan_editions_id
 * @property int $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read User $createdBy
 * @property-read User|null $deletedBy
 * @property-read Discipline $discipline
 * @property-read StudyPlanEdition $study_plan_edition
 * @property-read DisciplineCurriculaTranslation $translation
 * @property-read Collection|DisciplineCurriculaTranslation[] $translations
 * @property-read User|null $updatedBy
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineCurricula newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineCurricula newQuery()
 * @method static Builder|DisciplineCurricula onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineCurricula query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineCurricula whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineCurricula whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineCurricula whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineCurricula whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineCurricula whereDisciplinesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineCurricula whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineCurricula whereStudyPlanEditionsId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineCurricula whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineCurricula whereUpdatedBy($value)
 * @method static Builder|DisciplineCurricula withTrashed()
 * @method static Builder|DisciplineCurricula withoutTrashed()
 * @mixin Eloquent
 * @property-read int|null $translations_count
 */
class DisciplineCurricula extends Model
{
    use SoftDeletes;

    protected $table = 'discipline_curricula';

    protected $fillable = [
        'study_plan_editions_id',
        'disciplines_id',
        'created_by',
        'updated_by',
        'deleted_by',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    //================================================================================
    // Translations
    //================================================================================

    public function translations()
    {
        return $this->hasMany(DisciplineCurriculaTranslation::class, 'discipline_curricula_id', 'id');
    }

    public function translation()
    {
        return $this->hasOne(DisciplineCurriculaTranslation::class, 'discipline_curricula_id', 'id');
    }

    //================================================================================
    // Relations
    //================================================================================

    public function study_plan_edition()
    {
        return $this->belongsTo(StudyPlanEdition::class, 'study_plan_editions_id', 'id');
    }

    public function discipline()
    {
        return $this->belongsTo(Discipline::class, 'disciplines_id', 'id');
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

    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }


}
