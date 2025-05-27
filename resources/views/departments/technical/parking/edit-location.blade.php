<form method="POST" action="{{ route('terminal_parking.update_location', $terminal->id) }}">
    @csrf
    <div class="mb-3">
        <label>Latitude</label>
        <input type="text" name="latitude" value="{{ $terminal->latitude }}" class="form-control" required>
    </div>
    <div class="mb-3">
        <label>Longitude</label>
        <input type="text" name="longitude" value="{{ $terminal->longitude }}" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary">Save</button>
</form>
