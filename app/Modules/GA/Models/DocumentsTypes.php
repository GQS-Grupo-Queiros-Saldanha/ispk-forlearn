<?php

namespace App\Modules\GA\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\User;

class DocumentsTypes extends Model
{
    use SoftDeletes;

    protected $table = "documentation_type";

    protected $fillable = [
        'id',
        'name',
        'observation',
        'created_by',
        'updated_by',
        'deleted_by'
    ];
    

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }


}
