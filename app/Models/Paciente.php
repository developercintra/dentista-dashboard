<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Paciente extends Model
{
    use HasFactory;

    protected $fillable = [
        'id',
        'nome',
        'telefone',
        'email'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function agendamentos()
    {
        return $this->hasMany(Agendamento::class);
    }
}
