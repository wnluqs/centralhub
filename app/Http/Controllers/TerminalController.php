<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Terminal;

class TerminalController extends Controller
{
    public function terminalsByBranch(Request $request)
    {
        $terminals = Terminal::whereRaw('BINARY `branch` = ?', [$request->branch])
            ->orderBy('id')
            ->get(['id']);

        return response()->json($terminals);
    }

    public function search(Request $request)
    {
        $term = $request->get('q'); // 'q' matches the Select2 config

        $results = Terminal::where('id', 'LIKE', '%' . $term . '%')
            ->select('id') // only fetch id
            ->limit(30)
            ->get();

        return response()->json($results);
    }
}
