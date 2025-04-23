<?php

namespace App\Modules\GA\Models;

use App\Model;
use App\Modules\Cms\Models\Language;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Modules\GA\Models\StudyPlanEditionDisciplineModuleTranslation
 *
 * @property int $id
 * @property int $spe_has_disciplines_has_module_id
 * @property int $language_id
 * @property string $display_name
 * @property string $description
 * @property int $active
 * @property int $version
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property string $deleted_at
 * @property-read Language $language
 * @method static Builder|StudyPlanEditionDisciplineModuleTranslation newModelQuery()
 * @method static Builder|StudyPlanEditionDisciplineModuleTranslation newQuery()
 * @method static Builder|StudyPlanEditionDisciplineModuleTranslation query()
 * @method static Builder|StudyPlanEditionDisciplineModuleTranslation whereActive($value)
 * @method static Builder|StudyPlanEditionDisciplineModuleTranslation whereCreatedAt($value)
 * @method static Builder|StudyPlanEditionDisciplineModuleTranslation whereDeletedAt($value)
 * @method static Builder|StudyPlanEditionDisciplineModuleTranslation whereDescription($value)
 * @method static Builder|StudyPlanEditionDisciplineModuleTranslation whereDisplayName($value)
 * @method static Builder|StudyPlanEditionDisciplineModuleTranslation whereId($value)
 * @method static Builder|StudyPlanEditionDisciplineModuleTranslation whereLanguageId($value)
 * @method static Builder|StudyPlanEditionDisciplineModuleTranslation whereSpeHasDisciplinesHasModuleId($value)
 * @method static Builder|StudyPlanEditionDisciplineModuleTranslation whereUpdatedAt($value)
 * @method static Builder|StudyPlanEditionDisciplineModuleTranslation whereVersion($value)
 * @mixin Eloquent
 */
class StudyPlanEditionDisciplineModuleTranslation extends Model
{
    protected $table = 'spe_has_disciplines_has_module_translations';

    protected $fillable = [
        'spe_has_discipline_has_module_id',
        'language_id',
        'display_name',
        'description',
        'active',
        'version',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    //================================================================================
    // Translations
    //================================================================================

    public function language()
    {
        return $this->hasOne(Language::class, 'id', 'language_id');
    }
}
