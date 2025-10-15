<?php

namespace App\Modules\Users\Models;

use App\Model;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Modules\Users\Models\UserParameter
 *
 * @property-read Parameter $parameter
 * @property-read User $user
 * @method static bool|null forceDelete()
 * @method static Builder|UserParameter newModelQuery()
 * @method static Builder|UserParameter newQuery()
 * @method static \Illuminate\Database\Query\Builder|UserParameter onlyTrashed()
 * @method static Builder|UserParameter query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Query\Builder|UserParameter withTrashed()
 * @method static \Illuminate\Database\Query\Builder|UserParameter withoutTrashed()
 * @mixin Eloquent
 * @property int $id
 * @property int $users_id
 * @property int $parameters_id
 * @property string $value
 * @property int $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static Builder|UserParameter whereCreatedAt($value)
 * @method static Builder|UserParameter whereCreatedBy($value)
 * @method static Builder|UserParameter whereDeletedAt($value)
 * @method static Builder|UserParameter whereDeletedBy($value)
 * @method static Builder|UserParameter whereId($value)
 * @method static Builder|UserParameter whereParametersId($value)
 * @method static Builder|UserParameter whereUpdatedAt($value)
 * @method static Builder|UserParameter whereUpdatedBy($value)
 * @method static Builder|UserParameter whereUsersId($value)
 * @method static Builder|UserParameter whereValue($value)
 * @property int|null $parameter_group_id
 * @property string|null $description
 * @property-read ParameterGroup|null $parameter_group
 * @method static Builder|UserParameter whereDescription($value)
 * @method static Builder|UserParameter whereParameterGroupId($value)
 */
class UserParameter extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'users_id',
        'parameters_id',
        'parameter_group_id',
        'description',
        'value',
        'is_duplicate',
        'created_at',
        'updated_at',
        'deleted_at',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    //================================================================================
    // Relations
    //================================================================================

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function parameter_group()
    {
        return $this->belongsTo(ParameterGroup::class, 'parameter_group_id');
    }

    public function parameter()
    {
        return $this->belongsTo(Parameter::class, 'parameters_id');
    }
}
