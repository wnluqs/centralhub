<?php

namespace App\Http\Controllers;

use App\Models\TerminalParking;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\TerminalParkingExport;


class TerminalParkingController extends Controller
{
    public function index(Request $request)
    {
        $query = TerminalParking::query(); // Create a query builder instance

        // Filter by general search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('location', 'LIKE', '%' . $search . '%')
                ->orWhere('zone_code', 'LIKE', '%' . $search . '%')
                ->orWhere('number', 'LIKE', '%' . $search . '%'); // Changed from 'terminal_number' to 'number'
        }

        // Filter by terminal number
        if ($request->filled('terminal_number')) {
            $query->where('number', 'LIKE', '%' . $request->input('terminal_number') . '%'); // Changed from 'terminal_number' to 'number'
        }
        // Filter by location
        if ($request->filled('location')) {
            $query->where('location', 'LIKE', '%' . $request->input('location') . '%');
        }

        // Filter by zone code
        if ($request->filled('zone_code')) {
            $query->where('zone_code', 'LIKE', $request->input('zone_code') . '%');
        }
        // Fetch filtered results with pagination
        $terminals = $query->paginate(10);

        // Return the view with filtered terminals
        return view('departments.technical.parking.index', compact('terminals'));
    }

    public function exportCSV(Request $request)
    {
        $query = TerminalParking::query();

        // Apply the same filtering logic as used for the index view:

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('location', 'LIKE', '%' . $search . '%')
                    ->orWhere('zone_code', 'LIKE', '%' . $search . '%')
                    ->orWhere('number', 'LIKE', '%' . $search . '%');
            });
        }

        if ($request->filled('terminal_number')) {
            $query->where('number', 'LIKE', '%' . $request->input('terminal_number') . '%');
        }

        if ($request->filled('location')) {
            $query->where('location', 'LIKE', '%' . $request->input('location') . '%');
        }

        if ($request->filled('zone_code')) {
            $query->where('zone_code', 'LIKE', $request->input('zone_code') . '%');
        }

        // Retrieve all filtered results (without pagination)
        $filteredTerminals = $query->get();

        // Pass the filtered collection to the export class's constructor:
        return Excel::download(new TerminalParkingExport($filteredTerminals), 'terminal_parking.csv');
    }


    public function exportExcel(Request $request)
    {
        $query = TerminalParking::query();

        // Apply the same filtering logic as used for the index view:

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('location', 'LIKE', '%' . $search . '%')
                    ->orWhere('zone_code', 'LIKE', '%' . $search . '%')
                    ->orWhere('number', 'LIKE', '%' . $search . '%');
            });
        }

        if ($request->filled('terminal_number')) {
            $query->where('number', 'LIKE', '%' . $request->input('terminal_number') . '%');
        }

        if ($request->filled('location')) {
            $query->where('location', 'LIKE', '%' . $request->input('location') . '%');
        }

        if ($request->filled('zone_code')) {
            $query->where('zone_code', 'LIKE', $request->input('zone_code') . '%');
        }

        // Retrieve all filtered results (without pagination)
        $filteredTerminals = $query->get();

        // Pass the filtered collection to the export class's constructor:
        return Excel::download(new TerminalParkingExport($filteredTerminals), 'terminal_parking.csv');
    }
}
