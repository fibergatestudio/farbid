@if($relation_params)
    <select name="category_relation_param[]"
            multiple
            class="uk-select uk-select2">
        @foreach($relation_params as $key => $value)
            @php
                $_selected = !is_null($selected) ? (in_array($key, $selected->toArray()) ? ' selected' : '') : '';
            @endphp
            <option value="{{ $key }}" {{ $_selected }}>{!! $value !!}</option>
        @endforeach
    </select>
@endif