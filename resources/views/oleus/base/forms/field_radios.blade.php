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
        <div>
            @php
                $checked = $params->get('selected');
            @endphp
            @foreach($params->get('values') as $key => $value)
                @php
                    $_selected = ($key == $checked ? ' checked' : '');
                @endphp
                <label class="uk-text-small uk-display-block uk-margin-small-top">
                    <input type="radio"
                           name="{{ $params->get('name') }}"
                           class="uk-radio{{ ($class = $params->get('class')) ? " {$class}" : '' }}{{ ($error = $params->get('error')) ? ' uk-form-danger' : '' }}"
                           {!! $params->get('attributes') ? " {$params->get('attributes')}" : '' !!}
                           value="{{ $key }}"{{ $_selected }}>{!! $value !!}</label>
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