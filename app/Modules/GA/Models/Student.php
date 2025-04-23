<?php

namespace App\Modules\GA\Models;

use App\Model;
use App\Modules\Users\Models\Parameter;
use App\Modules\Users\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

/**
 * App\Modules\GA\Models\Student
 *
 * @property int $id
 * @property int $users_id ID do utilizador associado
 * @property string|null $number
 * @property Carbon|null $created_at
 * @property string|null $deleted_at
 * @property int $created_by ID do utilizador que criou este item
 * @property int|null $updated_by ID do último utilizador que editou este item
 * @property-read Collection|Parameter[] $parameters
 * @property-read User $user
 * @method static Builder|Student newModelQuery()
 * @method static Builder|Student newQuery()
 * @method static Builder|Student query()
 * @method static Builder|Student whereCreatedAt($value)
 * @method static Builder|Student whereCreatedBy($value)
 * @method static Builder|Student whereDeletedAt($value)
 * @method static Builder|Student whereId($value)
 * @method static Builder|Student whereNumber($value)
 * @method static Builder|Student whereUpdatedBy($value)
 * @method static Builder|Student whereUsersId($value)
 * @mixin Eloquent
 * @property Carbon|null $updated_at
 * @method static Builder|Student whereUpdatedAt($value)
 * @property-read int|null $parameters_count
 */
class Student extends Model
{
    protected $fillable = [
        'number',
        'users_id',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at'
    ];

    //================================================================================
    // Relations
    //================================================================================

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id');
    }

    public function parameters()
    {
        return $this
            ->belongsToMany(Parameter::class, 'student_has_parameters', 'students_id', 'parameters_id')
            ->withTimestamps()
            ->withPivot(['enrollments_id']);
    }
}
