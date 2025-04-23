<?php

namespace App\Modules\GA\Models;

use App\Model;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;

/**
 * App\Modules\GA\Models\ScheduleEvent
 *
 * @property-read DayOfTheWeek $dayOfTheWeek
 * @property-read StudyPlanEditionDiscipline $discipline
 * @property-read Room $room
 * @property-read ScheduleTypeTime $time
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleEvent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleEvent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ScheduleEvent query()
 * @mixin Eloquent
 * @method static bool|null forceDelete()
 * @method static Builder|ScheduleEvent onlyTrashed()
 * @method static bool|null restore()
 * @method static Builder|ScheduleEvent withTrashed()
 * @method static Builder|ScheduleEvent withoutTrashed()
 * @property int $id
 * @property int $schedule_id
 * @property int $schedule_type_time_id
 * @property int $spe_discipline_id
 * @property int $room_id
 * @property int $day_of_the_week_id
 * @property int $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\GA\Models\ScheduleEvent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\GA\Models\ScheduleEvent whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\GA\Models\ScheduleEvent whereDayOfTheWeekId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\GA\Models\ScheduleEvent whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\GA\Models\ScheduleEvent whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\GA\Models\ScheduleEvent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\GA\Models\ScheduleEvent whereRoomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\GA\Models\ScheduleEvent whereScheduleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\GA\Models\ScheduleEvent whereScheduleTypeTimeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\GA\Models\ScheduleEvent whereSpeDisciplineId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\GA\Models\ScheduleEvent whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\GA\Models\ScheduleEvent whereUpdatedBy($value)
 */
class ScheduleEvent extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code',
        'schedule_type_time_id',
        'spe_dsicipline_id',
        'room_id',
        'day_of_the_week_id',
    ];

    //================================================================================
    // Relations
    //================================================================================

    public function time()
    {
        return $this->belongsTo(ScheduleTypeTime::class, 'schedule_type_time_id', 'id');
    }

    public function discipline()
    {
        //return $this->belongsToMany(Discipline::class, 'study_plan_edition_disciplines', 'study_plan_edition_id', 'discipline_id');
        return $this->belongsTo(Discipline::class, 'spe_discipline_id', 'id');
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function dayOfTheWeek()
    {
        return $this->belongsTo(DayOfTheWeek::class);
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class, 'schedule_id', 'id');
    }
}
