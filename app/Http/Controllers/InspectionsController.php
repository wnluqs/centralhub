<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inspection;
use App\Models\Terminal;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InspectionsExport;

class InspectionsController extends Controller
{
    // Updated index method to include filtering based on a "search" parameter.
    public function index(Request $request)
    {
        $search = $request->get('search');

        $inspections = Inspection::with('terminal')
            ->when($search, function ($query, $search) {
                return $query->where('terminal_id', 'like', "%{$search}%");
            })
            ->orderBy('created_at', 'desc')  // Order by creation timestamp, newest first
            ->get();

        return view('departments.technical.inspection.index', compact('inspections'));
    }

    public function create()
    {
        $statusOptions = ['Complete' => 'Complete', 'Failed' => 'Failed', 'Almost' => 'Almost'];
        $terminals = Terminal::all(); // Fetch all terminals from the database

        // Define spare parts options
        $spareParts = [
            'Broken Meter' => 'Broken Meter',
            'Receipt Output Malfunction' => 'Receipt Output Malfunction',
            'Buttons Malfunction' => 'Buttons Malfunction',
            'Paper Jam' => 'Paper Jam',
            'Screen Damage' => 'Screen Damage'
        ];

        $roads = ['Jalan Himalaya', 'Jalan Ampang', 'Jalan Bukit Tinggi', 'Jalan Starlight'];
        $zones = ['Kuala Penyu', 'Kuala Lipis', 'Maran', 'Raub', 'Kampar', 'Beaufort']; // or fetch from DB if needed

        // Define technician names as a dropdown list
        $technicians = ['Adam', 'James', 'Phill', 'Danish', 'Hannah', 'Dwayne'];

        return view('departments.technical.inspection.create', compact('statusOptions', 'terminals', 'spareParts', 'roads', 'zones', 'technicians'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'terminal_id'     => 'required|exists:terminals,id',
            'zone'            => 'required|string',
            'road'            => 'required|string',
            'spare_part_1'    => 'nullable|string',
            'spare_part_2'    => 'nullable|string',
            'spare_part_3'    => 'nullable|string',
            'status'          => 'required|in:Complete,Failed,Almost',
            'technician_name' => 'required|string',
            'photos'          => 'nullable|file|mimes:jpeg,png,jpg,mp4,mov,avi|max:20480',
            'video'           => 'nullable|file|mimes:mp4,mov,avi|max:20480'
        ]);

        // Process file uploads if present.
        if ($request->hasFile('photos')) {
            $validated['photos'] = $request->file('photos')->store('inspection_photos', 'public');
        }
        if ($request->hasFile('video')) {
            $validated['video'] = $request->file('video')->store('inspection_videos', 'public');
        }

        // Create the inspection record in the database.
        Inspection::create($validated);

        // Return a JSON response if the request expects JSON, else do a redirect.
        if ($request->wantsJson()) {
            return response()->json(['message' => 'Inspection created successfully!'], 200);
        }
        return redirect()->route('inspections.index')->with('success', 'Inspection created successfully!');
    }

    public function edit($id)
    {
        $inspection = Inspection::findOrFail($id);
        $statusOptions = ['Complete' => 'Complete', 'Failed' => 'Failed', 'Almost' => 'Almost'];
        return view('inspection.edit', compact('inspection', 'statusOptions'));
    }

    public function update(Request $request, $id)
    {
        $inspection = Inspection::findOrFail($id);
        $validated = $request->validate([
            'terminal_id'     => 'required|exists:terminals,id',
            'zone'            => 'required|string',
            'road'            => 'required|string',
            'spare_part_1'    => 'nullable|string',
            'spare_part_2'    => 'nullable|string',
            'spare_part_3'    => 'nullable|string',
            'status'          => 'required|in:Complete,Failed,Almost',
            'technician_name' => 'required|string',
            'notes'           => 'nullable|string',
            // IMPORTANT: Change from 'image' to 'file', and allow video formats
            // Also note 'max:20480' = 20 MB in kilobytes. Increase/decrease as needed.
            'photos'          => 'nullable|file|mimes:jpeg,png,jpg,mp4,mov,avi|max:20480'
        ]);
        if ($request->hasFile('photos')) {
            $validated['photos'] = $request->file('photos')->store('inspection_photos', 'public');
        }
        $inspection->update($validated);
        return redirect()->route('inspections.index')->with('success', 'Inspection updated successfully!');
    }

    public function destroy($id)
    {
        $inspection = Inspection::findOrFail($id);
        $inspection->delete();
        return redirect()->route('inspections.index')->with('success', 'Inspection created successfully!');
    }

    // Export to CSV using Laravel Excel package
    public function exportCsv(Request $request)
    {
        $search = $request->get('search');
        return Excel::download(new InspectionsExport($search), 'inspections.csv', \Maatwebsite\Excel\Excel::CSV);
    }

    // Export to Excel using Laravel Excel package
    public function exportExcel(Request $request)
    {
        $search = $request->get('search');
        return Excel::download(new InspectionsExport($search), 'inspections.xlsx');
    }

    public function apiIndex()
    {
        $inspections = Inspection::with('terminal')
            ->orderBy('created_at', 'desc')
            ->get();

        // Return a JSON response instead of a Blade view:
        return response()->json($inspections);
    }
}
