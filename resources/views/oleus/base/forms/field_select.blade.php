<div class="uk-form-row"
     id="{{ $params->get('id') }}_object">
    @if($error = $params->get('error'))
        <span
            class="uk-help-block uk-text-danger uk-text-right uk-margin-small-bottom uk-display-block">{!! $error !!}</span>
    @endif
    @if($label = $params->get('label'))
        <label for="{{ $params->get('id') }}"
               class="uk-form-label">{!! $label !!}
            @if($label = $params->get('required'))
                <span class="uk-text-danger">*</span>
            @endif
        </label>
    @endif
    <div class="uk-form-controls">
        <div class="uk-inline uk-width-1-1">
            <select id="{{ $params->get('id') }}"
                    name="{{ $params->get('name') }}"
                    class="uk-select{{ ($class = $params->get('class')) ? " {$class}" : '' }}{{ ($error = $params->get('error')) ? ' uk-form-danger' : '' }}"
                {!! $params->get('multiple') ? ' multiple' : '' !!}
                {!! $params->get('attributes') ? " {$params->get('attributes')}" : '' !!}>
                @foreach($params->get('values') as $key => $value)
                    @php
                        $selected = $params->get('selected');
                    @endphp
                    @if(is_array($selected) || is_object($selected))
                        @php
                            if(is_object($selected)) $selected = $selected->toArray();
                            $_selected = in_array($key, $selected) ? ' selected' : '';
                        @endphp
                        <option value="{{ $key ? $key : NULL }}" {{ $_selected }}>{!! $value !!}</option>
                    @else
                        @php
                            $_selected = !is_null($selected) ? ($selected == $key ? ' selected' : '') : '';
                        @endphp
                        <option value="{{ !is_null($key) ? $key : NULL }}" {{ $_selected }}>{!! $value !!}</option>
                    @endif
                @endforeach
            </select>
        </div>
        @if($help = $params->get('help'))
            <span class="uk-help-block uk-display-block">{!! $help !!}</span>
        @endif
    </div>
</div>