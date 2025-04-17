<?php

// app/Http/Controllers/SupportController.php
namespace App\Http\Controllers;

use App\Models\SupportRequest;
use Illuminate\Http\Request;

class SupportController extends Controller
{
    public function form()
    {
        return view('admin.support.form');
    }

    public function submit(Request $request)
    {
        // Validate input
        $request->validate([
            'message' => 'required',
        ]);

        // Save the support request in the database
        SupportRequest::create([
            'user_id' => auth()->check() ? auth()->id() : null,
            'message' => $request->message,
        ]);

        return redirect()->route('support.form')->with('success', 'Your support request has been submitted!');
    }
    // app/Http/Controllers/SupportController.php
    public function adminIndex()
    {
        $supportRequests = SupportRequest::orderBy('created_at', 'desc')->get();
        return view('admin.support.index', compact('supportRequests'));
    }
    public function markRead($id)
    {
        $request = SupportRequest::findOrFail($id);
        $request->update(['is_read' => true]);
        return redirect()->route('admin.support.index')->with('success', 'Request marked as read.');
    }

    public function destroy($id)
    {
        $request = SupportRequest::findOrFail($id);
        $request->delete();
        return redirect()->route('admin.support.index')->with('success', 'Request deleted.');
    }
}
