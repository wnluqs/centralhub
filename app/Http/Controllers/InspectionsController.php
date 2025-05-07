<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inspection;
use App\Models\Terminal;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InspectionsExport;
use Carbon\Carbon;

class InspectionsController extends Controller
{
    // Updated index method to include filtering based on a "search" parameter.
    public function index(Request $request)
    {
        $query = Inspection::query();

        if ($request->filled('terminal_id')) {
            $query->where('terminal_id', 'LIKE', "%{$request->terminal_id}%");
        }
        if ($request->filled('zone')) {
            $query->where('zone', 'LIKE', "%{$request->zone}%");
        }
        if ($request->filled('road')) {
            $query->where('road', 'LIKE', "%{$request->road}%");
        }
        if ($request->filled('branch')) {
            $query->where('branch', 'LIKE', "%{$request->branch}%");
        }
        if ($request->filled('status')) {
            $query->where('status', 'LIKE', "%{$request->status}%");
        }
        if ($request->filled('technician_name')) {
            $query->where('technician_name', 'LIKE', "%{$request->technician_name}%");
        }
        // Then in your filtering
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $start = Carbon::parse($request->start_date)->startOfDay(); // 00:00:00
            $end = Carbon::parse($request->end_date)->endOfDay();       // 23:59:59

            $query->whereBetween('created_at', [$start, $end]);
        }
        if ($request->filled('keypad_grade')) {
            $query->where('keypad_grade', 'LIKE', "%{$request->keypad_grade}%");
        }

        // Sorting
        $sortField = $request->input('sort_field', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortField, $sortOrder);

        $inspections = $query->paginate(10); // 10 records per page

        return view('departments.technical.inspection.index', compact('inspections'));
    }

    public function create()
    {
        $statusOptions = ['Complete' => 'Complete', 'Failed' => 'Failed', 'Almost' => 'Almost'];
        $terminals = Terminal::all(); // ✅ Required for terminal dropdown
        $spareParts = [
            'Broken Meter',
            'Receipt Output Malfunction',
            'Buttons Malfunction',
            'Paper Jam',
            'Screen Damage'
        ];

        $roads = ['Jalan Himalaya', 'Jalan Ampang', 'Jalan Bukit Tinggi', 'Jalan Starlight'];
        $zones = ['Kuala Penyu', 'Kuala Lipis', 'Maran', 'Raub', 'Kampar', 'Beaufort'];
        $technicians = ['Adam', 'James', 'Phill', 'Danish', 'Hannah', 'Dwayne'];

        return view('departments.technical.inspection.create', compact(
            'statusOptions',
            'terminals',
            'spareParts',
            'roads',
            'zones',
            'technicians'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'terminal_id'     => 'required|exists:terminals,id',
            'zone'            => 'required|string',
            'road'            => 'required|string',
            'spare_parts'     => 'nullable|array',
            'spare_parts.*'   => 'string',
            'status'          => 'required|in:Complete,Failed,Almost',
            'branch'          => 'required|string|in:Kuantan,Machang,Kuala Terengganu',
            'screen_condition' => 'nullable|string',
            'keypad_condition' => 'nullable|string',
            'sticker_condition' => 'nullable|string',
            'solar_condition' => 'nullable|string',
            'environment_condition' => 'nullable|string',
            'technician_name' => 'required|string',
            'photo_path.*' => 'nullable|image|mimes:jpeg,png,jpg,heic,heif|max:20480',
            'video_path'      => 'nullable|file|mimes:mp4,mov,avi|max:20480',
            'keypad_grade'     => 'nullable|in:A,B,C',
        ]);

        $photoPaths = [];
        if ($request->hasFile('photo_path')) {
            foreach ($request->file('photo_path') as $photo) {
                $photoPaths[] = $photo->store('inspection_photos', 'public');
            }
            $validated['photo_path'] = json_encode($photoPaths);
        }

        if ($request->hasFile('video_path')) {
            $validated['video_path'] = $request->file('video_path')->store('inspection_videos', 'public');
        }

        Inspection::create($validated);


        // Smart response based on request type
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Inspection created successfully!'], 200);
        } else {
            return redirect()->route('inspections.index')
                ->with('success', 'Inspection created successfully!');
        }
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
            'spare_parts'     => 'nullable|array',
            'spare_parts.*'   => 'string',
            'status'          => 'required|in:Complete,Failed,Almost',
            'branch'          => 'required|string|in:Kuantan,Machang,Kuala Terengganu',
            'technician_name' => 'required|string',
            'photo_path'      => 'nullable|file|mimes:jpeg,png,jpg,heic,heif|max:20480',
            'video_path'      => 'nullable|file|mimes:mp4,mov,avi|max:20480',
            'spare_grade'     => 'nullable|in:A,B,C',
            'screen_condition' => 'nullable|string',
            'keypad_condition' => 'nullable|string',
            'sticker_condition' => 'nullable|string',
            'solar_condition' => 'nullable|string',
            'environment_condition' => 'nullable|string',
        ]);

        if ($request->hasFile('photo_path')) {
            $validated['photo_path'] = $request->file('photo_path')->store('inspection_photos', 'public');
        }

        if ($request->hasFile('video_path')) {
            $validated['video_path'] = $request->file('video_path')->store('inspection_videos', 'public');
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
        return response()->json([
            'status' => 'success',
            'data' => $inspections
        ]);
    }

    public function show($id)
    {
        $inspection = Inspection::findOrFail($id);
        return view('departments.technical.inspection.show', compact('inspection'));
    }

    public function updateSpotcheck(Request $request, $id)
    {
        $inspection = Inspection::findOrFail($id);

        if ($request->has('spotcheck_verified')) {
            $inspection->spotcheck_verified = 'Checked';
            $inspection->spotcheck_verified_by = $request->has('spotcheck_verified') ? auth()->user()->name : null;
        } else {
            $inspection->spotcheck_verified = null;
            $inspection->spotcheck_verified_by = null;
        }

        $inspection->save();

        return back()->with('success', 'Spotcheck updated.');
    }
}
