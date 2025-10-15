<?php

namespace App\Modules\GA\Models;

use App\Model;
use App\Modules\Cms\Models\Language;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Modules\GA\Models\DayOfTheWeekTranslation
 *
 * @property int $id
 * @property int $day_of_the_week_id
 * @property int $language_id
 * @property string $display_name
 * @property string $description
 * @property string $abbreviation
 * @property int $version
 * @property int $active
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read DayOfTheWeek $dayOfTheWeek
 * @property-read Language $language
 * @method static Builder|DayOfTheWeekTranslation newModelQuery()
 * @method static Builder|DayOfTheWeekTranslation newQuery()
 * @method static Builder|DayOfTheWeekTranslation query()
 * @method static Builder|DayOfTheWeekTranslation whereAbbreviation($value)
 * @method static Builder|DayOfTheWeekTranslation whereActive($value)
 * @method static Builder|DayOfTheWeekTranslation whereCreatedAt($value)
 * @method static Builder|DayOfTheWeekTranslation whereDayOfTheWeekId($value)
 * @method static Builder|DayOfTheWeekTranslation whereDeletedAt($value)
 * @method static Builder|DayOfTheWeekTranslation whereDescription($value)
 * @method static Builder|DayOfTheWeekTranslation whereDisplayName($value)
 * @method static Builder|DayOfTheWeekTranslation whereId($value)
 * @method static Builder|DayOfTheWeekTranslation whereLanguageId($value)
 * @method static Builder|DayOfTheWeekTranslation whereUpdatedAt($value)
 * @method static Builder|DayOfTheWeekTranslation whereVersion($value)
 * @mixin Eloquent
 */
class DayOfTheWeekTranslation extends Model
{
    //================================================================================
    // Relations
    //================================================================================

    public function dayOfTheWeek()
    {
        return $this->belongsTo(DayOfTheWeek::class, 'day_of_the_week_id');
    }

    public function language()
    {
        return $this->hasOne(Language::class, 'id', 'language_id');
    }
}
