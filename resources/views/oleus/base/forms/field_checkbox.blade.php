<div class="uk-form-row"
     id="{{ $params->get('id') }}_object">
    @if($error = $params->get('error'))
        <span
            class="uk-help-block uk-text-danger uk-text-right uk-margin-small-bottom uk-display-block">{!! $error !!}</span>
    @endif
    <div class="uk-form-controls uk-form-controls-text uk-form-controls-checkbox">
        <label class="uk-text-small"
               for="{{ $params->get('id') }}">
            <div class="uk-text-lowercase">
                <input name="{{ $params->get('name') }}"
                       type="hidden"
                       value="0">
                <input name="{{ $params->get('name') }}"
                       type="checkbox"
                       id="{{ $params->get('id') }}"
                       class="uk-checkbox{{ ($error = $params->get('error')) ? ' uk-form-danger' : '' }}"
                       value="{{ $params->get('value') }}"
                    {!! $params->get('attributes') ? " {$params->get('attributes')}" : '' !!}
                    {{ $params->get('selected') ? ' checked' : '' }}>{!! $params->get('label') !!}
                @if($label = $params->get('required'))
                    <span class="uk-text-danger">*</span>
                @endif
            </div>
        </label>
        @if($help = $params->get('help'))
            <span class="uk-help-block uk-display-block">{!! $help !!}</span>
        @endif
    </div>
</div>