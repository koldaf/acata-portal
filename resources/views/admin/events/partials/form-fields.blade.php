<div class="col-md-6">
    <label class="form-label" for="title">Title</label>
    <input id="title" name="title" type="text" class="form-control @error('title') is-invalid @enderror"
        value="{{ old('title', $event?->title) }}" required>
    @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="col-md-6">
    <label class="form-label" for="location">Location</label>
    <input id="location" name="location" type="text" class="form-control @error('location') is-invalid @enderror"
        value="{{ old('location', $event?->location) }}">
    @error('location')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="col-md-6">
    <label class="form-label" for="starts_at">Starts At</label>
    <input id="starts_at" name="starts_at" type="datetime-local" class="form-control @error('starts_at') is-invalid @enderror"
        value="{{ old('starts_at', $event?->starts_at?->format('Y-m-d\\TH:i')) }}" required>
    @error('starts_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="col-md-6">
    <label class="form-label" for="ends_at">Ends At</label>
    <input id="ends_at" name="ends_at" type="datetime-local" class="form-control @error('ends_at') is-invalid @enderror"
        value="{{ old('ends_at', $event?->ends_at?->format('Y-m-d\\TH:i')) }}">
    @error('ends_at')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="col-md-4">
    <label class="form-label" for="capacity">Capacity</label>
    <input id="capacity" name="capacity" type="number" min="1" class="form-control @error('capacity') is-invalid @enderror"
        value="{{ old('capacity', $event?->capacity) }}">
    @error('capacity')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="col-md-4">
    <label class="form-label" for="status">Status</label>
    <select id="status" name="status" class="form-select @error('status') is-invalid @enderror" required>
        @php $currentStatus = old('status', $event?->status ?? 'draft'); @endphp
        <option value="draft" @selected($currentStatus === 'draft')>Draft</option>
        <option value="published" @selected($currentStatus === 'published')>Published</option>
        <option value="closed" @selected($currentStatus === 'closed')>Closed</option>
    </select>
    @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>

<div class="col-md-4 d-flex align-items-end">
    <div class="form-check mb-2">
        @php $open = old('registration_open', $event?->registration_open ?? true); @endphp
        <input class="form-check-input" type="checkbox" id="registration_open" name="registration_open" value="1" @checked($open)>
        <label class="form-check-label" for="registration_open">Registration Open</label>
    </div>
</div>

<div class="col-12">
    <label class="form-label" for="description">Description</label>
    <textarea id="description" name="description" rows="5" class="form-control @error('description') is-invalid @enderror">{{ old('description', $event?->description) }}</textarea>
    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
</div>
