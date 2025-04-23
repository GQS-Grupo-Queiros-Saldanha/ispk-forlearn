<?php


namespace App\Modules\GA\Models;

use App\Model;
use App\Modules\Users\Models\Parameter;
use App\Modules\Users\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;
 


class Lb_publisher extends Model{

    use SoftDeletes;

    protected $fillable = [
        'id',
        'name',
        'address',
        'district',
        'city',
        'created_at',
        'deleted_at'
    ];
}



?>