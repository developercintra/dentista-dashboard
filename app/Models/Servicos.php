<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Servicos extends Model
{
    protected $fillable = ['user_id', 'nome', 'valor'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function agendamentos() {
        return $this->hasMany(Agendamento::class);
    }
}
