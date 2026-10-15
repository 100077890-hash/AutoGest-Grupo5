@php
    $displayValue = old($name, $value ?? '');
    $displayValue = is_scalar($displayValue) ? $displayValue : '';
    $descripciones = [];
    if (isset($help)) $descripciones[] = 'ayuda-'.$name;
    if ($errors->has($name)) $descripciones[] = 'error-'.$name;
@endphp
<div class="grupo-campo">
    <label for="{{ $name }}">{{ $label }}@if($required ?? true)<span aria-hidden="true"> *</span>@endif</label>
    <input id="{{ $name }}" name="{{ $name }}" type="{{ $type ?? 'text' }}" value="{{ ($type ?? 'text') === 'password' ? '' : $displayValue }}"
        @if($required ?? true) required @endif
        @isset($max) maxlength="{{ $max }}" @endisset
        @isset($min) min="{{ $min }}" @endisset
        @isset($maximum) max="{{ $maximum }}" @endisset
        @isset($autocomplete) autocomplete="{{ $autocomplete }}" @endisset
        @isset($autocapitalize) autocapitalize="{{ $autocapitalize }}" @endisset
        @if($descripciones) aria-describedby="{{ implode(' ', $descripciones) }}" @endif
        @error($name) aria-invalid="true" @enderror>
    @isset($help)<p class="ayuda-campo" id="ayuda-{{ $name }}">{{ $help }}</p>@endisset
    @error($name)<p class="error-campo" id="error-{{ $name }}">{{ $message }}</p>@enderror
</div>
