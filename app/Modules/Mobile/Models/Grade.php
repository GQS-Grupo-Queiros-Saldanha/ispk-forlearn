<?php

namespace App\Modules\Grades\Models;

use App\Model;
use App\Modules\GA\Models\Course;
use App\Modules\GA\Models\Discipline;
use App\Modules\Users\Models\User;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Modules\Grade\Models\Grade
 *
 * @property int $id
 * @property int $course_id
 * @property int $discipline_id
 * @property int $student_id
 * @property int $value
 * @property int $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Grade\Models\Grade newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Grade\Models\Grade newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Grade\Models\Grade query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Grade\Models\Grade whereCourseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Grade\Models\Grade whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Grade\Models\Grade whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Grade\Models\Grade whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Grade\Models\Grade whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Grade\Models\Grade whereDisciplineId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Grade\Models\Grade whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Grade\Models\Grade whereStudentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Grade\Models\Grade whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Grade\Models\Grade whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Grade\Models\Grade whereValue($value)
 * @mixin \Eloquent
 * @property-read \App\Modules\GA\Models\Course $course
 * @property-read \App\Modules\Users\Models\User $createdBy
 * @property-read \App\Modules\Users\Models\User|null $deletedBy
 * @property-read \App\Modules\GA\Models\Discipline $discipline
 * @property-read \App\Modules\Users\Models\User $student
 * @property-read \App\Modules\Users\Models\User|null $updatedBy
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\App\Modules\Grades\Models\Grade onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Query\Builder|\App\Modules\Grades\Models\Grade withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Modules\Grades\Models\Grade withoutTrashed()
 */
class Grade extends Model {

    use SoftDeletes;

    protected $fillable = [
        'course_id',
        'discipline_id',
        'student_id',
        'value',
        'created_by',
        'updated_by',
        'deleted_by',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    //================================================================================
    // Relations
    //================================================================================

    public function course()
    {
        return $this->belongsTo(Course::class, 'course_id');
    }

    public function discipline()
    {
        return $this->belongsTo(Discipline::class, 'discipline_id');
    }

    public function student()
    {
        return $this->belongsTo(User::class, 'student_id');
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
