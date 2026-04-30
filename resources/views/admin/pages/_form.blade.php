<div class="mb-3">
    <label class="form-label" for="title">Заголовок</label>
    <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title"
           value="{{ old('title', $page?->title) }}" required maxlength="255">
    @error('title')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
<div class="mb-3">
    <label class="form-label" for="slug">Slug (латиница, дефисы)</label>
    <input type="text" class="form-control @error('slug') is-invalid @enderror" id="slug" name="slug"
           value="{{ old('slug', $page?->slug) }}" required maxlength="255" pattern="[a-z0-9]+(?:-[a-z0-9]+)*">
    @error('slug')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
<div class="mb-3">
    <label class="form-label" for="body">Текст</label>
    <textarea class="form-control @error('body') is-invalid @enderror" id="body" name="body" rows="12">{{ old('body', $page?->body) }}</textarea>
    @error('body')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
<div class="form-check mb-3">
    <input class="form-check-input" type="checkbox" name="is_published" id="is_published" value="1"
           @checked(old('is_published', $page?->is_published ?? true))>
    <label class="form-check-label" for="is_published">Опубликована</label>
</div>
