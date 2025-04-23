<?php

namespace App\Modules\GA\Models;

use App\Model;
use App\Modules\Cms\Models\Language;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;

/**
 * App\Modules\GA\Models\DisciplineClassTranslation
 *
 * @property-read DisciplineClass $disciplineClass
 * @property-read Language $language
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineClassTranslation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineClassTranslation newQuery()
 * @method static Builder|DisciplineClassTranslation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineClassTranslation query()
 * @method static bool|null restore()
 * @method static Builder|DisciplineClassTranslation withTrashed()
 * @method static Builder|DisciplineClassTranslation withoutTrashed()
 * @mixin Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineClassTranslation disableCache()
 * @method static \Illuminate\Database\Eloquent\Builder|DisciplineClassTranslation withCacheCooldownSeconds($seconds = null)
 */
class DisciplineClassTranslation extends Model
{
    use SoftDeletes;
    //use Cachable;

    protected $table = 'discipline_classes_translations';

    protected $fillable = [
        'discipline_class_id',
        'language_id',
        'display_name',
        'description',
        'version',
        'active',
        'abbreviation',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    //================================================================================
    // Relations
    //================================================================================

    public function disciplineClass()
    {
        return $this->belongsTo(DisciplineClass::class, 'discipline_class_id');
    }

    public function language()
    {
        return $this->hasOne(Language::class, 'id', 'language_id');
    }
}
