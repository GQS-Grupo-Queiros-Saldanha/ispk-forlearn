<?php

namespace App\Modules\GA\Models;

use App\Model;
use App\Modules\Cms\Models\Language;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Modules\GA\Models\BuildingTranslation
 *
 * @property int $id
 * @property int $building_id
 * @property int $language_id
 * @property string $display_name
 * @property string $description
 * @property string $abbreviation
 * @property int $version
 * @property int $active
 * @property Carbon $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read Building $building
 * @property-read Language $language
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingTranslation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingTranslation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingTranslation query()
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingTranslation whereAbbreviation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingTranslation whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingTranslation whereBuildingId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingTranslation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingTranslation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingTranslation whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingTranslation whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingTranslation whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingTranslation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|BuildingTranslation whereVersion($value)
 * @mixin Eloquent
 * @method static bool|null forceDelete()
 * @method static Builder|BuildingTranslation onlyTrashed()
 * @method static bool|null restore()
 * @method static Builder|BuildingTranslation withTrashed()
 * @method static Builder|BuildingTranslation withoutTrashed()
 */
class BuildingTranslation extends Model
{
    use SoftDeletes;

    //================================================================================
    // Relations
    //================================================================================

    public function building()
    {
        return $this->belongsTo(Building::class);
    }

    public function language()
    {
        return $this->belongsTo(Language::class);
    }

}
