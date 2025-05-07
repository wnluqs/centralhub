<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Terminal;

class TerminalController extends Controller
{
    public function terminalsByBranch(Request $request)
    {
        $branch = ucfirst(strtolower($request->branch)); // Normalize branch casing first

        $terminals = Terminal::where('branch', $branch)
            ->orderBy('id')
            ->get(['id']);

        return response()->json($terminals);
    }

    public function search(Request $request)
    {
        $term = $request->get('q'); // 'q' matches the Select2 config

        $results = Terminal::where('id', 'LIKE', '%' . $term . '%')
            ->select('id') // only fetch id
            ->limit(20)
            ->get();

        return response()->json($results);
    }
}
