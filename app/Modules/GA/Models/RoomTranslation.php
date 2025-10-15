<?php

namespace App\Modules\GA\Models;

use App\Model;
use App\Modules\Cms\Models\Language;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Modules\GA\Models\RoomTranslation
 *
 * @property int $id
 * @property int $room_id
 * @property int $language_id
 * @property string $display_name
 * @property string $description
 * @property string $abbreviation
 * @property int $version
 * @property int $active
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 * @property int|null $deleted_at
 * @property-read Language $language
 * @property-read Room $room
 * @method static \Illuminate\Database\Eloquent\Builder|RoomTranslation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RoomTranslation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RoomTranslation query()
 * @method static \Illuminate\Database\Eloquent\Builder|RoomTranslation whereAbbreviation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoomTranslation whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoomTranslation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoomTranslation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoomTranslation whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoomTranslation whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoomTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoomTranslation whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoomTranslation whereRoomId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoomTranslation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RoomTranslation whereVersion($value)
 * @mixin Eloquent
 * @method static bool|null forceDelete()
 * @method static Builder|RoomTranslation onlyTrashed()
 * @method static bool|null restore()
 * @method static Builder|RoomTranslation withTrashed()
 * @method static Builder|RoomTranslation withoutTrashed()
 */
class RoomTranslation extends Model
{
    use SoftDeletes;

    //================================================================================
    // Relations
    //================================================================================

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }
}
