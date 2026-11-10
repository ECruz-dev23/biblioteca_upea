@if ($errors->any())
    <div class="alerta alerta-error">{{ $errors->first() }}</div>
@endif
@if (session('error'))
    <div class="alerta alerta-error">{{ session('error') }}</div>
@endif