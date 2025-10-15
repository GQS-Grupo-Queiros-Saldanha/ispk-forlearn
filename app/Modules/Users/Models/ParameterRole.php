<?php

namespace App\Modules\Users\Models;

use App\Model;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;


/**
 * App\Modules\Users\Models\ParameterRole
 *
 * @property int $id
 * @property int $parameter_id
 * @property int $role_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Parameter $parameter
 * @property-read Role $roles
 * @method static Builder|ParameterRole newModelQuery()
 * @method static Builder|ParameterRole newQuery()
 * @method static Builder|ParameterRole query()
 * @method static Builder|ParameterRole whereCreatedAt($value)
 * @method static Builder|ParameterRole whereId($value)
 * @method static Builder|ParameterRole whereParameterId($value)
 * @method static Builder|ParameterRole whereRoleId($value)
 * @method static Builder|ParameterRole whereUpdatedAt($value)
 * @mixin Eloquent
 */
class ParameterRole extends Model
{
    protected $table = 'parameter_roles';

    protected $fillable = [
        'parameter_id',
        'role_id',
        'created_at',
        'updated_at'
    ];

    //================================================================================
    // Relations
    //================================================================================

    public function parameter()
    {
        return $this->belongsTo(Parameter::class, 'parameter_id','id');
    }

    public function roles()
    {
        return $this->belongsTo(Role::class, 'role_id','id');
    }

}
