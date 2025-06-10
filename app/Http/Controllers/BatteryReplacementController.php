<?php

namespace App\Http\Controllers;

use App\Models\BatteryReplacement;
use App\Models\Terminal;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Exports\BatteryExport;
use App\Services\FirebaseUploader;

class BatteryReplacementController extends Controller
{
    public function index(Request $request)
    {
        $query = BatteryReplacement::query();

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                $request->start_date,
                $request->end_date
            ]);
        }

        $jobs = $query->latest()->get();

        return view('departments.technical.battery.index', compact('jobs'));
    }

    public function create()
    {
        $terminals = Terminal::select('id')->get(); // Ensure terminal_number is included
        return view('departments.technical.battery.create', compact('terminals'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'terminal_ids' => 'required|array',
        ]);

        foreach ($request->terminal_ids as $tid) {
            BatteryReplacement::create([
                'terminal_id' => $tid,
                'status' => 'Assigned',
                'comment' => $request->comment,
                'created_by' => auth()->id(),
            ]);
        }

        return redirect()->route('battery.index')->with('success', 'Battery jobs created!');
    }

    public function attend($id)
    {
        $job = BatteryReplacement::findOrFail($id);
        return view('departments.technical.battery.attend', compact('job'));
    }

    public function updateAttend(Request $request, $id)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'comment' => 'nullable|string',
        ]);

        $job = BatteryReplacement::findOrFail($id);

        // ✅ Upload to Firebase Storage
        $firebase = new FirebaseUploader();
        $photoPath = $firebase->uploadFile($request->file('photo'), 'battery_photos');

        // ✅ Update job record
        $job->update([
            'status' => 'Submitted',
            'photo' => $photoPath, // Store Firebase URL/path
            'comment' => $request->comment,
            'staff_id' => auth()->user()->staff_id,
            'submitted_by' => auth()->id(),
        ]);

        return redirect()->route('battery.index')->with('success', 'Battery job submitted!');
    }

    // API for Mobile
    public function apiIndex()
    {
        $staffId = auth()->user()->staff_id;
        $jobs = BatteryReplacement::where('staff_id', $staffId)->where('status', 'Assigned')->get();
        return response()->json($jobs);
    }

    public function apiSubmit(Request $request, $id)
    {
        $job = BatteryReplacement::findOrFail($id);

        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'comment' => 'nullable|string',
        ]);

        // ✅ Firebase upload for mobile too
        $firebase = new FirebaseUploader();
        $photoPath = $firebase->uploadFile($request->file('photo'), 'battery_photos');

        $job->update([
            'photo' => $photoPath,
            'comment' => $request->comment,
            'status' => 'Submitted',
            'staff_id' => auth()->user()->staff_id,
            'submitted_by' => auth()->id(),
        ]);

        return response()->json(['message' => 'Battery job submitted successfully']);
    }

    public function exportExcel()
    {
        return Excel::download(new BatteryExport, 'battery_jobs.xlsx');
    }

    public function exportCsv()
    {
        return Excel::download(new BatteryExport, 'battery_jobs.csv');
    }
}
