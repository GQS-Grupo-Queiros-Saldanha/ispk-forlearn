<?php

namespace App\Modules\Users\Models;

use App\Helpers\LanguageHelper;
use App\Model;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
use Spatie\Permission\Traits\HasRoles;

/**
 * App\Modules\Users\Models\ParameterGroup
 *
 * @property int $id
 * @property string|null $code
 * @property int $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read User $createdBy
 * @property-read User|null $deletedBy
 * @property-read Parameter $parameter
 * @property-read Collection|Parameter[] $parameters
 * @property-read ParameterGroupTranslation $translation
 * @property-read Collection|ParameterGroupTranslation[] $translations
 * @property-read User|null $updatedBy
 * @property-read Collection|UserParameter[] $user_parameters
 * @method static Builder|ParameterGroup newModelQuery()
 * @method static Builder|ParameterGroup newQuery()
 * @method static Builder|ParameterGroup query()
 * @method static Builder|ParameterGroup whereCode($value)
 * @method static Builder|ParameterGroup whereCreatedAt($value)
 * @method static Builder|ParameterGroup whereCreatedBy($value)
 * @method static Builder|ParameterGroup whereDeletedBy($value)
 * @method static Builder|ParameterGroup whereId($value)
 * @method static Builder|ParameterGroup whereUpdatedAt($value)
 * @method static Builder|ParameterGroup whereUpdatedBy($value)
 * @mixin Eloquent
 * @property-read Collection|Permission[] $permissions
 * @property-read Collection|Role[] $roles
 * @method static Builder|ParameterGroup permission($permissions)
 * @method static Builder|ParameterGroup role($roles, $guard = null)
 * @property int $order
 * @method static Builder|ParameterGroup whereOrder($value)
 * @property-read int|null $parameters_count
 * @property-read int|null $permissions_count
 * @property-read int|null $roles_count
 * @property-read int|null $translations_count
 */
class ParameterGroup extends Model
{
    use HasRoles;

    protected $guard_name = 'web';

    protected $table = 'parameter_groups';

    protected $fillable = [
        'code',
        'order',
        'created_by',
        'updated_by',
        'deleted_by',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    //================================================================================
    // Translations
    //================================================================================

    public function translations()
    {
        return $this->hasMany(ParameterGroupTranslation::class, 'parameter_group_id', 'id');
    }

    public function translation()
    {
        return $this->hasOne(ParameterGroupTranslation::class, 'parameter_group_id', 'id');
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

    public function parameters()
    {
        return $this
            ->belongsToMany(Parameter::class, 'parameter_has_parameter_groups', 'parameter_group_id', 'parameter_id')
            ->withPivot(['order'])
            ->orderBy('order');
    }
}
