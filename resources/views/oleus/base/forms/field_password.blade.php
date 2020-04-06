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
            @if($icon = $params->get('icon'))
                <span class="uk-form-icon uk-icon"
                      uk-icon="icon: {{ $icon }}"></span>
            @endif
            <input type="{{ $params->get('type') }}"
                   id="{{ $params->get('id') }}"
                   name="{{ $params->get('name') }}"
                   value=""
                   class="uk-input uk-border-rounded{{ ($error = $params->get('error')) ? ' uk-form-danger' : '' }}"
                {!! $params->get('attributes') ? " {$params->get('attributes')}" : '' !!}>
        </div>
        @if($help = $params->get('help'))
            <span class="uk-help-block uk-display-block">{!! $help !!}</span>
        @endif
    </div>
</div>
