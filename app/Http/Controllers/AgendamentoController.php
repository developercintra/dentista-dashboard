<?php

namespace App\Http\Controllers;

use App\Models\Agendamento;
use App\Models\Paciente;
use App\Models\Servicos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AgendamentoController extends Controller
{
    public function index(Request $request)
    {
        $q = Agendamento::where('user_id', Auth::id());

        if ($request->filled('status')) {
            $q->where('status', $request->status);
        }

        if ($request->filled('paciente_id')) {
            $q->where('paciente_id', $request->paciente_id);
        }

        if ($request->periodo === 'dia') {
            $q->whereDate('data', now());
        } elseif ($request->periodo === 'semana') {
            $q->whereBetween('data', [now()->startOfWeek(), now()->endOfWeek()]);
        } elseif ($request->periodo === 'mes') {
            $q->whereMonth('data', now()->month)->whereYear('data', now()->year);
        } elseif ($request->periodo === 'ano') {
            $q->whereYear('data', now()->year);
        }

        $agendamentos = $q->orderBy('data')->orderBy('hora')->get();

        return response()->json($agendamentos);
    }

    public function store(Request $request)
    {
        $request->validate([
            'paciente_id' => 'required|integer|exists:pacientes,id',
            'servico_id'  => 'required|integer|exists:servicos,id',
            'data'        => 'required|date',
            'hora'        => 'required|date_format:H:i',
            'valor'       => 'nullable|numeric|min:0',
            'status'      => 'nullable|in:ativo,cancelado',
        ]);

        $paciente = Paciente::where('user_id', Auth::id())->findOrFail($request->paciente_id);
        $servico  = Servicos::where('user_id', Auth::id())->findOrFail($request->servico_id);

        $valor = $request->filled('valor') ? $request->valor : $servico->valor;

        $conflito = Agendamento::where('user_id', Auth::id())
            ->whereDate('data', $request->data)
            ->where('hora', $request->hora)
            ->where('status', 'ativo')
            ->exists();

        if ($conflito) {
            return response()->json([
                'message' => 'Horário indisponível: já existe agendamento ativo para essa data e hora.'
            ], 422);
        }

        $agendamento = Agendamento::create([
            'user_id'     => Auth::id(),
            'paciente_id' => $paciente->id,
            'servico_id'  => $servico->id,
            'data'        => $request->data,
            'hora'        => $request->hora,
            'valor'       => $valor,
            'status'      => $request->input('status', 'ativo'),
        ]);

        return response()->json($agendamento, 201);
    }

    public function show($id)
    {
        $agendamento = Agendamento::where('user_id', Auth::id())->findOrFail($id);
        return response()->json($agendamento);
    }

    public function update(Request $request, $id)
    {
        $agendamento = Agendamento::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'paciente_id' => 'required|integer|exists:pacientes,id',
            'servico_id'  => 'required|integer|exists:servicos,id',
            'data'        => 'required|date',
            'hora'        => 'required|date_format:H:i',
            'valor'       => 'nullable|numeric|min:0',
            'status'      => 'nullable|in:ativo,cancelado',
        ]);

        Paciente::where('user_id', Auth::id())->findOrFail($request->paciente_id);
        $servico = Servicos::where('user_id', Auth::id())->findOrFail($request->servico_id);

        $mudouDataHora = $agendamento->data !== $request->data || $agendamento->hora !== $request->hora;

        if ($mudouDataHora) {
            $conflito = Agendamento::where('user_id', Auth::id())
                ->whereDate('data', $request->data)
                ->where('hora', $request->hora)
                ->where('status', 'ativo')
                ->where('id', '!=', $agendamento->id)
                ->exists();

            if ($conflito) {
                return response()->json([
                    'message' => 'Horário indisponível: já existe agendamento ativo para essa data e hora.'
                ], 422);
            }
        }

        $valor = $request->filled('valor') ? $request->valor : $servico->valor;

        $agendamento->update([
            'paciente_id' => $request->paciente_id,
            'servico_id'  => $request->servico_id,
            'data'        => $request->data,
            'hora'        => $request->hora,
            'valor'       => $valor,
            'status'      => $request->input('status', $agendamento->status),
        ]);

        return response()->json($agendamento);
    }

    public function destroy($id)
    {
        $agendamento = Agendamento::where('user_id', Auth::id())->findOrFail($id);
        $agendamento->delete();

        return response()->json(['message' => 'Agendamento removido com sucesso']);
    }
}
