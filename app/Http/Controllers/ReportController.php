<?php

namespace App\Http\Controllers;

use App\Support\DailyProfitCalculator;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request, DailyProfitCalculator $calculator): View
    {
        abort_unless($request->user()?->can('reports.view'), 403);

        $from = $request->date('from') ?? now()->subDays(13)->startOfDay();
        $to = $request->date('to') ?? today();

        if ($from->gt($to)) {
            [$from, $to] = [$to, $from];
        }

        $rows = $calculator->forRange($from, $to);

        return view('reports.index', [
            'from' => $from->toDateString(),
            'to' => $to->toDateString(),
            'rows' => $rows,
            'totals' => [
                'cash_collected' => $rows->sum('cash_collected'),
                'expenses' => $rows->sum('expenses'),
                'profit' => $rows->sum('profit'),
            ],
        ]);
    }
}
