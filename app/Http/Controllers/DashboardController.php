<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Agendamento;

class DashboardController extends Controller
{
   public function index()
   {
      $user = auth()->user();

      $totalPacientes = $user->pacientes->count();
      $totalAgendamentos = $user->agendamentos->count();
      $faturamentoDiario  = Agendamento::whereDate('data', now())->sum('valor');
      $faturamentoSemanal = Agendamento::whereBetween('data', [now()->startOfWeek(), now()->endOfWeek()])->sum('valor');
      $faturamentoMensal  = Agendamento::whereMonth('data', now()->month)->whereYear('data', now()->year)->sum('valor');
      $faturamentoAnual   = Agendamento::whereYear('data', now()->year)->sum('valor');



      return view('dashboard.index', compact(
         'user',
         'totalPacientes',
         'totalAgendamentos',
         'faturamentoDiario',
         'faturamentoSemanal',
         'faturamentoMensal',
         'faturamentoAnual'
      ));
   }
}
