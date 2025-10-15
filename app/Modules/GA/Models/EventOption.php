<?php

namespace App\Modules\GA\Models;

use App\Model;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Modules\GA\Models\EventOption
 *
 * @property int $id
 * @property int $event_id
 * @property string $key
 * @property string $value
 * @property-read Event $event
 * @method static Builder|EventOption newModelQuery()
 * @method static Builder|EventOption newQuery()
 * @method static Builder|EventOption query()
 * @method static Builder|EventOption whereEventId($value)
 * @method static Builder|EventOption whereId($value)
 * @method static Builder|EventOption whereKey($value)
 * @method static Builder|EventOption whereValue($value)
 * @mixin Eloquent
 */
class EventOption extends Model
{
    public $timestamps = false;

    protected $fillable = ['event_id', 'key', 'value'];

    //================================================================================
    // Relations
    //================================================================================

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

}
