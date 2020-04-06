<div class="uk-form-row"
     id="{{ $params->get('id') }}-object">
    @if($label = $params->get('label'))
        <label for="{{ $params->get('id') }}"
               class="uk-form-label">{!! $label !!}
            @if($label = $params->get('required'))
                <span class="uk-text-danger">*</span>
            @endif
        </label>
    @endif
    <div class="uk-form-controls">
        <div class="uk-inline uk-width-1-1 uk-child-width-auto" uk-grid>
            @php
                $checked = $params->get('selected');
            @endphp
            @foreach($params->get('values') as $key => $value)
                @php
                    $_selected = ($key == $checked ? ' checked' : '');
                @endphp
                <label class="uk-text-small uk-display-block">
                    <input type="radio"
                           name="{{ $params->get('name') }}"
                           class="uk-radio{{ ($class = $params->get('class')) ? " {$class}" : '' }}{{ ($error = $params->get('error')) ? ' uk-form-danger' : '' }}"
                           value="{{ $key }}"{{ $_selected }}>
                    {!! $value !!}
                </label>
            @endforeach
        </div>
        @if ($error)
            <span class=" uk-help-block uk-text-danger">{!! $error !!}</span>
        @endif
        @if($help = $params->get('help'))
            <span class="uk-help-block uk-display-block">{!! $help !!}</span>
        @endif
    </div>
</div>