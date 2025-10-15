<?php

namespace App\Modules\Payments\Models;

use App\Helpers\LanguageHelper;
use App\Model;
use Eloquent;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;

/**
 * App\Modules\Payments\Models\Article
 *
 * @property int $id
 * @property float $base_value
 * @property int $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Modules\Payments\Models\ArticleExtraFee[] $extra_fees
 * @property-read int|null $extra_fees_count
 * @property-read \App\Modules\Payments\Models\ArticleMonthlyCharge $monthly_charges
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\Article newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\Article newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\Article query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\Article whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\Article whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\Article whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\Article whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\Article whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\Article whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\Article whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\Article whereValue($value)
 * @mixin \Eloquent
 * @property string|null $code
 * @property-read \App\Modules\Payments\Models\ArticleTranslation $translation
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Modules\Payments\Models\ArticleTranslation[] $translations
 * @property-read int|null $translations_count
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Query\Builder|\App\Modules\Payments\Models\Article onlyTrashed()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\Article whereBaseValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\Article whereCode($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Modules\Payments\Models\Article withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Modules\Payments\Models\Article withoutTrashed()
 * @property-read int|null $monthly_charges_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Modules\Payments\Models\Payment[] $payments
 * @property-read int|null $payments_count
 */
class Article extends Model
{
    use SoftDeletes;

    protected $table = 'articles';

    protected $fillable = [
        'code',
        'base_value',
        'deleted_by',
        'deleted_at',
        'code_reference_discipline',
        'created_at',
        'anoLectivo'
    ];

    public function extraFeesAsText()
    {
        // TODO: translations
        $feesString = "";

        $fees = $this->extra_fees;
        foreach ($fees as $fee) {
            if (!$fee->fee_percent) {
                $feesString = "Périodo para pagamento regular: <b>$fee->max_delay_days dias</b>.<br>";
            } else {
                $newValue = number_format((float)$this->base_value * (1 + ($fee->fee_percent / 100)), 2, '.', '');
                $feesString .= "Taxa de <b>$fee->fee_percent%</b> aplicada até <b>$fee->max_delay_days dias</b> de atraso ($newValue Kz).<br>";
            }
        }
        return $feesString;
    }

    //================================================================================
    // Translations
    //================================================================================

    public function translations()
    {
        return $this->hasMany(ArticleTranslation::class, 'article_id', 'id');
    }

    public function translation()
    {
        return $this->hasOne(ArticleTranslation::class, 'article_id', 'id');
    }

    public function currentTranslation()
    {
        return $this
            ->translation()
            ->whereActive(true)
            ->whereLanguageId(LanguageHelper::getCurrentLanguage());
    }

    //================================================================================
    // Relations
    //================================================================================

    public function monthly_charges()
    {
        return $this->hasMany(ArticleMonthlyCharge::class);
    }

    public function extra_fees()
    {
        return $this->hasMany(ArticleExtraFee::class)->orderBy('fee_percent');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    //Relacionamento para a classe DisciplineArticle
    public function arg()
    {
        return $this->hasMany(DisciplineArticle::class);
    }
}
