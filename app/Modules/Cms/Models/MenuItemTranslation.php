<?php

namespace App\Modules\Cms\Models;

use App\Model;
use App\Modules\GA\Models\Degree;
use App\Modules\GA\Models\DegreeTranslation;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Modules\GA\Models\DegreeTranslation
 *
 * @property int $id
 * @property int $language_id
 * @property int $degrees_id
 * @property string|null $display_name
 * @property string|null $description
 * @property int $version
 * @property int|null $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read Degree $degree
 * @property-read Language $language
 * @method static bool|null forceDelete()
 * @method static Builder|DegreeTranslation newModelQuery()
 * @method static Builder|DegreeTranslation newQuery()
 * @method static \Illuminate\Database\Query\Builder|DegreeTranslation onlyTrashed()
 * @method static Builder|DegreeTranslation query()
 * @method static bool|null restore()
 * @method static Builder|DegreeTranslation whereActive($value)
 * @method static Builder|DegreeTranslation whereCreatedAt($value)
 * @method static Builder|DegreeTranslation whereDegreesId($value)
 * @method static Builder|DegreeTranslation whereDeletedAt($value)
 * @method static Builder|DegreeTranslation whereDescription($value)
 * @method static Builder|DegreeTranslation whereDisplayName($value)
 * @method static Builder|DegreeTranslation whereId($value)
 * @method static Builder|DegreeTranslation whereLanguageId($value)
 * @method static Builder|DegreeTranslation whereUpdatedAt($value)
 * @method static Builder|DegreeTranslation whereVersion($value)
 * @method static \Illuminate\Database\Query\Builder|DegreeTranslation withTrashed()
 * @method static \Illuminate\Database\Query\Builder|DegreeTranslation withoutTrashed()
 * @mixin Eloquent
 * @property int $menus_id
 * @property-read Menu $menu
 * @method static Builder|MenuTranslation whereMenusId($value)
 * @property int $menu_items_id
 * @property-read Menu $menuItem
 * @method static Builder|MenuItemTranslation whereMenuItemsId($value)
 * @method static Builder|MenuItemTranslation disableCache()
 * @method static Builder|MenuItemTranslation withCacheCooldownSeconds($seconds = null)
 */
class MenuItemTranslation extends Model
{
    use SoftDeletes;
    //use Cachable;

    protected $table = 'menu_item_translations';

    protected $fillable = [
        'menu_items_id',
        'language_id',
        'display_name',
        'description',
        'version',
        'active',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    //================================================================================
    // Relations
    //================================================================================

    public function menuItem()
    {
        return $this->belongsTo(Menu::class, 'menu_items_id');
    }

    public function language()
    {
        return $this->hasOne(Language::class, 'id', 'language_id');
    }
}
