<?php

namespace App\Modules\GA\Models;

use App\Helpers\LanguageHelper;
use App\Model;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * App\Modules\GA\Models\StudyPlanEditionDisciplineModule
 *
 * @property int $id
 * @property int $spe_has_disciplines_id
 * @property int $created_by
 * @property int $updated_by
 * @property-read StudyPlanEditionDisciplineModuleTranslation $translation
 * @property-read Collection|StudyPlanEditionDisciplineModuleTranslation[] $translations
 * @property-read int|null $translations_count
 * @method static Builder|StudyPlanEditionDisciplineModule newModelQuery()
 * @method static Builder|StudyPlanEditionDisciplineModule newQuery()
 * @method static Builder|StudyPlanEditionDisciplineModule query()
 * @method static Builder|StudyPlanEditionDisciplineModule whereCreatedBy($value)
 * @method static Builder|StudyPlanEditionDisciplineModule whereId($value)
 * @method static Builder|StudyPlanEditionDisciplineModule whereSpeHasDisciplinesId($value)
 * @method static Builder|StudyPlanEditionDisciplineModule whereUpdatedBy($value)
 * @mixin Eloquent
 */
class StudyPlanEditionDisciplineModule extends Model
{
    public $timestamps = false;

    protected $table = 'spe_has_disciplines_has_modules';

    protected $fillable = [
        'spe_has_disciplines_id',
        'created_by',
        'updated_by'
    ];

    //================================================================================
    // Translations
    //================================================================================

    public function translations()
    {
        return $this->hasMany(StudyPlanEditionDisciplineModuleTranslation::class, 'spe_has_disciplines_has_module_id', 'id');
    }

    public function translation()
    {
        return $this->hasOne(StudyPlanEditionDisciplineModuleTranslation::class, 'spe_has_disciplines_has_module_id', 'id');
    }

    public function currentTranslation()
    {
        return $this
            ->translation()
            ->whereActive(true)
            ->whereLanguageId(LanguageHelper::getCurrentLanguage());
    }

}
