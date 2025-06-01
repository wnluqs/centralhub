<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Inspection;
use App\Models\Terminal;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\InspectionsExport;
use Carbon\Carbon;
use App\Services\FirebaseUploader;  // ✅ <-- Use your service class!

// If you have a FirebaseUploader class, import it here
// use App\Services\FirebaseUploader;

class InspectionsController extends Controller
{
    public function index(Request $request) //updated on 26st May 2025 to add the auto-refresh
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
        if ($request->filled('submitted_by')) {
            $query->where('submitted_by', 'LIKE', "%{$request->submitted_by}%");
        }
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $start = Carbon::parse($request->start_date)->startOfDay();
            $end = Carbon::parse($request->end_date)->endOfDay();
            $query->whereBetween('created_at', [$start, $end]);
        }
        if ($request->filled('keypad_grade')) {
            $query->where('keypad_grade', 'LIKE', "%{$request->keypad_grade}%");
        }

        $sortField = $request->input('sort_field', 'created_at');
        $sortOrder = $request->input('sort_order', 'desc');
        $query->orderBy($sortField, $sortOrder); // used only once

        if ($request->has('json')) {
            return response()->json([
                'inspections' => $query
                    ->take(10)
                    ->get()
                    ->map(function ($i) {
                        return [
                            'id' => $i->id,
                            'terminal_id' => $i->terminal_id,
                            'zone' => $i->zone,
                            'road' => $i->road,
                            'branch' => $i->branch,
                            'spare_parts' => $i->spare_parts,
                            'status' => $i->status,
                            'submitted_by' => $i->submitted_by,
                            'created_at' => $i->created_at,
                            'keypad_grade' => $i->keypad_grade,
                            'spotcheck_verified_by' => $i->spotcheck_verified_by
                        ];
                    })
            ]);
        }

        $inspections = $query->orderBy('created_at', 'desc')->paginate(10);
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
        // 1️⃣ Validation — Perfectly fine
        $validated = $request->validate([
            'terminal_id'     => 'required|exists:terminals,id',
            'zone'            => 'required|string',
            'road'            => 'required|string',
            'spare_parts'     => 'nullable|array',
            'spare_parts.*'   => 'string',
            'status'          => 'required|in:Complete,Failed,Almost',
            'branch'          => 'required|string|in:Kuantan,Machang,Kuala Terengganu',
            'screen'          => 'nullable|string',
            'keypad'          => 'nullable|string',
            'sticker'         => 'nullable|string',
            'solar'           => 'nullable|string',
            'environment'     => 'nullable|string',
            'submitted_by'    => 'required|string',
            'photo_path.*'    => 'nullable|image|mimes:jpeg,png,jpg,heic,heif|max:20480',
            'video_path'      => 'nullable|file|mimes:mp4,mov,avi|max:20480',
            'keypad_grade'    => 'nullable|in:A,B,C',
        ]);

        // 2️⃣ FirebaseUploader Instantiation — ✔✅
        $firebase = new FirebaseUploader();

        // 3️⃣ Upload Photos (multiple)
        if ($request->hasFile('photo_path')) {
            foreach ($request->file('photo_path') as $photo) {
                $photoPaths[] = $firebase->uploadFile($photo, 'inspection_photos');
            }
            $validated['photo_path'] = json_encode($photoPaths);
        }

        if ($request->hasFile('video_path')) {
            $validated['video_path'] = $firebase->uploadFile($request->file('video_path'), 'inspection_videos');
        }

        // 5️⃣ Save to DB
        Inspection::create($validated);

        // 6️⃣ Return response
        if ($request->expectsJson()) {
            return response()->json(['message' => 'Inspection created successfully!'], 200);
        } else {
            return redirect()->route('inspections.index')->with('success', 'Inspection created successfully!');
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
            'submitted_by' => 'required|string',
            'photo_path'      => 'nullable|file|mimes:jpeg,png,jpg,heic,heif|max:20480',
            'video_path'      => 'nullable|file|mimes:mp4,mov,avi|max:20480',
            'spare_grade'     => 'nullable|in:A,B,C',
            'screen' => 'nullable|string',
            'keypad' => 'nullable|string',
            'sticker' => 'nullable|string',
            'solar' => 'nullable|string',
            'environment' => 'nullable|string',
        ]);

        $firebase = new FirebaseUploader();

        if ($request->hasFile('photo_path')) {
            $photoPaths = [];
            foreach ($request->file('photo_path') as $photo) {
                $photoPaths[] = $firebase->uploadFile($photo, 'inspection_photos');
            }
            $validated['photo_path'] = json_encode($photoPaths);
        }

        if ($request->hasFile('video_path')) {
            $validated['video_path'] = $firebase->uploadFile($request->file('video_path'), 'inspection_videos');
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

        return response()->json($inspections); // ✅ Only return array directly updated on 21st May 2025
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

    public function apiStore(Request $request)
    {
        $validated = $request->validate([
            'terminal_id' => 'required|exists:terminals,id',
            'zone' => 'required|string',
            'road' => 'required|string',
            'spare_parts' => 'nullable|array',
            'spare_parts.*' => 'string',
            'status' => 'required|in:Complete,Failed,Almost',
            'branch' => 'required|string|in:Kuantan,Machang,Kuala Terengganu',
            'screen' => 'nullable|string',
            'keypad' => 'nullable|string',
            'sticker' => 'nullable|string',
            'solar' => 'nullable|string',
            'environment' => 'nullable|string',
            'submitted_by' => 'required|string',
            'photo_path.*' => 'nullable|image|mimes:jpeg,png,jpg,heic,heif|max:20480',
            'video_path' => 'nullable|file|mimes:mp4,mov,avi|max:20480',
            'keypad_grade' => 'nullable|in:A,B,C',
        ]);

        $firebase = new FirebaseUploader();

        // 3️⃣ Upload Photos (multiple)
        if ($request->hasFile('photo_path')) {
            foreach ($request->file('photo_path') as $photo) {
                $photoPaths[] = $firebase->uploadFile($photo, 'inspection_photos');
            }
            $validated['photo_path'] = json_encode($photoPaths);
        }

        if ($request->hasFile('video_path')) {
            $validated['video_path'] = $firebase->uploadFile($request->file('video_path'), 'inspection_videos');
        }

        $inspection = Inspection::create($validated);

        return response()->json(['message' => 'Inspection created successfully!', 'data' => $inspection], 201);
    }

    public function apiUpdate(Request $request, $id)
    {
        $inspection = Inspection::find($id);

        if (!$inspection) {
            return response()->json(['error' => 'Inspection not found'], 404);
        }

        $validated = $request->validate([
            'terminal_id' => 'sometimes|exists:terminals,id',
            'zone' => 'sometimes|string',
            'road' => 'sometimes|string',
            'spare_parts' => 'nullable|array',
            'spare_parts.*' => 'string',
            'status' => 'sometimes|in:Complete,Failed,Almost',
            'branch' => 'sometimes|string|in:Kuantan,Machang,Kuala Terengganu',
            'screen' => 'nullable|string',
            'keypad' => 'nullable|string',
            'sticker' => 'nullable|string',
            'solar' => 'nullable|string',
            'environment' => 'nullable|string',
            'submitted_by' => 'sometimes|string',
            'photo_path.*' => 'nullable|image|mimes:jpeg,png,jpg,heic,heif|max:20480',
            'video_path' => 'nullable|file|mimes:mp4,mov,avi|max:20480',
            'keypad_grade' => 'nullable|in:A,B,C',
        ]);

        $firebase = new FirebaseUploader();

        if ($request->hasFile('photo_path')) {
            $photoPaths = [];
            foreach ($request->file('photo_path') as $photo) {
                $photoPaths[] = $firebase->uploadFile($photo, 'inspection_photos');
            }
            $validated['photo_path'] = json_encode($photoPaths);
        }

        if ($request->hasFile('video_path')) {
            $validated['video_path'] = $firebase->uploadFile($request->file('video_path'), 'inspection_videos');
        }

        $inspection->update($validated);

        return response()->json(['message' => 'Inspection updated successfully', 'data' => $inspection->fresh()], 200);
    }

    public function apiDelete($id)
    {
        $inspection = Inspection::find($id);

        if (!$inspection) {
            return response()->json(['error' => 'Inspection not found'], 404);
        }

        $inspection->delete();

        return response()->json(['message' => 'Inspection deleted successfully', 'id' => $id], 200);
    }
}
