<div class="uk-form-row">
    @if($error = $params->get('error'))
        <span
            class="uk-help-block uk-text-danger uk-text-right uk-margin-small-bottom uk-display-block">{!! $error !!}</span>
    @endif
    <div class="uk-form-controls">
        <div class="uk-grid uk-child-width-1-2">
            <div id="{{ $params->get('id') }}_object">
                @if($label = $params->get('label'))
                    <label for="{{ $params->get('id') }}"
                           class="uk-form-label">
                        {!! $label !!}
                        @if($params->get('required'))
                            <span class="uk-text-danger">*</span>
                        @endif
                    </label>
                @endif
                <div class="uk-inline uk-width-1-1">
                    @if($icon = $params->get('icon'))
                        <span class="uk-form-icon uk-icon"
                              uk-icon="icon: {{ $icon }}"></span>
                    @endif
                    <input type="password"
                           id="{{ $params->get('id') }}"
                           name="{{ $params->get('name') }}"
                           value=""
                           autocomplete="off"
                           {!! $params->get('attributes') ? " {$params->get('attributes')}" : '' !!}
                           class="uk-input uk-border-rounded{{ ($class = $params->get('class')) ? " {$class}" : '' }}{{ $error ? ' uk-form-danger' : '' }}">
                </div>
            </div>
            <div id="{{ $params->get('id') }}_object_confirmation">
                @if($label = $params->get('label_confirmation'))
                    <label for="{{ $params->get('id') }}_confirmation"
                           class="uk-form-label">
                        {!! $label !!}
                        @if($params->get('required'))
                            <span class="uk-text-danger">*</span>
                        @endif
                    </label>
                @endif
                <div class="uk-inline uk-width-1-1">
                    @if($icon = $params->get('icon'))
                        <span class="uk-form-icon uk-icon"
                              uk-icon="icon: {{ $icon }}"></span>
                    @endif
                    <input type="password"
                           id="{{ $params->get('id') }}_confirmation"
                           name="{{ $params->get('name_confirmation') }}"
                           value=""
                           autocomplete="off"
                           class="uk-input uk-border-rounded{{ ($class = $params->get('class')) ? " {$class}" : '' }}{{ $error ? ' uk-form-danger' : '' }}">
                </div>
            </div>
        </div>
        @if($help = $params->get('help'))
            <span class="uk-help-block uk-display-block">{!! $help !!}</span>
        @endif
    </div>
</div>
