<?php

namespace App\Modules\Users\Models;

use App\Helpers\LanguageHelper;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

/**
 * App\Modules\Users\Models\Permission
 *
 * @property int $id
 * @property string $guard_name
 * @property string $name
 * @property int $created_by
 * @property int|null $updated_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|\Spatie\Permission\Models\Permission[] $permissions
 * @property-read Collection|\Spatie\Permission\Models\Role[] $roles
 * @property-read Collection|PermissionTranslation[] $translations
 * @property-read Collection|User[] $users
 * @method static Builder|Permission newModelQuery()
 * @method static Builder|Permission newQuery()
 * @method static Builder|\Spatie\Permission\Models\Permission permission($permissions)
 * @method static Builder|Permission query()
 * @method static Builder|\Spatie\Permission\Models\Permission role($roles, $guard = null)
 * @method static Builder|Permission whereCreatedAt($value)
 * @method static Builder|Permission whereCreatedBy($value)
 * @method static Builder|Permission whereGuardName($value)
 * @method static Builder|Permission whereId($value)
 * @method static Builder|Permission whereName($value)
 * @method static Builder|Permission whereUpdatedAt($value)
 * @method static Builder|Permission whereUpdatedBy($value)
 * @mixin Eloquent
 * @property-read User $createdBy
 * @property-read User $deletedBy
 * @property-read User|null $updatedBy
 * @property-read PermissionTranslation $translation
 * @method static Builder|Permission disableCache()
 * @method static Builder|Permission withCacheCooldownSeconds($seconds = null)
 * @property-read int|null $permissions_count
 * @property-read int|null $roles_count
 * @property-read int|null $translations_count
 * @property-read int|null $users_count
 */
class Permission extends \Spatie\Permission\Models\Permission
{
    //use Cachable;


    //================================================================================
    // Translations
    //================================================================================

    public function translations()
    {
        return $this->hasMany(PermissionTranslation::class, 'permission_id', 'id');
    }

    public function translation()
    {
        return $this->hasOne(PermissionTranslation::class, 'permission_id', 'id');
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
