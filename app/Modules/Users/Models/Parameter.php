<?php

namespace App\Modules\Users\Models;

use App\Helpers\LanguageHelper;
use App\Model;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Spatie\Permission\Traits\HasRoles;

/**
 * App\Modules\Users\Models\Parameter
 *
 * @property-read Collection|ParameterOption[] $options
 * @property-read Collection|ParameterTranslation[] $translations
 * @method static bool|null forceDelete()
 * @method static Builder|Parameter newModelQuery()
 * @method static Builder|Parameter newQuery()
 * @method static \Illuminate\Database\Query\Builder|Parameter onlyTrashed()
 * @method static Builder|Parameter query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Query\Builder|Parameter withTrashed()
 * @method static \Illuminate\Database\Query\Builder|Parameter withoutTrashed()
 * @mixin Eloquent
 * @property int $id
 * @property string|null $code
 * @property int $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property string|null $type
 * @property int|null $has_options
 * @property-read User $createdBy
 * @property-read User|null $deletedBy
 * @property-read User|null $updatedBy
 * @method static Builder|Parameter whereCode($value)
 * @method static Builder|Parameter whereCreatedAt($value)
 * @method static Builder|Parameter whereCreatedBy($value)
 * @method static Builder|Parameter whereDeletedAt($value)
 * @method static Builder|Parameter whereDeletedBy($value)
 * @method static Builder|Parameter whereHasOptions($value)
 * @method static Builder|Parameter whereId($value)
 * @method static Builder|Parameter whereType($value)
 * @method static Builder|Parameter whereUpdatedAt($value)
 * @method static Builder|Parameter whereUpdatedBy($value)
 * @property-read Collection|ParameterOptionTranslation[] $optionsTranslations
 * @property-read Collection|UserParameter[] $parameters
 * @property-read ParameterTranslation $translation
 * @property int|null $parameter_group_id
 * @property-read Collection|ParameterHasParameterGroup[] $parameter_has_parameter_groups
 * @property-read Collection|ParameterRole[] $parameter_roles
 * @property-read Collection|Role[] $roles
 * @method static Builder|Parameter whereParameterGroupId($value)
 * @property-read Collection|ParameterGroup[] $groups
 * @property-read Collection|Permission[] $permissions
 * @method static Builder|Parameter permission($permissions)
 * @method static Builder|Parameter role($roles, $guard = null)
 * @property int $required
 * @property-read Collection|User[] $users
 * @method static Builder|Parameter whereRequired($value)
 * @property-read int|null $groups_count
 * @property-read int|null $options_count
 * @property-read int|null $permissions_count
 * @property-read int|null $roles_count
 * @property-read int|null $translations_count
 * @property-read int|null $users_count
 */
class Parameter extends Model
{
    use SoftDeletes;
    use HasRoles;

    protected $guard_name = 'web';

    protected $fillable = [
        'code',
        'created_by',
        'updated_by',
        'deleted_by',
        'created_at',
        'updated_at',
        'deleted_at',
        'type',
        'has_options',
        'required'
    ];

    //================================================================================
    // Translations
    //================================================================================

    public function translations()
    {
        return $this->hasMany(ParameterTranslation::class, 'parameters_id', 'id');
    }

    public function translation()
    {
        return $this->hasOne(ParameterTranslation::class, 'parameters_id', 'id');
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

    public function groups()
    {
        return $this->belongsToMany(ParameterGroup::class, 'parameter_has_parameter_groups', 'parameter_id', 'parameter_group_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_parameters', 'parameters_id', 'users_id');
    }

    //================================================================================
    // Creators
    //================================================================================

    public function options()
    {
        return $this->hasMany(ParameterOption::class, 'parameters_id', 'id');
    }
}
