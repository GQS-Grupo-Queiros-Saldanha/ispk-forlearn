<?php

namespace App\Modules\Users\Models;

use App\Model;
use App\Modules\Cms\Models\Language;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Modules\Users\Models\PermissionTranslation
 *
 * @property int $id
 * @property int $permission_id
 * @property int $language_id
 * @property string|null $display_name
 * @property string|null $description
 * @property int|null $version
 * @property int $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read Language $language
 * @property-read Permission $permission
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionTranslation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionTranslation newQuery()
 * @method static Builder|PermissionTranslation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionTranslation query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionTranslation whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionTranslation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionTranslation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionTranslation whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionTranslation whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionTranslation whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionTranslation wherePermissionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionTranslation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionTranslation whereVersion($value)
 * @method static Builder|PermissionTranslation withTrashed()
 * @method static Builder|PermissionTranslation withoutTrashed()
 * @mixin Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionTranslation disableCache()
 * @method static \Illuminate\Database\Eloquent\Builder|PermissionTranslation withCacheCooldownSeconds($seconds = null)
 */
class PermissionTranslation extends Model
{
    use SoftDeletes;
    //use Cachable;

    protected $fillable = [
        'permission_id',
        'language_id',
        'display_name',
        'description',
        'version',
        'active',
    ];

    //================================================================================
    // Relations
    //================================================================================

    public function permission()
    {
        return $this->belongsTo('App\Modules\Users\Models\Permission', 'permission_id');
    }

    public function language()
    {
        return $this->hasOne('App\Modules\Cms\Models\Language', 'id', 'language_id');
    }
}
