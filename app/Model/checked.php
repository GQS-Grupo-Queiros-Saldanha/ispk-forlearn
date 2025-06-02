<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class WhatsappChecked extends Model
{
    use SoftDeletes;

    protected $table = "users";

    protected $fillable = [
        'id',
        'user_whatsapp'
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
