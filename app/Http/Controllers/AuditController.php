<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;

class AuditController extends Controller
{
    public function index(Request $request)
    {
        // 1. Retrieve sort parameters from the query string (e.g. ?sort=user&direction=asc).
        $sort = $request->get('sort');
        $direction = $request->get('direction', 'asc'); // default to 'asc' if not specified

        // 2. Define a list of allowed sortable columns to prevent errors or injection
        $allowedSorts = ['user', 'event', 'model', 'ip_address', 'created_at'];
        //  (You can adjust these to match your actual columns)

        // 3. Build your base query
        $query = AuditLog::with('user');

        // 4. If the 'sort' parameter is valid, apply the orderBy
        if (in_array($sort, $allowedSorts)) {
            // Special case: The “User” column is actually stored as user->name in the relationship.
            // We'll handle that separately, or you can join the users table.
            // But for a simple approach, let's do a join if you want to sort by user name.
            // Or you can sort by user_id if that’s acceptable.
            if ($sort === 'user') {
                // Sort by the user's name. We can do a join:
                $query->select('audit_logs.*')
                    ->join('users', 'audit_logs.user_id', '=', 'users.id')
                    ->orderBy('users.name', $direction);
            } else {
                // Sort by a direct column on the activity_logs table
                $query->orderBy($sort, $direction);
            }
        } else {
            // If no valid sort is specified, you might define a default sort order
            $query->orderBy('created_at', 'desc');
        }

        // 5. Paginate
        $logs = $query->paginate(10);

        // 6. Pass the current sort/direction to the view for the links
        return view('departments.technical.audit.index', [
            'logs' => $logs,
            'currentSort' => $sort,
            'currentDirection' => $direction
        ]);
    }
}
