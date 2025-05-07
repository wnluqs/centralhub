<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LocalReport;
use Illuminate\Support\Facades\Auth;
use App\Models\Road;

class LocalReportController extends Controller
{
    public function index(Request $request)
    {
        $reports = LocalReport::latest()->get();

        if ($request->wantsJson()) {
            return response()->json($reports);
        }

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
            'photos.*' => 'nullable|image|mimes:jpeg,png,jpg,heic,heif|max:20480',
            'videos.*' => 'nullable|file|mimes:mp4,mov,avi|max:20480',
        ]);

        $validated['technician_name'] = Auth::user()->name;

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
}
