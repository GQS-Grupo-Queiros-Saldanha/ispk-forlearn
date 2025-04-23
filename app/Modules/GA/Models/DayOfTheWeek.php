<?php

namespace App\Modules\GA\Models;

use App\Helpers\LanguageHelper;
use App\Model;
use App\Modules\Users\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

/**
 * App\Modules\GA\Models\DayOfTheWeek
 *
 * @property int $id
 * @property string $code
 * @property int $is_start_of_week
 * @property int $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read User $createdBy
 * @property-read User|null $deletedBy
 * @property-read DayOfTheWeekTranslation $translation
 * @property-read Collection|DayOfTheWeekTranslation[] $translations
 * @property-read int|null $translations_count
 * @property-read User|null $updatedBy
 * @method static Builder|DayOfTheWeek newModelQuery()
 * @method static Builder|DayOfTheWeek newQuery()
 * @method static Builder|DayOfTheWeek query()
 * @method static Builder|DayOfTheWeek whereCode($value)
 * @method static Builder|DayOfTheWeek whereCreatedAt($value)
 * @method static Builder|DayOfTheWeek whereCreatedBy($value)
 * @method static Builder|DayOfTheWeek whereDeletedAt($value)
 * @method static Builder|DayOfTheWeek whereDeletedBy($value)
 * @method static Builder|DayOfTheWeek whereId($value)
 * @method static Builder|DayOfTheWeek whereIsStartOfWeek($value)
 * @method static Builder|DayOfTheWeek whereUpdatedAt($value)
 * @method static Builder|DayOfTheWeek whereUpdatedBy($value)
 * @mixin Eloquent
 */
class DayOfTheWeek extends Model
{
    protected $table = 'days_of_the_week';

    //================================================================================
    // Translations
    //================================================================================

    public function translations()
    {
        return $this->hasMany(DayOfTheWeekTranslation::class);
    }

    public function translation()
    {
        return $this->hasOne(DayOfTheWeekTranslation::class);
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
}
