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
                    <td>{{ optional($c->zone)->name ?? '-' }}</td>
                    <td>{{ ucfirst($c->road) }}</td>
                    <td>{{ $c->remarks }}</td>
                    <td>{{ $c->technician->name ?? '-' }}</td>
                    @php $types = $c->types_of_damages; @endphp
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
                            @php
                                $photos = is_string($c->photos) ? json_decode($c->photos, true) : $c->photos ?? [];
                            @endphp

                            @foreach ($photos as $p)
                                <a href="{{ asset('storage/' . $p) }}" target="_blank">View</a><br>
                            @endforeach
                        @else
                            -
                        @endif
                    </td>
                    <td style="display:none;">{{ $c->created_at }}</td>
                    <td>
                        @if ($c->status === 'New')
                            @if (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('ControlCenter'))
                                <a href="{{ route('technical.complaints.assign', $c->id) }}"
                                    class="btn btn-sm btn-warning">Assign</a>
                            @elseif (auth()->user()->hasRole('Technical'))
                                <a href="{{ route('technical.complaints.attend', $c->id) }}"
                                    class="btn btn-sm btn-info">Attend</a>
                            @else
                                <span class="text-muted">No Action</span>
                            @endif
                        @elseif ($c->status === 'In Progress')
                            @if (auth()->user()->hasRole('Admin') || auth()->user()->hasRole('ControlCenter'))
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
                                <span class="text-muted">No Action</span>
                            @endif
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
