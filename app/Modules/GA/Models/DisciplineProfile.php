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
 * App\Modules\GA\Models\DisciplineProfile
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
 * @property-read Collection|Discipline[] $disciplines
 * @property-read DisciplineProfileTranslation $translation
 * @property-read Collection|DisciplineProfileTranslation[] $translations
 * @property-read User|null $updatedBy
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineProfile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineProfile newQuery()
 * @method static Builder|DisciplineProfile onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineProfile query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineProfile whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineProfile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineProfile whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineProfile whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineProfile whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineProfile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineProfile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineProfile whereUpdatedBy($value)
 * @method static Builder|DisciplineProfile withTrashed()
 * @method static Builder|DisciplineProfile withoutTrashed()
 * @mixin Eloquent
 * @property-read int|null $disciplines_count
 * @property-read int|null $translations_count
 */
class DisciplineProfile extends Model
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
        return $this->hasMany(DisciplineProfileTranslation::class, 'discipline_profiles_id', 'id');
    }

    public function translation()
    {
        return $this->hasOne(DisciplineProfileTranslation::class, 'discipline_profiles_id', 'id');
    }

    public function currentTranslation()
    {
        return $this
            ->translation()
            ->whereActive(true)
            ->whereLanguageId(LanguageHelper::getCurrentLanguage());
    }

    //================================================================================
    // Relations
    //================================================================================

    public function disciplines()
    {
        return $this->hasMany(Discipline::class, 'discipline_profiles_id');
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
