<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PacienteController extends Controller
{
    public function index()
    {
        $pacientes = Paciente::where('user_id', Auth::id())->get();
        return response()->json($pacientes);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255',
            'cpf' => 'required|string|max:14|unique:pacientes,cpf',
            'telefone' => 'nullable|string|max:20',
        ]);

        $paciente = Paciente::create([
            'user_id' => Auth::id(),
            'nome' => $request->nome,
            'cpf' => $request->cpf,
            'telefone' => $request->telefone,
        ]);

        return response()->json($paciente, 201);
    }

    public function show($id)
    {
        $paciente = Paciente::where('user_id', Auth::id())->findOrFail($id);
        return response()->json($paciente);
    }

    // 📍 Atualizar paciente
    public function update(Request $request, $id)
    {
        $paciente = Paciente::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'nome' => 'required|string|max:255',
            'cpf' => "required|string|max:14|unique:pacientes,cpf,$id",
            'telefone' => 'nullable|string|max:20',
        ]);

        $paciente->update($request->all());

        return response()->json($paciente);
    }


    public function destroy($id)
    {
        $paciente = Paciente::where('user_id', Auth::id())->findOrFail($id);
        $paciente->delete();

        return response()->json(['message' => 'Paciente removido com sucesso']);
    }
}
