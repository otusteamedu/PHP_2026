<div class="mb-3">
    <label class="form-label" for="direction_id">Направление</label>
    <select class="form-select @error('direction_id') is-invalid @enderror" id="direction_id" name="direction_id" required>
        <option value="" disabled @selected(!old('direction_id', $course?->direction_id))>Выберите</option>
        @foreach ($directions as $direction)
            <option value="{{ $direction->id }}" @selected((string) old('direction_id', $course?->direction_id) === (string) $direction->id)>
                {{ $direction->name }}
            </option>
        @endforeach
    </select>
    @error('direction_id')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
<div class="mb-3">
    <label class="form-label" for="title">Название</label>
    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title"
           value="{{ old('title', $course?->title) }}" required maxlength="255">
    @error('title')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
<div class="mb-3">
    <label class="form-label" for="slug">Slug</label>
    <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug"
           value="{{ old('slug', $course?->slug) }}" required maxlength="255" pattern="[a-z0-9]+(?:-[a-z0-9]+)*">
    @error('slug')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
<div class="mb-3">
    <label class="form-label" for="summary">Краткое описание</label>
    <textarea class="form-control @error('summary') is-invalid @enderror" id="summary" name="summary" rows="4">{{ old('summary', $course?->summary) }}</textarea>
    @error('summary')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
<div class="mb-3">
    <label class="form-label" for="duration_hours">Длительность (часы)</label>
    <input type="number" class="form-control @error('duration_hours') is-invalid @enderror" id="duration_hours" name="duration_hours"
           value="{{ old('duration_hours', $course?->duration_hours) }}" min="0" max="32767">
    @error('duration_hours')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
