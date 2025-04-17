@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Inspection</h1>
    <form action="{{ route('inspections.update', $inspection->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label>Notes</label>
            <textarea name="notes" class="form-control">{{ $inspection->notes }}</textarea>
        </div>
        <button type="submit" class="btn btn-success">Update</button>
    </form>
</div>
@endsection