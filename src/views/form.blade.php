
@foreach($form as $name => $f)
    <label>{{ $name }}</label>
    @if($f['type'] == 'select' && isset($f['options']) && !empty($f['options']))
        <select class="{{ $f['class'] }}">
            @foreach($f['options'] as $key => $option)
                <option value="{{ $key }}" @if($f['selected'] == $key) selected @endif @if($f['disabled']) disabled @endif>
                    {{ $option }}
                </option>
            @endforeach
        </select>
    @else
        <input id="{{ $f['id'] }}" name="{{ $f['name'] }}" class="{{ $f['class'] }}" type="{{ $f['type'] }}" value="{{ $f['value'] }}" @if($f['disabled']) disabled @endif>
    @endif
@endforeach
<button type="submit" class="{{ $dynamicforms['css']['submit'] }}">Save</button>