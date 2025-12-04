<?php

namespace App\Modules\Users\Models;

use App\Modules\GA\Models\Classes;
use App\Modules\GA\Models\Course;
use App\Modules\GA\Models\Discipline;
use App\Modules\GA\Models\Department;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Spatie\Permission\Traits\HasRoles;

/**
 * App\Modules\Users\Models\User
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read DatabaseNotificationCollection|DatabaseNotification[] $notifications
 * @property-read Collection|\Spatie\Permission\Models\Permission[] $permissions
 * @property-read Collection|\Spatie\Permission\Models\Role[] $roles
 * @method static bool|null forceDelete()
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static \Illuminate\Database\Query\Builder|User onlyTrashed()
 * @method static Builder|User permission($permissions)
 * @method static Builder|User query()
 * @method static bool|null restore()
 * @method static Builder|User role($roles, $guard = null)
 * @method static Builder|User whereCreatedAt($value)
 * @method static Builder|User whereDeletedAt($value)
 * @method static Builder|User whereEmail($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereName($value)
 * @method static Builder|User wherePassword($value)
 * @method static Builder|User whereRememberToken($value)
 * @method static Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Query\Builder|User withTrashed()
 * @method static \Illuminate\Database\Query\Builder|User withoutTrashed()
 * @mixin Eloquent
 * @property-read Collection|UserParameter[] $parameters
 * @property string|null $image
 * @method static Builder|User whereImage($value)
 * @property-read int|null $notifications_count
 * @property-read int|null $parameters_count
 * @property-read int|null $permissions_count
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Modules\GA\Models\Course[] $courses
 * @property-read int|null $courses_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Modules\GA\Models\Discipline[] $disciplines
 * @property-read int|null $disciplines_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Modules\GA\Models\Classes[] $classes
 * @property-read int|null $classes_count
 * @property-read \App\Modules\Users\Models\UserCandidate $candidate
 * @property-read \App\Modules\Users\Models\Matriculation $matriculation
 * @property int $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Users\Models\User whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Users\Models\User whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Users\Models\User whereUpdatedBy($value)
 */
class User extends Authenticatable
{
    use HasRoles;
    use SoftDeletes;
    use Notifiable;

    protected $guard_name = 'web';

    protected $fillable = ['name', 'email', 'password','image', 'duplicate', 'is_duplicate', 'created_at', 'updated_at', 'deleted_at', 'created_by', 'credit_balance'];

    protected $hidden = [
        'password', 'remember_token'
    ];

    //================================================================================
    // Relations
    //================================================================================

    public function user_parameters()
    {
        return $this->hasMany(UserParameter::Class,'users_id');
    }
    
    public function parameters()
    {
        return $this->belongsToMany(Parameter::class, 'user_parameters', 'users_id', 'parameters_id')->withTimestamps()->withPivot(['value', 'parameter_group_id']);
    }

    public function courses()
    {
        return $this->belongsToMany(Course::class, 'user_courses', 'users_id', 'courses_id');
    }
    public function transference()
    {
        return $this->hasMany(\App\Modules\Users\Models\TransferenceStudant::class, 'user_id', 'id');
    }


    public function disciplines()
    {
        return $this->belongsToMany(Discipline::class, 'user_disciplines', 'users_id', 'disciplines_id');
    }

    public function classes()
    {
        return $this->belongsToMany(Classes::class, 'user_classes', 'user_id', 'class_id');
    }
    
      public function departments()
    {
        return $this->belongsToMany(Department::class, 'users_departments', 'user_id', 'departments_id');
    }

    public function candidate()
    {
        return $this->hasOne(UserCandidate::class);
    }

    public function matriculation()
    {
        return $this->hasOne(Matriculation::class);
    }

    public function arg()
    {
        return $this->hasMany('App\Modules\Payments\Models\DisciplineArticle');
    }
}
