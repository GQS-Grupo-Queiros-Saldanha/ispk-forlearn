<?php

namespace App\Modules\Users\Models;

use App\Helpers\LanguageHelper;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

/**
 * Class Role
 *
 * @package App\Modules\Users\Models
 * @property int $id
 * @property string $name
 * @property string $guard_name
 * @property int $created_by
 * @property int|null $updated_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|\Spatie\Permission\Models\Permission[] $permissions
 * @property-read Collection|RoleTranslation[] $translations
 * @property-read Collection|User[] $users
 * @method static Builder|Role newModelQuery()
 * @method static Builder|Role newQuery()
 * @method static Builder|\Spatie\Permission\Models\Role permission($permissions)
 * @method static Builder|Role query()
 * @method static Builder|Role whereCreatedAt($value)
 * @method static Builder|Role whereCreatedBy($value)
 * @method static Builder|Role whereGuardName($value)
 * @method static Builder|Role whereId($value)
 * @method static Builder|Role whereName($value)
 * @method static Builder|Role whereUpdatedAt($value)
 * @method static Builder|Role whereUpdatedBy($value)
 * @mixin Eloquent
 * @property-read User $createdBy
 * @property-read User|null $updatedBy
 * @property-read RoleTranslation $translation
 * @method static Builder|Role disableCache()
 * @method static Builder|Role withCacheCooldownSeconds($seconds = null)
 * @property-read int|null $permissions_count
 * @property-read int|null $translations_count
 * @property-read int|null $users_count
 */
class Role extends \Spatie\Permission\Models\Role
{
    //use Cachable;

    protected $fillable = [
        'name',
        'guard_name',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
    ];

    //================================================================================
    // Translations
    //================================================================================

    public function translations()
    {
        return $this->hasMany(RoleTranslation::class, 'role_id', 'id');
    }

    public function translation()
    {
        return $this->hasOne(RoleTranslation::class, 'role_id', 'id');
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

}
