<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BTS;
use Illuminate\Support\Facades\Auth;
use App\Models\Terminal;
use App\Models\Report;

class BTSController extends Controller
{
    // Show BTS List
    public function index()
    {
        $available = BTS::where('action_status', 'New')->latest()->get();
        $inProgress = BTS::where('action_status', 'In Progress')->latest()->get();

        return view('departments.technical.bts.index', compact('available', 'inProgress'));
    }

    // Show Create BTS Form
    public function create()
    {
        return view('departments.technical.bts.create');
    }

    // Store New BTS Record
    public function store(Request $request)
    {
        $validated = $request->validate([
            'terminal_id'     => 'required|exists:terminals,id',
            'status'          => 'required|string',
            'location'        => 'required|string',
            'event_date'      => 'required|date',
            'event_code_name' => 'required|string',
            'comment'         => 'nullable|string',
        ]);

        $validated['action_status'] = 'New'; // Always default when created
        $validated['action_by'] = null;       // No technician yet
        $validated['terminal_status'] = 'Okay'; // <-- Default it here to avoid NULL


        BTS::create($validated);

        return redirect()->route('bts.index')->with('success', 'BTS alert submitted successfully!');
    }

    // Show Attend Form
    public function attend($id)
    {
        $bts = BTS::findOrFail($id);
        return view('departments.technical.bts.attend', compact('bts'));
    }

    // Update After Attending
    public function updateAttend(Request $request, $id)
    {
        $bts = BTS::findOrFail($id);

        $validated = $request->validate([
            'comment' => 'required|string',
            'parts_request' => 'required|string', // New field
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'terminal_status' => 'required|in:Okay,Off',
        ]);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('bts_photos', 'public');
            $validated['photo'] = $path;
        }

        $validated['action_status'] = 'In Progress';
        $validated['action_by'] = Auth::id();

        $bts->update($validated);

        Report::create([
            'terminal_id' => $bts->terminal_id,
            'location' => $bts->location,
            'event_date' => $bts->event_date,
            'event_code_name' => $bts->event_code_name,
            'comment' => $validated['comment'],
            'parts_request' => $validated['parts_request'],
            'photo' => $bts->photo ?? null,
            'terminal_status' => $validated['terminal_status'],
        ]);

        return redirect()->route('bts.index')->with('success', 'BTS alert attended successfully!');
    }

    public function searchTerminals(Request $request)
    {
        $search = $request->get('q');

        $terminals = Terminal::where('id', 'like', "%$search%")
            ->orderBy('id', 'asc')
            ->limit(20)
            ->get(['id']);

        return response()->json($terminals);
    }

    public function verify($id)
    {
        $bts = BTS::findOrFail($id);
        $bts->verified = true; // Assuming you have a verified column in your BTS model
        $bts->save();

        return redirect()->route('bts.index')->with('success', 'BTS attendance has been verified.');
    }

// Show BTS Details from Control Center
    public function controlCenterView()
    {
        $available = BTS::where('action_status', 'New')->latest()->get();
        $inProgress = BTS::where('action_status', 'In Progress')->latest()->get();

        return view('departments.controlcenter.bts.index', compact('available', 'inProgress'));
    }

    public function reassign($id)
    {
        $bts = BTS::findOrFail($id);
        $bts->action_status = 'New';
        $bts->action_by = null;
        $bts->save();

        return redirect()->route('bts.index')->with('success', 'BTS alert reassigned to Available Jobs.');
    }
}
