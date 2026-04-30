<div class="row g-3">
    <div class="col-12">
        <label class="form-label" for="title">Название</label>
        <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title"
               value="{{ old('title', $task->title ?? '') }}" required maxlength="255">
        @error('title')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-12">
        <label class="form-label" for="description">Описание</label>
        <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description"
                  rows="4" maxlength="10000">{{ old('description', $task->description ?? '') }}</textarea>
        @error('description')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-12 col-md-6">
        <label class="form-label" for="due_at">Срок</label>
        <input type="datetime-local" class="form-control @error('due_at') is-invalid @enderror" id="due_at"
               name="due_at"
               value="{{ old('due_at', isset($task) && $task->due_at ? $task->due_at->format('Y-m-d\TH:i') : '') }}">
        @error('due_at')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
    <div class="col-12 col-md-6 d-flex align-items-end">
        <div class="form-check mb-2">
            <input class="form-check-input" type="checkbox" name="is_done" id="is_done" value="1"
                @checked(old('is_done', $task->is_done ?? false))>
            <label class="form-check-label" for="is_done">Выполнено</label>
        </div>
    </div>
</div>
