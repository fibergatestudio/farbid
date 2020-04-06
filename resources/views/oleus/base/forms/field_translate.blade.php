<div class="uk-form-row">
    <label class="uk-form-label">{!! $label !!}</label>
    <div class="uk-form-controls">
        <div class="uk-width-1-1"
             style="padding: 7px 0 5px">
            @php
                $icon = isset($link['add']) ? 'ui_note_add' : 'ui_link';
            @endphp
            <span class="uk-icon"
                  uk-icon="icon: {{ $icon }}"></span>
            {!! _l(truncateString($link['title'], 80, TRUE), $link['url'], ['a' => ['class' => (isset($link['add']) ? 'uk-text-success' : NULL)]]) !!}
        </div>
    </div>
</div>
