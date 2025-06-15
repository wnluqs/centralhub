<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FTLT;
use App\Models\Terminal;
use Illuminate\Support\Facades\Log;
use App\Services\FirebaseUploader; // For Firebase file uploads

class FTLTController extends Controller
{
    // 🌐 Web View
    public function index(Request $request)
    {
        if (!auth()->check()) {
            return back()->with('error', 'User not authenticated.');
        }

        $query = FTLT::query();

        if (auth()->user()->hasRole('Technical')) {
            $query->where('staff_id', auth()->user()->staff_id);
        }

        if ($request->filled('branch')) {
            $query->where('branch', $request->branch);
        }

        if ($request->filled('start_time') && $request->filled('end_time')) {
            $query->whereTime('check_in_time', '>=', $request->start_time)
                  ->whereTime('check_in_time', '<=', $request->end_time);
        }

        $ftlts = $query->orderBy('created_at', 'desc')->paginate(10);
        return view('departments.technical.ftlt.index', compact('ftlts'));
    }

    // 📲 API GET: For Mobile
    public function apiIndex()
    {
        return response()->json(
            FTLT::with('user')->latest()->get()
        );
    }

    public function create()
    {
        $terminals = Terminal::all();
        return view('departments.technical.ftlt.create', compact('terminals'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'branch' => 'required|string',
            'location'       => 'required|string',
            'checkin_photo'  => 'required|image|mimes:jpeg,jpg,png|max:5120',
            'notes'          => 'nullable|string',
        ]);

        $user = auth()->user();
        if (!$user || !$user->staff_id) {
            return back()->with('error', 'Missing staff ID');
        }

        $validated['staff_id'] = $user->staff_id;
        $validated['user_id'] = $user->id;
        $validated['branch'] = auth()->user()->branch;
        $validated['check_in_time'] = now();
        $firebase = new FirebaseUploader();
        $validated['checkin_photo'] = $firebase->uploadFile($request->file('checkin_photo'), 'ftlt_photos');

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
        $firebase = new FirebaseUploader();
        $ftlt->checkout_photo = $firebase->uploadFile($request->file('checkout_photo'), 'ftlt_photos');
        $ftlt->check_out_time = now();
        $ftlt->notes = $request->input('notes');
        $ftlt->save();

        return redirect()->route('ftlt.index')->with('success', 'Check-out completed!');
    }

    // ✅ API POST: Check-In from Flutter
    public function apiCheckIn(Request $request)
    {
        Log::info('📥 Check-In hit', $request->all());

        $validated = $request->validate([
            'staff_id'      => 'required|string',
            'name'          => 'required|string',
            'branch'       => 'required|string',
            'location'      => 'required|string',
            'check_in_time' => 'required|date',
            'checkin_photo' => 'required|image|mimes:jpeg,jpg,png|max:5120',
            'user_id'       => 'required|numeric',
        ]);

        $validated['user_id'] = (int) $validated['user_id'];
        $firebase = new FirebaseUploader();
        $validated['checkin_photo'] = $firebase->uploadFile($request->file('checkin_photo'), 'ftlt_photos');

        FTLT::create($validated);
        return response()->json(['message' => 'Check-In saved'], 201);
    }

    // ✅ API POST: Check-Out from Flutter
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
        $entry->location = $validated['location'];
        $firebase = new FirebaseUploader();
        $entry->checkout_photo = $firebase->uploadFile($request->file('checkout_photo'), 'ftlt_photos');

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

        $firebase = new FirebaseUploader();

        if ($request->hasFile('checkin_photo')) {
            $validated['checkin_photo'] = $firebase->uploadFile($request->file('checkin_photo'), 'ftlt_photos');
        }

        if ($request->hasFile('checkout_photo')) {
            $validated['checkout_photo'] = $firebase->uploadFile($request->file('checkout_photo'), 'ftlt_photos');
        }

        $ftlt->update($validated);

        return response()->json([
            'message' => 'FTLT record updated successfully',
            'data' => $ftlt->fresh()
        ], 200);
    }

    public function apiDelete($id)
    {
        $ftlt = FTLT::find($id);
        if (!$ftlt) {
            return response()->json(['error' => 'FTLT record not found'], 404);
        }

        $ftlt->delete();

        return response()->json(['message' => 'FTLT record deleted', 'id' => $id], 200);
    }
}
