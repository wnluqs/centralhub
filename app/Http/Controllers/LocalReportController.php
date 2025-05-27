<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LocalReport;
use Illuminate\Support\Facades\Auth;
use App\Models\Road;
use App\Models\Zone;

class LocalReportController extends Controller
{
    public function index(Request $request)
    {
        $reports = LocalReport::orderByDesc('id')->get();

        // If AJAX or JSON requested, return raw JSON
        if ($request->ajax() || $request->query('json')) {
            return response()->json([
                'reports' => $reports,
            ]);
        }

        // Otherwise, return the Blade view
        return view('departments.technical.local_report.index', compact('reports'));
    }

    public function create()
    {
        $roads = Road::all(); // 10 generic roads
        return view('departments.technical.local_report.create', compact('roads'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'zone' => 'required|string',
            'road' => 'required|string',
            'public_complaints' => 'nullable|array',
            'public_others' => 'nullable|string',
            'operations_complaints' => 'nullable|array',
            'operations_others' => 'nullable|string',
            'landmark' => 'nullable|string',
            'latitude' => 'nullable|numeric', //added on 24th May2024
            'longitude' => 'nullable|numeric',
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,heic,heif|max:20480',
            'videos.*' => 'nullable|file|mimes:mp4,mov,avi|max:20480',
        ]);

        $validated['zone'] = $request->input('zone');
        $zoneId = $request->input('zone');
        $zone = Zone::find($zoneId);
        $validated['zone'] = $zone?->name ?? 'Unknown';
        $validated['technician_name'] = Auth::user()->name;
        $validated['landmark'] = $request->input('landmark');
        $validated['latitude'] = $request->input('latitude');
        $validated['longitude'] = $request->input('longitude');

        // Process photos
        $photoPaths = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $photoPaths[] = $photo->store('local_report_photos', 'public');
            }
            $validated['photos'] = json_encode($photoPaths);
        }

        // Process videos
        $videoPaths = [];
        if ($request->hasFile('videos')) {
            foreach ($request->file('videos') as $video) {
                $videoPaths[] = $video->store('local_report_videos', 'public');
            }
            $validated['videos'] = json_encode($videoPaths);
        }

        // JSON encode checkbox fields
        // Handle dynamic public complaints with sub-values
        $public = [];

        if ($request->has('public_complaints')) {
            foreach ($request->input('public_complaints') as $key => $complaint) {
                // If it's an array (like 'Halangan Dalam Petak'), expect 'type' and 'value'
                if (is_array($complaint)) {
                    $type = $complaint['type'] ?? '';
                    $value = $complaint['value'] ?? '0';
                    $public[$key] = $type . ' : ' . $value;
                } else {
                    // If it's a normal numeric value
                    $public[$key] = $complaint;
                }
            }
        }
        $validated['public_complaints'] = json_encode($public);

        // Handle dynamic operations complaints (with sub-values)
        $ops = [];

        if ($request->has('operations_complaints')) {
            foreach ($request->input('operations_complaints') as $key => $complaint) {
                // Assume all operation values are simple text or numeric (no subtypes)
                $ops[$key] = $complaint;
            }
        }
        $validated['operations_complaints'] = json_encode($ops);

        $report = LocalReport::create($validated);

        if ($request->wantsJson()) {
            return response()->json(['message' => 'Local report created', 'data' => $report], 201);
        }

        return redirect()->route('technical-local_report')->with('success', 'Local report submitted successfully.');
    }

    // PUT: Update existing local report
    public function apiUpdate(Request $request, $id)
    {
        $report = LocalReport::find($id);
        if (!$report) {
            return response()->json(['error' => 'Report not found'], 404);
        }

        $validated = $request->validate([
            'zone' => 'sometimes|string',
            'road' => 'sometimes|string',
            'public_complaints' => 'nullable|array',
            'public_others' => 'nullable|string',
            'operations_complaints' => 'nullable|array',
            'operations_others' => 'nullable|string',
        ]);

        // Convert arrays to JSON if present
        if (isset($validated['public_complaints'])) {
            $validated['public_complaints'] = json_encode($validated['public_complaints']);
        }

        if (isset($validated['operations_complaints'])) {
            $validated['operations_complaints'] = json_encode($validated['operations_complaints']);
        }

        $report->update($validated);

        return response()->json(['message' => 'Local report updated successfully', 'data' => $report->fresh()], 200);
    }

    // DELETE: Remove a local report
    public function apiDelete($id)
    {
        $report = LocalReport::find($id);
        if (!$report) {
            return response()->json(['error' => 'Report not found'], 404);
        }

        $report->delete();
        return response()->json(['message' => 'Local report deleted successfully', 'id' => $id]);
    }

    public function apiIndex()
    {
        $bts = LocalReport::latest()->get();
        return response()->json($bts);
    }

    public function apiStore(Request $request)
    {
        // Convert stringified JSON to actual array if coming from Flutter
        if (is_string($request->public_complaints)) {
            $request->merge([
                'public_complaints' => json_decode($request->public_complaints, true)
            ]);
        }

        if (is_string($request->operations_complaints)) {
            $request->merge([
                'operations_complaints' => json_decode($request->operations_complaints, true)
            ]);
        }

        $validated = $request->validate([
            'branch' => 'required|string',
            'zone' => 'required|string',
            'road' => 'required|string',
            'public_complaints' => 'nullable|array',
            'public_others' => 'nullable|string',
            'operations_complaints' => 'nullable|array',
            'operations_others' => 'nullable|string',
            'landmark' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric', //added on 24th May 2024
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg|max:20480',
            'videos' => 'nullable|file|mimes:mp4,mov,avi|max:20480',
        ]);

        // Convert zone ID to zone name for API input
        $zoneId = $request->input('zone');
        $zone = is_numeric($zoneId)
            ? Zone::find($zoneId)
            : Zone::where('name', $zoneId)->first();
        $validated['zone'] = $zone?->name ?? 'Unknown';
        $validated['technician_name'] = $request->input('technician_name');
        $validated['landmark'] = $request->input('landmark');
        $validated['latitude'] = $request->input('latitude');
        $validated['longitude'] = $request->input('longitude');

        // ✅ Handle file uploads
        $photoPath = [];
        if ($request->hasFile('photos')) {
            foreach ($request->file('photos') as $photo) {
                $photoPath[] = $photo->store('local_report_photos', 'public');
            }
        }
        $validated['photos'] = json_encode($photoPath);

        $videoPath = null;
        if ($request->hasFile('videos')) {
            $videoPath = $request->file('videos')->store('local_report_videos', 'public');
        }
        $validated['videos'] = json_encode($videoPath ? [$videoPath] : []);

        // Re-encode for storage
        $validated['public_complaints'] = isset($validated['public_complaints'])
            ? json_encode($validated['public_complaints'])
            : json_encode([]);

        $validated['operations_complaints'] = isset($validated['operations_complaints'])
            ? json_encode($validated['operations_complaints'])
            : json_encode([]);

        $report = LocalReport::create($validated);

        return response()->json(['message' => 'Local report created', 'data' => $report], 201);
    }
}
