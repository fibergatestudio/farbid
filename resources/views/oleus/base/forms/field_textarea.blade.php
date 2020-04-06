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
            <textarea id="{{ $params->get('id') }}"
                      name="{{ $params->get('name') }}"
                      {!! $params->get('attributes') ? " {$params->get('attributes')}" : '' !!}
                      class="uk-textarea uk-border-rounded{{ ($class = $params->get('class')) ? " {$class}" : '' }}{{ ($error = $params->get('error')) ? ' uk-form-danger' : '' }}">{!! $params->get('selected') !!}</textarea>
        </div>
        @if($help = $params->get('help'))
            <span class="uk-help-block uk-display-block">{!! $help !!}</span>
        @endif
    </div>
</div>