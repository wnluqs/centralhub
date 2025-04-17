@extends('layouts.app')

@section('content')
<!-- Back Button -->
<a href="{{ route('admin.settings') }}" class="btn btn-secondary mb-3">← Back to Admin's Setting</a>
<!-- Page Title -->
<div class="container">
    <h1>Create New User</h1>

    <form action="{{ route('admin.users.store') }}" method="POST">
        @csrf

        <!-- Name -->
        <div class="form-group">
            <label for="name">Name:</label>
            <input type="text" name="name" id="name" class="form-control" value="{{ old('name') }}" required>
            @error('name')
            <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <!-- Email -->
        <div class="form-group mt-2">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email" class="form-control" value="{{ old('email') }}" required>
            @error('email')
            <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <!-- Password -->
        <div class="form-group mt-2">
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" class="form-control" required>
            @error('password')
            <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

        <!-- Confirm Password -->
        <div class="form-group mt-2">
            <label for="password_confirmation">Confirm Password:</label>
            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control"
                required>
        </div>

        <!-- Roles (Multiple) -->
        <div class="form-group mt-3">
            <label for="roles">Assign Role:</label>
            <select name="roles[]" id="roles" class="form-control select2">
                <option value="" disabled selected>Select a role</option>
                @foreach($roles as $role)
                <option value="{{ $role->name }}">{{ $role->name }}</option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-success mt-3">
            Create User
        </button>
    </form>
</div>
@endsection