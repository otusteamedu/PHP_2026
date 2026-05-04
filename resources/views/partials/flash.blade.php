@if (session('ok'))
    <div class="alert alert-success small py-2 mb-3">{{ session('ok') }}</div>
@endif
@if (session('status'))
    <div class="alert alert-info small py-2 mb-3">{{ session('status') }}</div>
@endif
