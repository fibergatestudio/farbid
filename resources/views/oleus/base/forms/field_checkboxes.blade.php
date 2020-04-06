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
    <div class="uk-form-controls uk-form-controls-text uk-form-controls-checkbox">
        @php
            $selected = $params->get('selected');
        @endphp
        @foreach($params->get('values') as $item_key => $item_value)
            <label class="uk-text-small"
                   for="{{ $params->get('id') ."-{$item_key}" }}">
                <div style="margin-bottom: 5px;">
                    <input name="{{ $params->get('name') }}[{{ $item_key }}]"
                           type="checkbox"
                           id="{{ $params->get('id')."-{$item_key}" }}"
                           class="uk-checkbox{{ ($error = $params->get('error')) ? ' uk-form-danger' : '' }}"
                           value="1"
                        {!! $params->get('attributes') ? " {$params->get('attributes')}" : '' !!}
                        {{ $selected && is_array($selected) && in_array($item_key, $selected) ? ' checked' : '' }}>{!! $item_value !!}
                </div>
            </label>
        @endforeach
        @if($help = $params->get('help'))
            <span class="uk-help-block uk-display-block">{!! $help !!}</span>
        @endif
    </div>
</div>
