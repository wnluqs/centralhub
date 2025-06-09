<table id="{{ $tableId }}" class="table table-bordered table-striped table-sm text-center align-middle">
    <thead class="thead-dark">
        <tr>
            <th>Staff ID</th>
            <th>Terminal ID</th>
            <th>Status</th>
            <th>Location</th>
            <th>Event Date</th>
            <th>Event Code - Name</th>
            <th>Comment</th>
            @if ($mode === 'in_progress')
                <th>Parts Request</th>
                <th>Terminal Status</th>
                <th>Created At (Attended)</th>
            @endif
            <th>Action</th>
            @if ($mode === 'in_progress')
                <th>Photo</th>
            @endif
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $item)
            <tr class="{{ session('highlight_id') == $item->id ? 'newly-added' : '' }}">
                <td>
                    @if (empty($item->staff_id) || $item->staff_id === 'UNKNOWN')
                        <span class="badge bg-secondary">Unassigned</span>
                    @else
                        <span class="badge bg-info text-dark">{{ $item->staff_id }}</span>
                    @endif
                </td>
                <td>{{ $item->terminal_id }}</td>
                <td>
                    @php
                        $badgeColor = match (strtolower($item->status)) {
                            'error' => 'danger',
                            'warning' => 'warning text-dark',
                            'normal', 'okay' => 'success',
                            default => 'secondary',
                        };
                    @endphp
                    <span class="badge bg-{{ $badgeColor }}">
                        {{ ucfirst($item->status) }}
                    </span>
                </td>
                <td>{{ $item->location }}</td>
                <td>{{ \Carbon\Carbon::parse($item->event_date)->format('d/m/Y H:i') }}</td>
                <td>{{ $item->event_code_name }}</td>
                <td>{{ $item->comment ?? '-' }}</td>

                @if ($mode === 'in_progress')
                    <td>{{ $item->parts_request ?? '-' }}</td>
                    <td>
                        @if (!empty($item->terminal_status))
                            <span class="badge bg-{{ $item->terminal_status === 'Off' ? 'danger' : 'success' }}">
                                {{ $item->terminal_status }}
                            </span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td>{{ \Carbon\Carbon::parse($item->created_at)->format('d/m/Y H:i') }}</td>
                    <td>
                        @if ($item->verified)
                            <span class="badge bg-success">Verified</span>
                        @elseif (auth()->user()->hasRole(['Admin', 'ControlCenter']))
                            <form method="POST" action="{{ route('bts.verify', $item->id) }}" style="display:inline;">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-sm btn-success mb-1">Verify</button>
                            </form>

                            <form method="POST" action="{{ route('bts.reassign', $item->id) }}"
                                style="display:inline;">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-sm btn-warning">Reassign</button>
                            </form>
                        @else
                            <span class="badge bg-warning text-dark">Pending</span>
                        @endif
                    </td>
                    <td>
                        @if ($item->photo)
                            <a href="{{ $item->photo }}" target="_blank">View Photo</a>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                @elseif ($mode === 'available')
                    <td>
                        <a href="{{ route('bts.attend', $item->id) }}" class="btn btn-sm btn-primary">Attend</a>
                    </td>
                @endif
            </tr>
        @endforeach
    </tbody>
</table>
