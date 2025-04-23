<?php

namespace App\Modules\GA\Models;

use App\Helpers\LanguageHelper;
use App\Model;
use App\Modules\Users\Models\User;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * App\Modules\GA\Models\Event
 *
 * @property int $id
 * @property string $title
 * @property \Illuminate\Support\Carbon $start
 * @property \Illuminate\Support\Carbon $end
 * @property int $all_day
 * @property-read Collection|EventOption[] $options
 * @property-read int|null $options_count
 * @method static Builder|Event newModelQuery()
 * @method static Builder|Event newQuery()
 * @method static Builder|Event query()
 * @method static Builder|Event whereAllDay($value)
 * @method static Builder|Event whereEnd($value)
 * @method static Builder|Event whereId($value)
 * @method static Builder|Event whereStart($value)
 * @method static Builder|Event whereTitle($value)
 * @mixin Eloquent
 * @property int $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property string|null $url
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read User $createdBy
 * @property-read User|null $deletedBy
 * @property-read EventTranslation $translation
 * @property-read Collection|EventTranslation[] $translations
 * @property-read int|null $translations_count
 * @property-read User|null $updatedBy
 * @method static Builder|Event whereCreatedAt($value)
 * @method static Builder|Event whereCreatedBy($value)
 * @method static Builder|Event whereDeletedAt($value)
 * @method static Builder|Event whereDeletedBy($value)
 * @method static Builder|Event whereUpdatedAt($value)
 * @method static Builder|Event whereUpdatedBy($value)
 * @method static Builder|Event whereUrl($value)
 * @property int $event_type_id
 * @property-read EventType $type
 * @method static Builder|Event whereEventTypeId($value)
 */
class Event extends Model implements \MaddHatter\LaravelFullcalendar\Event
{

    protected $fillable = [ 'event_type_id', 'start', 'end', 'all_day', 'url', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at'
    ];

    //================================================================================
    // Translations
    //================================================================================
    public function translations()
    {
        return $this->hasMany(EventTranslation::class);
    }

    public function translation()
    {
        return $this->hasOne(EventTranslation::class);
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

    public function type()
    {
        return $this->belongsTo(EventType::class, 'event_type_id');
    }

    public function options()
    {
        return $this->hasMany(EventOption::class);
    }

    //================================================================================
    // Laravel Fullcalendar
    //================================================================================

    public function getTitle()
    {
        return $this->currentTranslation->display_name;
    }

    public function isAllDay()
    {
        return (bool)$this->all_day;
    }

    public function getStart()
    {
        return Carbon::parse($this->start);
    }

    public function getEnd()
    {
        return Carbon::parse($this->end);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getEventOptions()
    {
        return $this
            ->options()
            ->pluck('value', 'key')
            ->toArray();
    }
}
