<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Paciente;
use App\Models\Servicos;

class Agendamento extends Model
{
    protected $fillable = ['user_id', 'paciente_id', 'servico_id', 'data_hora', 'valor', 'status'];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function paciente() {
        return $this->belongsTo(Paciente::class);
    }

    public function servico() {
        return $this->belongsTo(Servicos::class);
    }
}

