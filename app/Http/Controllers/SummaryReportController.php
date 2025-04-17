<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SummaryReport;
use App\Models\Terminal;
use App\Exports\SummaryReportsExport;  // For Excel export (Laravel-Excel)
use Maatwebsite\Excel\Facades\Excel;

class SummaryReportController extends Controller
{
    /**
     * Display a listing of the resource, optionally filtered.
     */
    public function index(Request $request)
    {
        $terminal  = $request->get('terminal');   // e.g., "1001"
        $sparePart = $request->get('spare_part');   // e.g., "wrench"

        $query = SummaryReport::with('terminal');

        // Filter by Terminal ID if provided
        if ($terminal) {
            $query->where('terminal_id', 'like', "%{$terminal}%");
        }

        // Filter by Spare Part if provided.
        // It will check all three spare_part fields for a match.
        if ($sparePart) {
            $query->where(function ($q) use ($sparePart) {
                $q->where('spare_part_1', 'like', "%{$sparePart}%")
                    ->orWhere('spare_part_2', 'like', "%{$sparePart}%")
                    ->orWhere('spare_part_3', 'like', "%{$sparePart}%");
            });
        }

        // Since these filters are applied separately, both conditions must be met (if provided).
        $reports = $query->get();

        return view('departments.technical.summary.index', compact('reports'));
    }


    /**
     * Show the form for creating a new report.
     */
    public function create()
    {
        // Example: fetch terminals if you want a dropdown
        $terminals = Terminal::all();

        // Example: define spare parts or statuses if needed
        $statusOptions = ['Complete' => 'Complete', 'Failed' => 'Failed', 'Almost' => 'Almost'];
        $spareParts = [
            'Broken Meter' => 'Broken Meter',
            'Receipt Output Malfunction' => 'Receipt Output Malfunction',
            'Buttons Malfunction' => 'Buttons Malfunction',
            'Paper Jam' => 'Paper Jam',
            'Screen Damage' => 'Screen Damage',
            'Meter Malfunction' => 'Meter Malfunction',
        ];  // Example list

        // Return the create views
        return view('departments.technical.summary.create', compact('terminals', 'statusOptions', 'spareParts'));
    }

    /**
     * Store a newly created report in storage.
     */
    public function store(Request $request)
    {
        // Validate
        $validated = $request->validate([
            'terminal_id'  => 'required|exists:terminals,id',
            'spare_part_1' => 'nullable|string',
            'spare_part_2' => 'nullable|string',
            'spare_part_3' => 'nullable|string',
            'status'       => 'required|in:Complete,Failed,Almost',
        ]);

        // **Create** the report (only once!)
        SummaryReport::create($validated);

        return redirect()->route('summary.index')
            ->with('success', 'Report created successfully!');
    }

    /**
     * Show the form for editing the specified report.
     */
    public function edit($id)
    {
        $report = SummaryReport::findOrFail($id);
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
        $report = SummaryReport::findOrFail($id);

        $validated = $request->validate([
            'terminal_id'  => 'required|exists:terminals,id',
            'spare_part_1' => 'nullable|string',
            'spare_part_2' => 'nullable|string',
            'spare_part_3' => 'nullable|string',
            'status'       => 'required|in:Complete,Failed,Almost',
        ]);

        $report->update($validated);

        return redirect()->route('summary.index')
            ->with('success', 'Report updated successfully!');
    }

    /**
     * Remove the specified report from storage.
     */
    public function destroy($id)
    {
        $report = SummaryReport::findOrFail($id);
        $report->delete();

        return redirect()->route('summary.index')
            ->with('success', 'Report deleted successfully!');
    }

    /**
     * Export filtered data to CSV.
     */
    public function exportCSV(Request $request)
    {
        // 1. Capture the same parameters
        $terminal  = $request->get('terminal');
        $sparePart = $request->get('spare_part');

        // 2. Build the same query as your index
        $query = SummaryReport::query();

        if ($terminal) {
            $query->where('terminal_id', 'like', "%{$terminal}%");
        }

        if ($sparePart) {
            $query->where(function ($q) use ($sparePart) {
                $q->where('spare_part_1', 'like', "%{$sparePart}%")
                    ->orWhere('spare_part_2', 'like', "%{$sparePart}%")
                    ->orWhere('spare_part_3', 'like', "%{$sparePart}%");
            });
        }

        $reports = $query->get();

        // 3. Generate CSV from $reports
        $filename = 'summary_reports.csv';
        $handle = fopen('php://memory', 'r+');

        // Header row
        fputcsv($handle, [
            'Terminal ID',
            'Created At',
            'Spare Part 1',
            'Spare Part 2',
            'Spare Part 3',
            'Status'
        ]);

        // Data rows
        foreach ($reports as $report) {
            fputcsv($handle, [
                $report->terminal_id,
                $report->created_at,
                $report->spare_part_1,
                $report->spare_part_2,
                $report->spare_part_3,
                $report->status,
            ]);
        }

        rewind($handle);
        $csvOutput = stream_get_contents($handle);
        fclose($handle);

        return response($csvOutput, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ]);
    }
    /**
     * Export filtered data to Excel (using Maatwebsite\Excel).
     */
    public function exportExcel(Request $request)
    {
        $terminal  = $request->get('terminal');
        $sparePart = $request->get('spare_part');

        // Pass both parameters to your Export class
        return Excel::download(new SummaryReportsExport($terminal, $sparePart), 'summary_reports.xlsx');
    }
}
