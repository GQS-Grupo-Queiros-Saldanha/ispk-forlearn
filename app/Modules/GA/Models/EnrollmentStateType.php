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
 * App\Modules\GA\Models\EnrollmentStateType
 *
 * @property int $id
 * @property string $code
 * @property int $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read User $createdBy
 * @property-read User|null $deletedBy
 * @property-read EnrollmentStateTypeTranslation $translation
 * @property-read Collection|EnrollmentStateTypeTranslation[] $translations
 * @property-read User|null $updatedBy
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|EnrollmentStateType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EnrollmentStateType newQuery()
 * @method static Builder|EnrollmentStateType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EnrollmentStateType query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|EnrollmentStateType whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnrollmentStateType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnrollmentStateType whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnrollmentStateType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnrollmentStateType whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnrollmentStateType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnrollmentStateType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EnrollmentStateType whereUpdatedBy($value)
 * @method static Builder|EnrollmentStateType withTrashed()
 * @method static Builder|EnrollmentStateType withoutTrashed()
 * @mixin Eloquent
 * @property-read int|null $translations_count
 */
class EnrollmentStateType extends Model
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
        return $this->hasMany(EnrollmentStateTypeTranslation::class, 'enrollment_state_types_id', 'id');
    }

    public function translation()
    {
        return $this->hasOne(EnrollmentStateTypeTranslation::class, 'enrollment_state_types_id', 'id');
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


}
