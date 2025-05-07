<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FTLT;
use App\Models\Terminal;

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
        $validated['user_id'] = auth()->id();

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
}
