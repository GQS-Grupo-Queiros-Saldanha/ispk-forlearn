<?php

namespace App\Modules\GA\Models;

use App\Helpers\LanguageHelper;
use App\Model;
use App\Modules\Users\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Modules\GA\Models\OptionalGroup
 *
 * @property int $id
 * @property string|null $code
 * @property int $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read User $createdBy
 * @property-read User|null $deletedBy
 * @property-read OptionalGroupTranslation $translation
 * @property-read Collection|OptionalGroupTranslation[] $translations
 * @property-read User|null $updatedBy
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|OptionalGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|OptionalGroup newQuery()
 * @method static Builder|OptionalGroup onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|OptionalGroup query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|OptionalGroup whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OptionalGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OptionalGroup whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OptionalGroup whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OptionalGroup whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OptionalGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OptionalGroup whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|OptionalGroup whereUpdatedBy($value)
 * @method static Builder|OptionalGroup withTrashed()
 * @method static Builder|OptionalGroup withoutTrashed()
 * @mixin Eloquent
 * @property-read Collection|StudyPlanEditionOptionalGroup[] $studyPlans
 * @property-read Collection|StudyPlanHasOptionalGroup[] $study_plans_has_optional_groups
 * @property-read int|null $study_plans_count
 * @property-read int|null $study_plans_has_optional_groups_count
 * @property-read int|null $translations_count
 */
class OptionalGroup extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
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
        return $this->hasMany(OptionalGroupTranslation::class, 'optional_groups_id', 'id');
    }

    public function translation()
    {
        return $this->hasOne(OptionalGroupTranslation::class, 'optional_groups_id', 'id');
    }

    public function currentTranslation()
    {
        return $this
            ->translation()
            ->whereActive(true)
            ->whereLanguageId(LanguageHelper::getCurrentLanguage());
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

    //================================================================================
    // Relations
    //================================================================================

    public function studyPlans()
    {
        return $this->hasMany(StudyPlanEditionOptionalGroup::class, 'optional_groups_id', 'id');
    }

    public function study_plans_has_optional_groups()
    {
        return $this->hasMany(StudyPlanHasOptionalGroup::class, 'optional_groups_id', 'id');

    }
}
