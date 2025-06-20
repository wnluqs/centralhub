@extends('layouts.app')

@section('content')
<a href="{{ route('dashboard') }}" class="btn btn-secondary mb-3">
    ← Back to Main Dashboard
</a>
<div class="container">
    <h1>User Management</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <a href="{{ route('admin.users.create') }}" class="btn btn-primary mb-3">Create New User</a>

    <table id="userTable" class="table table-bordered table-striped table-hover">
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Roles</th>
                <th>Staff ID</th>
                <th>Branch</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
            <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    @foreach($user->getRoleNames() as $role)
                        <span class="badge bg-primary">{{ $role }}</span>
                    @endforeach
                </td>
                <td>{{ $user->staff_id ?? '-' }}</td>
                <td>{{ $user->branch ?? '-' }}</td>
                <td>
                    <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm"
                            onclick="return confirm('Are you sure you want to delete this user?')">
                            Delete
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('#userTable').DataTable({
            pageLength: 10,
            lengthChange: false,
            ordering: true,
            autoWidth: false,
            language: {
                search: "🔍 Search Users:",
                paginate: {
                    previous: "←",
                    next: "→"
                }
            }
        });
    });
</script>
@endpush
