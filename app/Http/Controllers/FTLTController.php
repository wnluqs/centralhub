<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FTLT;
use App\Models\Terminal;
use Illuminate\Support\Facades\Log;

class FTLTController extends Controller
{
    public function index(Request $request)
    {
        $query = FTLT::query();

        // 🛡️ If the user is a Technician, limit to only their records
        if (!auth()->check()) {
            return back()->with('error', 'User not authenticated.');
        }

        $validated['staff_id'] = auth()->user()->staff_id; // use the new field
        $validated['user_id'] = 1; // or any fixed ID for now
        // ✅ Admins can still use filters
        if (auth()->user()->hasRole('Technical')) {
            $query->where('staff_id', auth()->user()->staff_id);
        }

        if ($request->filled('zone')) {
            $query->where('zone', $request->zone);
        }

        if ($request->filled('start_time') && $request->filled('end_time')) {
            $query->whereTime('check_in_time', '>=', $request->start_time)
                ->whereTime('check_in_time', '<=', $request->end_time);
        }

        $ftlts = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('departments.technical.ftlt.index', compact('ftlts'));
    }
    // For Flutter/mobile
    public function apiIndex()
    {
        $ftlts = FTLT::with('user')->latest()->get();
        return response()->json($ftlts);
    }

    public function create()
    {
        $terminals = Terminal::all();
        return view('departments.technical.ftlt.create', compact('terminals'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'zone'           => 'required|string',
            'location'       => 'required|string',
            'checkin_photo'  => 'required|image|mimes:jpeg,jpg,png|max:5120',
            'notes'          => 'nullable|string',
        ]);

        $user = auth()->user();

        if (!$user || !$user->staff_id) {
            dd('Missing staff_id on user');
        }

        $validated['staff_id'] = $user->staff_id;
        $validated['user_id'] = $user->id;
        $validated['check_in_time'] = now();

        // Try uploading the photo
        if ($request->hasFile('checkin_photo')) {
            $validated['checkin_photo'] = $request->file('checkin_photo')->store('ftlt_photos', 'public');
        } else {
            dd('No photo uploaded');
        }

        // See what's inside the $validated
        FTLT::create($validated);
        return redirect()->route('ftlt.index')->with('success', 'Check-in successful!');
    }

    public function checkoutForm($id)
    {
        $ftlt = FTLT::findOrFail($id);
        return view('departments.technical.ftlt.checkout', compact('ftlt'));
    }

    public function checkoutSubmit(Request $request, $id)
    {
        $request->validate([
            'checkout_photo' => 'required|image|mimes:jpeg,jpg,png|max:5120',
        ]);

        $ftlt = FTLT::findOrFail($id);
        $ftlt->checkout_photo = $request->file('checkout_photo')->store('ftlt_photos', 'public');
        $ftlt->check_out_time = now();
        $ftlt->notes = $request->input('notes');
        $ftlt->save();

        return redirect()->route('ftlt.index')->with('success', 'Check-out completed!');
    }

    public function apiCheckIn(Request $request)
    {
        Log::info('📥 Check-In hit', $request->all());

        $validated = $request->validate([
            'staff_id'      => 'required|string',
            'name'          => 'required|string',
            'zone'          => 'required|string',
            'location'      => 'required|string',
            'check_in_time' => 'required|date',
            'checkin_photo' => 'required|image|mimes:jpeg,jpg,png|max:5120',
        ]);

        $validated['user_id'] = 1;
        $validated['checkin_photo'] = $request->file('checkin_photo')->store('ftlt_photos', 'public');

        FTLT::create($validated);

        return response()->json(['message' => 'Check-In saved'], 201);
    }

    public function apiCheckOut(Request $request)
    {
        Log::info('📤 Check-Out hit', $request->all());

        $validated = $request->validate([
            'staff_id'        => 'required|string',
            'name'            => 'required|string',
            'location'        => 'required|string',
            'check_out_time'  => 'required|date',
            'checkout_photo'  => 'required|image|mimes:jpeg,jpg,png|max:5120',
        ]);

        $entry = FTLT::where('staff_id', $validated['staff_id'])
            ->whereNull('check_out_time')
            ->latest()
            ->first();

        if (!$entry) {
            return response()->json(['error' => 'No check-in found'], 404);
        }

        $entry->check_out_time = $validated['check_out_time'];
        $entry->location = $validated['location']; // optional overwrite
        $entry->checkout_photo = $request->file('checkout_photo')->store('ftlt_photos', 'public');
        $entry->save();

        return response()->json(['message' => 'Check-Out updated'], 200);
    }

    public function apiUpdate(Request $request, $id)
    {
        $ftlt = FTLT::find($id);

        if (!$ftlt) {
            return response()->json(['error' => 'FTLT record not found'], 404);
        }

        $validated = $request->validate([
            'zone'           => 'nullable|string',
            'location'       => 'nullable|string',
            'check_in_time'  => 'nullable|date',
            'check_out_time' => 'nullable|date',
            'notes'          => 'nullable|string',
            'checkin_photo'  => 'nullable|image|mimes:jpeg,jpg,png|max:5120',
            'checkout_photo' => 'nullable|image|mimes:jpeg,jpg,png|max:5120',
        ]);

        if ($request->hasFile('checkin_photo')) {
            $validated['checkin_photo'] = $request->file('checkin_photo')->store('ftlt_photos', 'public');
        }

        if ($request->hasFile('checkout_photo')) {
            $validated['checkout_photo'] = $request->file('checkout_photo')->store('ftlt_photos', 'public');
        }

        $ftlt->update($validated);

        return response()->json([
            'message' => 'FTLT record updated successfully',
            'data' => $ftlt->fresh() // ✅ this reloads from DB
        ], 200);
    }

    public function apiDelete($id)
    {
        $ftlt = FTLT::find($id);

        if (!$ftlt) {
            return response()->json(['error' => 'FTLT record not found'], 404);
        }

        $ftlt->delete();

        return response()->json([
            'message' => 'FTLT record deleted successfully',
            'id' => $id
        ], 200);
    }
}
