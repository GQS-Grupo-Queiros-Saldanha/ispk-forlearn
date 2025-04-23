<?php

namespace App\Modules\GA\Models;

use App\Model;
use App\Modules\Users\Models\Matriculation;
use App\Modules\Users\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Modules\GA\Models\Classes
 *
 * @property int $id
 * @property string $code
 * @property string $display_name
 * @property int $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read User $createdBy
 * @property-read User|null $deletedBy
 * @property-read User|null $updatedBy
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|Classes newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Classes newQuery()
 * @method static Builder|Classes onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|Classes query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|Classes whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Classes whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Classes whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Classes whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Classes whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Classes whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Classes whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Classes whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Classes whereUpdatedBy($value)
 * @method static Builder|Classes withTrashed()
 * @method static Builder|Classes withoutTrashed()
 * @mixin Eloquent
 * @property int|null $room_id
 * @property int $vacancies
 * @property-read \App\Modules\GA\Models\Room $room
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\GA\Models\Classes whereRoomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\GA\Models\Classes whereVacancies($value)
 * @property int|null $courses_id
 * @property int $year
 * @property-read \App\Modules\GA\Models\Course $course
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\GA\Models\Classes whereCoursesId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\GA\Models\Classes whereYear($value)
 */
class Classes extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'display_name',
        'room_id',
        'vacancies',
        'courses_id',
        'year',
        'created_by',
        'updated_by',
        'deleted_by',
        'created_at',
        'updated_at',
        'deleted_at',
        'schedule_type_id',
        'lective_year_id'
    ];

    public function room()
    {
        return $this->hasOne(Room::class, 'id', 'room_id');
    }

    public function course()
    {
        return $this->hasOne(Course::class, 'id', 'courses_id');
    }

    public function matriculations()
    {
        return $this->belongsToMany(Matriculation::class, 'matriculation_classes', 'class_id', 'matriculation_id');
    }

    public function scheduleType()
    {
        return $this->hasOne(ScheduleType::class, 'id', 'schedule_type_id');
    }

    public function lectiveYear()
    {
        return $this->hasOne(LectiveYear::class, 'id','lective_year_id');
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
