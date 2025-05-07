@extends('layouts.app')

@section('content')
    <!-- Back Button -->
    <a href="{{ route('admin.settings') }}" class="btn btn-secondary mb-3">← Back to Admin's Setting</a>
    <div class="container">
        <h1>Edit User: {{ $user->name }}</h1>
        <form action="{{ route('admin.users.update', $user) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" name="name" id="name" class="form-control" value="{{ old('name', $user->name) }}"
                    required>
                @error('name')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group mt-2">
                <label for="email">Email:</label>
                <input type="email" name="email" id="email" class="form-control"
                    value="{{ old('email', $user->email) }}" required>
                @error('email')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group mt-2">
                <label for="staff_id">Staff ID (optional):</label>
                <input type="text" name="staff_id" id="staff_id" class="form-control"
                    value="{{ old('staff_id', $user->staff_id ?? '') }}">
                @error('staff_id')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group mt-2">
                <label for="branch">Branch:</label>
                <select name="branch" id="branch" class="form-control">
                    <option value="" disabled>Select Branch</option>
                    <option value="Kuantan" {{ $user->branch == 'Kuantan' ? 'selected' : '' }}>Kuantan</option>
                    <option value="Kuala Terengganu" {{ $user->branch == 'Kuala Terengganu' ? 'selected' : '' }}>Kuala
                        Terengganu</option>
                    <option value="Machang" {{ $user->branch == 'Machang' ? 'selected' : '' }}>Machang</option>
                </select>
            </div>

            <div class="form-group mt-2">
                <label for="password">Password: <small>(Leave blank if not changing)</small></label>
                <input type="password" name="password" id="password" class="form-control">
                @error('password')
                    <small class="text-danger">{{ $message }}</small>
                @enderror
            </div>

            <div class="form-group mt-2">
                <label for="password_confirmation">Confirm Password:</label>
                <input type="password" name="password_confirmation" id="password_confirmation" class="form-control">
            </div>

            <div class="form-group mt-3">
                <label for="roles">Assign Roles:</label>
                <select name="roles[]" id="roles" class="form-control" multiple>
                    @foreach ($roles as $role)
                        <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>
                <small class="form-text text-muted">Hold down the Ctrl (Windows) or Command (Mac) button to select multiple
                    roles.</small>
            </div>

            <button type="submit" class="btn btn-primary mt-3">Update User</button>
        </form>
    </div>
@endsection
