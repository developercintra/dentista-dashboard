<?php

namespace App\Http\Controllers;

use App\Models\Servicos; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServicosController extends Controller
{
    public function index()
    {
        $servicos = Servicos::where('user_id', Auth::id())
            ->orderBy('nome')
            ->get();

        return response()->json($servicos);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome'  => 'required|string|max:255',
            'valor' => 'required|numeric|min:0',
            'descricao' => 'nullable|string|max:1000',
        ]);

        $servico = Servicos::create([
            'user_id'   => Auth::id(),
            'nome'      => $request->nome,
            'valor'     => $request->valor,
            'descricao' => $request->descricao,
        ]);

        return response()->json($servico, 201);
    }

    public function show($id)
    {
        $servico = Servicos::where('user_id', Auth::id())->findOrFail($id);
        return response()->json($servico);
    }

    public function update(Request $request, $id)
    {
        $servico = Servicos::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'nome'  => 'required|string|max:255',
            'valor' => 'required|numeric|min:0',
            'descricao' => 'nullable|string|max:1000',
        ]);

        $servico->update($request->only('nome','valor','descricao'));

        return response()->json($servico);
    }

    public function destroy($id)
    {
        $servico = Servicos::where('user_id', Auth::id())->findOrFail($id);
        $servico->delete();

        return response()->json(['message' => 'Serviço removido com sucesso']);
    }
}
