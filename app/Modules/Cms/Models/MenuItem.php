<?php

namespace App\Modules\Cms\Models;

use App\Helpers\LanguageHelper;
use App\Model;
use App\Modules\Users\Models\Permission;
use App\Modules\Users\Models\User;
use Eloquent;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Spatie\Permission\Traits\HasPermissions;

/**
 * App\Modules\Cms\Models\MenuItem
 *
 * @property int $id
 * @property string $code
 * @property int $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property int|null $parent_id
 * @property int $position
 * @property string $icon
 * @property string $external_link
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static \Illuminate\Database\Eloquent\Builder|MenuItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MenuItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MenuItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|MenuItem whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MenuItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MenuItem whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MenuItem whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MenuItem whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MenuItem whereExternalLink($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MenuItem whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MenuItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MenuItem whereParentId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MenuItem wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MenuItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MenuItem whereUpdatedBy($value)
 * @mixin Eloquent
 * @property int|null $menus_id
 * @property-read Collection|MenuItem[] $children
 * @property-read User $createdBy
 * @property-read User|null $deletedBy
 * @property-read Menu|null $menu
 * @property-read MenuItem|null $parent
 * @property-read Collection|Permission[] $permissions
 * @property-read MenuItemTranslation $translation
 * @property-read Collection|MenuItemTranslation[] $translations
 * @property-read User|null $updatedBy
 * @method static bool|null forceDelete()
 * @method static Builder|MenuItem onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|MenuItem permission($permissions)
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|MenuItem whereMenusId($value)
 * @method static Builder|MenuItem withTrashed()
 * @method static Builder|MenuItem withoutTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|MenuItem disableCache()
 * @method static \Illuminate\Database\Eloquent\Builder|MenuItem withCacheCooldownSeconds($seconds = null)
 * @property-read int|null $children_count
 * @property-read int|null $permissions_count
 * @property-read int|null $translations_count
 */
class MenuItem extends Model
{
    use SoftDeletes;
    use HasPermissions;
    use Cachable;

    protected $guard_name = 'web';

    protected $fillable = [
        'parent_id',
        'menus_id',
        'code',
        'position',
        'icon',
        'external_link',
        'created_by',
        'updated_by',
        'deleted_by',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    //================================================================================
    // Translations
    //================================================================================

    public function translations()
    {
        return $this->hasMany(MenuItemTranslation::class, 'menu_items_id', 'id');
    }

    public function translation()
    {
        return $this->hasOne(MenuItemTranslation::class, 'menu_items_id', 'id');
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

    public function menu()
    {
        return $this->belongsTo(Menu::class, 'menus_id');
    }

    public function parent()
    {
        return $this->belongsTo(__CLASS__, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(__CLASS__, 'parent_id');
    }

    public function childrenRecursive()
    {
//        return $this->children()->with([
//            'currentTranslation',
//        ]);
        return $this->hasMany(__CLASS__, 'parent_id')->with('childrenRecursive');
    }

    public static function tree($menu_id)
    {
        $items = self::with([
            'translation' => function ($q) {
                $q->whereActive(true)->whereLanguageId(LanguageHelper::getCurrentLanguage());
            },
            'permissions',
            'childrenRecursive' => function ($q) {
                $q->with([
                    'translation' => function ($q) {
                        $q->whereActive(true)->whereLanguageId(LanguageHelper::getCurrentLanguage());
                    }
                ]);
            },
        ])->whereMenusId($menu_id)->orderBy('position')->get();


        return static::buildTree($items);
    }

    private static function buildTree($elements, $parentId = null)
    {
        $branch = [];
        foreach ($elements as $element) {
            if ($element->parent_id === $parentId) {
                $children = static::buildTree($elements, $element->id);
                if ($children) {
                    $element->children = $children;
                }
                $branch[] = $element;
            }
        }
        return $branch;
    }

}
