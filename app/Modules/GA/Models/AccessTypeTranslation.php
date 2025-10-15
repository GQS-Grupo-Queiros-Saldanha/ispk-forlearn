<?php

namespace App\Modules\GA\Models;

use App\Model;
use App\Modules\Cms\Models\Language;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;


/**
 * App\Modules\GA\Models\AccessTypeTranslation
 *
 * @property int $id
 * @property int $access_type_id
 * @property int $language_id
 * @property string|null $display_name
 * @property string|null $description
 * @property int $version
 * @property int|null $active
 * @property string|null $abbreviation
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read AccessType $accessType
 * @property-read Language $language
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|AccessTypeTranslation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AccessTypeTranslation newQuery()
 * @method static Builder|AccessTypeTranslation onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|AccessTypeTranslation query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|AccessTypeTranslation whereAbbreviation($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccessTypeTranslation whereAccessTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccessTypeTranslation whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccessTypeTranslation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccessTypeTranslation whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccessTypeTranslation whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccessTypeTranslation whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccessTypeTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccessTypeTranslation whereLanguageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccessTypeTranslation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AccessTypeTranslation whereVersion($value)
 * @method static Builder|AccessTypeTranslation withTrashed()
 * @method static Builder|AccessTypeTranslation withoutTrashed()
 * @mixin Eloquent
 * @method static \Illuminate\Database\Eloquent\Builder|AccessTypeTranslation disableCache()
 * @method static \Illuminate\Database\Eloquent\Builder|AccessTypeTranslation withCacheCooldownSeconds($seconds = null)
 */
class AccessTypeTranslation extends Model
{
    use SoftDeletes;
    //use Cachable;

    protected $table = 'access_type_translations';

    protected $fillable = [
        'access_type_id',
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

    public function accessType()
    {
        return $this->belongsTo(AccessType::class, 'acr_id');
    }

    public function language()
    {
        return $this->hasOne(Language::class, 'id', 'language_id');
    }
}
