<?php

namespace App\Modules\Users\Models;

use Illuminate\Database\Eloquent\Model;


class WhatsappForlearn extends Model
{
    // Se o nome da tabela não seguir padrão plural, indica-o explicitamente
    protected $table = 'whatsapp_forlearn';

    // Caso não tenhas colunas 'created_at' e 'updated_at' automáticas, desativa timestamps
    public $timestamps = true; // ou false, se não usares timestamps automáticos

    // Campos que podem ser atribuídos em massa
    protected $fillable = [
        'whatsapp_to',
        'whatsapp_body',
        'whatsapp_of_number',
        'created_by',
        'updated_by', // só se existir esta coluna
    ];

    // Relação com o utilizador que criou
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Relação com o utilizador que atualizou (se existir)
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
