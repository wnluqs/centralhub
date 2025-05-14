<div class="table-responsive">
    <table id="{{ $tableId ?? 'complaintsTable' }}" class="table table-bordered table-striped">
        <!-- your thead + tbody as-is -->
        <thead class="table-light">
            <tr>
                <th>Terminal ID</th>
                <th>Zone</th>
                <th>Road</th>
                <th>Remarks</th>
                <th>Assigned To</th>
                <th>Types of Damages</th>
                <th>Attended At</th>
                <th>Fixed At</th>
                <th>Status</th>
                <th>Photos</th>
                <th style="display:none;">Created At</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($complaints as $c)
                <tr>
                    <td>{{ $c->terminal_id }}</td>
                    <td>{{ $c->zone }}</td>
                    <td>{{ ucfirst($c->road) }}</td>
                    <td>{{ $c->remarks }}</td>
                    <td>{{ $c->technician->name ?? '-' }}</td>
                    @php $types = json_decode($c->types_of_damages, true); @endphp
                    <td>{{ $types && is_array($types) ? implode(', ', $types) : '-' }}</td>
                    <td>{{ $c->attended_at ? \Carbon\Carbon::parse($c->attended_at)->format('Y-m-d H:i:s') : '-' }}
                    </td>
                    <td>{{ $c->fixed_at ? \Carbon\Carbon::parse($c->fixed_at)->format('Y-m-d H:i:s') : '-' }}</td>
                    <td>
                        <span
                            class="badge bg-{{ $c->status === 'Resolved' ? 'success' : ($c->status === 'In Progress' ? 'warning text-dark' : 'secondary') }}">
                            {{ $c->status }}
                        </span>
                    </td>
                    <td>
                        @if ($c->photos)
                            @foreach (json_decode($c->photos, true) as $p)
                                <a href="{{ asset('storage/' . $p) }}" target="_blank">View</a><br>
                            @endforeach
                        @else
                            -
                        @endif
                    </td>
                    <td style="display:none;">{{ $c->created_at }}</td>
                    <td>
                        @if ($c->status === 'New')
                            <a href="{{ route('complaints.assign', $c->id) }}" class="btn btn-sm btn-warning">Attend</a>
                        @elseif($c->status === 'In Progress')
                            <form action="{{ route('complaints.markFixed', $c->id) }}" method="POST"
                                style="display:inline;">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-sm btn-success me-2">Verify</button>
                            </form>

                            <form action="{{ route('complaints.unassign', $c->id) }}" method="POST"
                                style="display:inline;">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="btn btn-sm btn-danger">Reassign</button>
                            </form>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="11" class="text-center">No complaints available.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
