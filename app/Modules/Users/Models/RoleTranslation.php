<?php

namespace App\Modules\Users\Models;

use App\Model;
use App\Modules\Cms\Models\Language;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Modules\Users\Models\RoleTranslation
 *
 * @property int $id
 * @property int $role_id
 * @property int $language_id
 * @property string|null $display_name
 * @property string|null $description
 * @property int|null $version
 * @property int $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read Language $language
 * @property-read Role $role
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|RoleTranslation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RoleTranslation newQuery()
 * @method static Builder|RoleTranslation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|RoleTranslation query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|RoleTranslation whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoleTranslation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoleTranslation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoleTranslation whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoleTranslation whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoleTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoleTranslation whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoleTranslation whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoleTranslation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoleTranslation whereVersion($value)
 * @method static Builder|RoleTranslation withTrashed()
 * @method static Builder|RoleTranslation withoutTrashed()
 * @mixin Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|RoleTranslation disableCache()
 * @method static \Illuminate\Database\Eloquent\Builder|RoleTranslation withCacheCooldownSeconds($seconds = null)
 */
class RoleTranslation extends Model
{
    use SoftDeletes;
    //use Cachable;

    protected $fillable = [
        'role_id',
        'language_id',
        'display_name',
        'description',
        'version',
        'active',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    //================================================================================
    // Relations
    //================================================================================

    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    public function language()
    {
        return $this->hasOne(Language::class, 'id', 'language_id');
    }
}
