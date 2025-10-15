<?php

namespace App\Modules\GA\Models;

use App\Helpers\LanguageHelper;
use App\Model;
use App\Modules\Users\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Modules\GA\Models\EventType
 *
 * @property int $id
 * @property string $code
 * @property int $created_by
 * @property int $updated_by
 * @property int $deleted_by
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read User $createdBy
 * @property-read User $deletedBy
 * @property-read EventTypeTranslation $translation
 * @property-read Collection|EventTypeTranslation[] $translations
 * @property-read int|null $translations_count
 * @property-read User $updatedBy
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|EventType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EventType newQuery()
 * @method static Builder|EventType onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|EventType query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|EventType whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventType whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventType whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventType whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventType whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EventType whereUpdatedBy($value)
 * @method static Builder|EventType withTrashed()
 * @method static Builder|EventType withoutTrashed()
 * @mixin Eloquent
 * @property-read Event $event
 */
class EventType extends Model
{
    use SoftDeletes;

    protected $fillable = ['code', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at'];

    //================================================================================
    // Translations
    //================================================================================

    public function translations()
    {
        return $this->hasMany(EventTypeTranslation::class);
    }

    public function translation()
    {
        return $this->hasOne(EventTypeTranslation::class);
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

    public function event()
    {
        return $this->hasOne(Event::class);
    }

}
