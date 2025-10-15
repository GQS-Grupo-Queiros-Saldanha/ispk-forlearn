<?php

namespace App\Modules\Users\Models;

use App\Model;
use App\Modules\Cms\Models\Language;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Modules\Users\Models\ProfessionalStateTranslation
 *
 * @property-read Language $language
 * @property-read ProfessionalState $professionalState
 * @method static bool|null forceDelete()
 * @method static Builder|ProfessionalStateTranslation newModelQuery()
 * @method static Builder|ProfessionalStateTranslation newQuery()
 * @method static \Illuminate\Database\Query\Builder|ProfessionalStateTranslation onlyTrashed()
 * @method static Builder|ProfessionalStateTranslation query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Query\Builder|ProfessionalStateTranslation withTrashed()
 * @method static \Illuminate\Database\Query\Builder|ProfessionalStateTranslation withoutTrashed()
 * @mixin Eloquent
 * @property int $id
 * @property int $professional_states_id
 * @property int $language_id
 * @property string|null $display_name
 * @property string|null $description
 * @property int $version
 * @property int|null $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static Builder|ProfessionalStateTranslation whereActive($value)
 * @method static Builder|ProfessionalStateTranslation whereCreatedAt($value)
 * @method static Builder|ProfessionalStateTranslation whereDeletedAt($value)
 * @method static Builder|ProfessionalStateTranslation whereDescription($value)
 * @method static Builder|ProfessionalStateTranslation whereDisplayName($value)
 * @method static Builder|ProfessionalStateTranslation whereId($value)
 * @method static Builder|ProfessionalStateTranslation whereLanguageId($value)
 * @method static Builder|ProfessionalStateTranslation whereProfessionalStatesId($value)
 * @method static Builder|ProfessionalStateTranslation whereUpdatedAt($value)
 * @method static Builder|ProfessionalStateTranslation whereVersion($value)
 * @method static Builder|ProfessionalStateTranslation disableCache()
 * @method static Builder|ProfessionalStateTranslation withCacheCooldownSeconds($seconds = null)
 */
class ProfessionalStateTranslation extends Model
{
    use SoftDeletes;
    //use Cachable;

    protected $table = 'professional_states_translations';

    protected $fillable = [
        'professional_states_id',
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
    // Creators
    //================================================================================

    public function professionalState()
    {
        return $this->belongsTo(ProfessionalState::class, 'professional_states_id');
    }

    public function language()
    {
        return $this->hasOne(Language::class, 'id', 'language_id');
    }
}
