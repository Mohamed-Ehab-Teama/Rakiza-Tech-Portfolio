{{--
    One form field. Expects $name, $field (definition from the controller) and
    $record (the model being edited, or null). Also used by Settings, which
    passes $value directly.
--}}
@php
    $type = $field['type'];
    $id = 'field-'.$name;
    $error = $errors->first($name) ?: $errors->first("remove_{$name}");
    $helpId = ! empty($field['help']) ? $id.'-help' : null;
    $errorId = $error ? $id.'-error' : null;
    $describedBy = trim(($helpId ?? '').' '.($errorId ?? '')) ?: null;

    // The value to show: what was just typed (after a failed save), else the
    // saved value, else the field's default.
    if (! isset($value)) {
        $value = $record?->{$name} ?? ($field['default'] ?? null);
    }
    if ($type === 'tags' && is_array($value)) {
        $value = implode(', ', $value);
    }
    if ($value instanceof \DateTimeInterface) {
        $value = $value->format('Y-m-d');
    }
    if ($type === 'boolean') {
        $checked = session()->hasOldInput() ? (bool) old($name) : (bool) $value;
    } elseif ($type !== 'image') {
        $value = old($name, $value);
    }

    $width = match ($field['width'] ?? 'full') {
        'half' => 'form-field--half',
        'quarter' => 'form-field--quarter',
        default => '',
    };
@endphp

<div class="form-field {{ $width }}">
  @if ($type === 'boolean')
    <label class="switch" for="{{ $id }}">
      <input type="checkbox" id="{{ $id }}" name="{{ $name }}" value="1" @checked($checked)
             @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif>
      <span class="switch__track" aria-hidden="true"></span>
      <span class="switch__label">{{ $field['label'] }}</span>
    </label>
  @else
    <label class="form-label" for="{{ $id }}">{{ $field['label'] }}</label>

    @switch($type)
      @case('textarea')
      @case('markdown')
      @case('lines')
        <textarea class="textarea{{ $type === 'markdown' ? ' textarea--code' : '' }}{{ $error ? ' is-invalid' : '' }}"
                  id="{{ $id }}" name="{{ $name }}" rows="{{ $type === 'markdown' ? 12 : ($type === 'lines' ? 6 : 4) }}"
                  @if ($error) aria-invalid="true" @endif
                  @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif>{{ $value }}</textarea>
        @break

      @case('select')
        <select class="select{{ $error ? ' is-invalid' : '' }}" id="{{ $id }}" name="{{ $name }}"
                @if ($error) aria-invalid="true" @endif
                @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif>
          @foreach ($field['options'] as $optionValue => $optionLabel)
            <option value="{{ $optionValue }}" @selected((string) $value === (string) $optionValue)>{{ $optionLabel }}</option>
          @endforeach
        </select>
        @break

      @case('image')
        @if ($value)
          <div class="image-field__current">
            <img src="{{ asset('storage/'.$value) }}" alt="Current {{ Str::lower($field['label']) }}" loading="lazy">
            <label class="checkbox">
              <input type="checkbox" name="remove_{{ $name }}" value="1">
              <span>Remove this image</span>
            </label>
          </div>
        @endif
        <input class="input input--file{{ $error ? ' is-invalid' : '' }}" type="file" id="{{ $id }}" name="{{ $name }}"
               accept="image/jpeg,image/png,image/webp" data-preview
               @if ($error) aria-invalid="true" @endif
               @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif>
        @break

      @default
        <input class="input{{ $error ? ' is-invalid' : '' }}" id="{{ $id }}" name="{{ $name }}" value="{{ $value }}"
               type="{{ match ($type) { 'email' => 'email', 'url' => 'url', 'number' => 'number', 'date' => 'date', default => 'text' } }}"
               @if ($type === 'number') step="{{ $field['step'] ?? '1' }}" inputmode="decimal" @endif
               @if ($type === 'url') placeholder="https://" @endif
               @if ($error) aria-invalid="true" @endif
               @if ($describedBy) aria-describedby="{{ $describedBy }}" @endif>
    @endswitch
  @endif

  @if ($helpId)
    <p class="form-help" id="{{ $helpId }}">{{ $field['help'] }}</p>
  @endif
  @if ($errorId)
    <p class="form-error" id="{{ $errorId }}">{{ $error }}</p>
  @endif
</div>
