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
        $query = TerminalParking::query();

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

        // ✅ Eager load related terminal info
        $terminals = $query->get(); // instead of paginate(10)

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
        return Excel::download(new TerminalParkingExport($filteredTerminals), 'terminal_parking.xlsx');
    }

    public function editLocation($id)
    {
        $terminal = TerminalParking::findOrFail($id);
        return view('departments.technical.parking.edit-location', compact('terminal'));
    }

    public function updateLocation(Request $request, $id)
    {
        $request->validate([
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180'
        ]);

        $terminal = TerminalParking::findOrFail($id);
        $terminal->latitude = $request->latitude;
        $terminal->longitude = $request->longitude;
        $terminal->save();

        return redirect()->route('technical.terminal_parking')->with('success', 'Location updated.');
    }
}
