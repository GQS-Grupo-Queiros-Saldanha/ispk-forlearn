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
 * App\Modules\GA\Models\Building
 *
 * @property int $id
 * @property string $code
 * @property int $created_by
 * @property int $updated_by
 * @property int $deleted_by
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read User $createdBy
 * @property-read User $deletedBy
 * @property-read BuildingTranslation $translation
 * @property-read Collection|BuildingTranslation[] $translations
 * @property-read int|null $translations_count
 * @property-read User $updatedBy
 * @method static \Illuminate\Database\Eloquent\Builder|Building newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Building newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Building query()
 * @method static \Illuminate\Database\Eloquent\Builder|Building whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Building whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Building whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Building whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Building whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Building whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Building whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Building whereUpdatedBy($value)
 * @mixin Eloquent
 * @property-read Collection|Room[] $rooms
 * @property-read int|null $rooms_count
 * @method static bool|null forceDelete()
 * @method static Builder|Building onlyTrashed()
 * @method static bool|null restore()
 * @method static Builder|Building withTrashed()
 * @method static Builder|Building withoutTrashed()
 */
class Building extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'code'
    ];

    //================================================================================
    // Translations
    //================================================================================

    public function translations()
    {
        return $this->hasMany(BuildingTranslation::class);
    }

    public function translation()
    {
        return $this->hasOne(BuildingTranslation::class);
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

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }
}
