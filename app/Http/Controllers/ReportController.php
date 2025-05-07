<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;           // BTS Reports
use App\Models\Complaint;        // Complaint Reports
use App\Models\LocalReport;      // Local (“Laporan Setempat”) Reports
use App\Models\Terminal;
use App\Exports\ReportExport; // Ensure this class exists in the specified namespace
use Maatwebsite\Excel\Facades\Excel;
use DB;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource, optionally filtered.
     */
    public function index(Request $request)
    {
        $terminal = $request->get('terminal');
        $status   = $request->get('terminal_status');
        $type     = $request->get('type');

        //
        // 1) BTS Reports
        //
        $bts = Report::query();
        if ($terminal)  $bts->where('terminal_id', 'like', "%{$terminal}%");
        if ($status)    $bts->where('terminal_status', $status);
        $btsReports = $bts->get()->map(function ($r) {
            $r->type = 'BTS';
            return $r;
        });

        //
        // 2) Complaint Reports
        //
        $complaints = Complaint::select(
            'id',
            'terminal_id',
            'zone as location',
            'created_at as event_date',
            'types_of_damages',
            DB::raw("'' as event_code_name"),
            'remarks as comment',
            DB::raw("'' as parts_request"),
            'attended_at',
            'fixed_at',
            'photos as photo',
            'status as terminal_status'
        )

            ->when($terminal, fn($q) => $q->where('terminal_id', 'like', "%{$terminal}%"))
            ->when($status,   fn($q) => $q->where('status', $status))
            ->get()
            ->map(function ($c) {
                $c->type = 'Complaint';
                return $c;
            });

        //
        // 3) Local Reports (“Laporan Setempat”)
        //    Assuming your LocalReport model has:
        //      zone, road, public_complaints, operations_complaints,
        //      photos, videos, technician, date
        //
        $local = LocalReport::select(
            'id',
            // if you don’t have a terminal_id field here, just blank it out:
            DB::raw("'' as terminal_id"),
            'zone as location',
            'created_at as event_date',    // ← use created_at
            DB::raw("'' as event_code_name"),
            'public_complaints as comment',
            DB::raw("'' as parts_request"),
            'photos as photo',
            DB::raw("'' as terminal_status")
        )
            ->when($status, fn($q) => $q->where('date', 'like', "%{$status}%")) // optional date filter?
            ->get()
            ->map(function ($lr) {
                $lr->type = 'Local';
                return $lr;
            });

        // pick the correct collection
        if ($type === 'BTS') {
            $reports = $btsReports;
        } elseif ($type === 'Complaint') {
            $reports = $complaints;
        } elseif ($type === 'Local') {
            $reports = $local;
        } else {
            $reports = $btsReports
                ->merge($complaints)
                ->merge($local)
                ->sortByDesc('event_date')
                ->values();
        }

        return view('departments.technical.report.index', compact('reports', 'type'));
    }
    /**
     * Show the form for creating a new report.
     */
    public function create()
    {
        $terminals = Terminal::all();

        $terminalStatusOptions = ['Okay' => 'Okay', 'Off' => 'Off'];

        return view('departments.technical.summary.create', compact('terminals', 'terminalStatusOptions'));
    }

    /**
     * Store a newly created report in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'terminal_id'      => 'required|exists:terminals,id',
            'location'         => 'nullable|string',
            'event_date'       => 'required|date',
            'event_code_name'  => 'nullable|string',
            'comment'          => 'nullable|string',
            'parts_request'    => 'nullable|string',
            'photo'            => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'terminal_status'  => 'required|in:Okay,Off',
        ]);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('summary_photos', 'public');
            $validated['photo'] = $path;
        }

        Report::create($validated);

        return redirect()->route('summary.index')->with('success', 'Summary Report created successfully!');
    }

    /**
     * Show the form for editing the specified report.
     */
    public function edit($id)
    {
        $report = Report::findOrFail($id);
        $terminals = Terminal::all();
        $statusOptions = ['Complete' => 'Complete', 'Failed' => 'Failed', 'Almost' => 'Almost'];
        $spareParts = [
            'Broken Meter' => 'Broken Meter',
            'Receipt Output Malfunction' => 'Receipt Output Malfunction',
            'Buttons Malfunction' => 'Buttons Malfunction',
            'Paper Jam' => 'Paper Jam',
            'Screen Damage' => 'Screen Damage',
            'Meter Malfunction' => 'Meter Malfunction',
        ];
        return view('departments.technical.summary.edit', compact('report', 'terminals', 'statusOptions', 'spareParts'));
    }

    /**
     * Update the specified report in storage.
     */
    public function update(Request $request, $id)
    {
        $report = Report::findOrFail($id);

        $validated = $request->validate([
            'terminal_id'      => 'required|exists:terminals,id',
            'location'         => 'nullable|string',
            'event_date'       => 'required|date',
            'event_code_name'  => 'nullable|string',
            'comment'          => 'nullable|string',
            'parts_request'    => 'nullable|string',
            'photo'            => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'terminal_status'  => 'required|in:Okay,Off',
        ]);

        if ($request->hasFile('photo')) {
            $path = $request->file('photo')->store('summary_photos', 'public');
            $validated['photo'] = $path;
        }

        $report->update($validated);

        return redirect()->route('summary.index')->with('success', 'Report updated successfully!');
    }
    /**
     * Remove the specified report from storage.
     */
    public function destroy($id)
    {
        $report = Report::findOrFail($id);
        $report->delete();

        return redirect()->route('summary.index')
            ->with('success', 'Report deleted successfully!');
    }

    /**
     * Export filtered data to CSV.
     */
    public function exportCSV(Request $request)
    {
        $type = $request->get('type');

        // Build the right collection
        if ($type === 'BTS') {
            $items = Report::all()->map(fn($r) => [
                'type'            => 'BTS',
                'terminal_id'     => $r->terminal_id,
                'location'        => '',           // fill if you have it
                'event_date'      => $r->event_date,
                'event_code_name' => $r->event_code_name,
                'comment'         => $r->comment,
                'parts_request'   => '',
                'terminal_status' => $r->terminal_status,
            ]);
        } elseif ($type === 'Complaint') {
            $items = Complaint::all()->map(fn($c) => [
                'type'            => 'Complaint',
                'terminal_id'     => $c->terminal_id,
                'location'        => $c->zone,
                'event_date'      => $c->created_at,
                'event_code_name' => '',
                'comment'         => $c->remarks,
                'parts_request'   => '',
                'terminal_status' => $c->status,
            ]);
        } elseif ($type === 'Local') {
            $items = LocalReport::all()->map(fn($l) => [
                'type'            => 'Local',
                'terminal_id'     => '',          // no terminal in your local table?
                'location'        => $l->zone,
                'event_date'      => $l->created_at,
                'event_code_name' => '',
                'comment'         => $l->public_complaints,
                'parts_request'   => '',
                'terminal_status' => '',
            ]);
        } else {
            // Merge all three if no type filter
            $items = collect()
                ->concat(Report::all()->map(fn($r) => [
                    'type' => 'BTS',
                    'terminal_id'     => $r->terminal_id,
                    'location' => '',
                    'event_date' => $r->event_date,
                    'event_code_name' => $r->event_code_name,
                    'comment' => $r->comment,
                    'parts_request' => '',
                    'terminal_status' => $r->terminal_status,
                ]))
                ->concat(Complaint::all()->map(fn($c) => [
                    'type' => 'Complaint',
                    'terminal_id' => $c->terminal_id,
                    'location' => $c->zone,
                    'event_date' => $c->created_at,
                    'event_code_name' => '',
                    'comment' => $c->remarks,
                    'parts_request' => '',
                    'terminal_status' => $c->status,
                ]))
                ->concat(LocalReport::all()->map(fn($l) => [
                    'type' => 'Local',
                    'terminal_id' => '',
                    'location' => $l->zone,
                    'event_date' => $l->created_at,
                    'event_code_name' => '',
                    'comment' => $l->public_complaints,
                    'parts_request' => '',
                    'terminal_status' => '',
                ]))
                ->sortByDesc('event_date');
        }

        // Write out CSV
        $filename = 'reports.csv';
        $handle   = fopen('php://memory', 'r+');

        // Header row
        fputcsv($handle, ['Type', 'Terminal ID', 'Location', 'Event Date', 'Event Code - Name', 'Comment', 'Parts Request', 'Terminal Status']);

        foreach ($items as $row) {
            fputcsv($handle, array_values($row));
        }

        rewind($handle);
        $csv = stream_get_contents($handle);
        fclose($handle);

        return response($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]);
    }
    /**
     * Export filtered data to Excel (using Maatwebsite\Excel).
     */
    public function exportExcel(Request $request)
    {
        $type = $request->get('type');
        return Excel::download(new ReportExport($type), 'reports.xlsx');
    }
}
