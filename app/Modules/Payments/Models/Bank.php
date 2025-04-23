<?php

namespace App\Modules\Payments\Models;

use App\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Modules\Payments\Models\Bank
 *
 * @property int $id
 * @property string|null $code
 * @property string $display_name
 * @property string|null $account_number
 * @property string|null $iban
 * @property int $created_by
 * @property int|null $updated_by
 * @property int|null $deleted_by
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @method static bool|null forceDelete()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\Bank newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\Bank newQuery()
 * @method static \Illuminate\Database\Query\Builder|\App\Modules\Payments\Models\Bank onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\Bank query()
 * @method static bool|null restore()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\Bank whereAccountNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\Bank whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\Bank whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\Bank whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\Bank whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\Bank whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\Bank whereDisplayName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\Bank whereIban($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\Bank whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\Bank whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Modules\Payments\Models\Bank whereUpdatedBy($value)
 * @method static \Illuminate\Database\Query\Builder|\App\Modules\Payments\Models\Bank withTrashed()
 * @method static \Illuminate\Database\Query\Builder|\App\Modules\Payments\Models\Bank withoutTrashed()
 * @mixin \Eloquent
 */
class Bank extends Model
{
    use SoftDeletes;

    protected $table = 'banks';

    protected $fillable = [
        'code',
        'display_name',
        'account_number',
        'iban',
        'deleted_by',
        'type_conta_entidade'
    ];
}
