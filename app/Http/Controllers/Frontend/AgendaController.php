<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Agenda;
use Illuminate\View\View;

class AgendaController extends Controller
{
    public function index(): View
    {
        $agendas = Agenda::query()
            ->where('is_published', true)
            ->orderBy('start_date')
            ->paginate(10);

        return view('agendas.index', compact('agendas'));
    }
}
