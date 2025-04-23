<?php

namespace App\Modules\Users\Models;

use App\Model;
use App\Modules\Cms\Models\Language;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Modules\Users\Models\ProfessionTranslation
 *
 * @property-read Language $language
 * @property-read Profession $profession
 * @method static bool|null forceDelete()
 * @method static Builder|ProfessionTranslation newModelQuery()
 * @method static Builder|ProfessionTranslation newQuery()
 * @method static \Illuminate\Database\Query\Builder|ProfessionTranslation onlyTrashed()
 * @method static Builder|ProfessionTranslation query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Query\Builder|ProfessionTranslation withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ProfessionTranslation withoutTrashed()
 * @mixin Eloquent
 * @property-read User $createdBy
 * @property-read User $deletedBy
 * @property-read User $updatedBy
 * @property int $id
 * @property int $professions_id
 * @property int $language_id
 * @property string|null $display_name
 * @property string|null $description
 * @property int $version
 * @property int|null $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static Builder|ProfessionTranslation whereActive($value)
 * @method static Builder|ProfessionTranslation whereCreatedAt($value)
 * @method static Builder|ProfessionTranslation whereDeletedAt($value)
 * @method static Builder|ProfessionTranslation whereDescription($value)
 * @method static Builder|ProfessionTranslation whereDisplayName($value)
 * @method static Builder|ProfessionTranslation whereId($value)
 * @method static Builder|ProfessionTranslation whereLanguageId($value)
 * @method static Builder|ProfessionTranslation whereProfessionsId($value)
 * @method static Builder|ProfessionTranslation whereUpdatedAt($value)
 * @method static Builder|ProfessionTranslation whereVersion($value)
 * @method static Builder|ProfessionTranslation disableCache()
 * @method static Builder|ProfessionTranslation withCacheCooldownSeconds($seconds = null)
 */
class ProfessionTranslation extends Model
{
    use SoftDeletes;
    //use Cachable;

    protected $table = 'professions_translations';

    protected $fillable = [
        'professions_id',
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

    public function profession()
    {
        return $this->belongsTo(Profession::class, 'professions_id');
    }

    public function language()
    {
        return $this->hasOne(Language::class, 'id', 'language_id');
    }
}
