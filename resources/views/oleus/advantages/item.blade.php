<li class="uk-item"
    id="advantage-item-{{ $item->id }}">
    <div class="uk-form-row">
        <div class="uk-grid uk-grid-collapse">
            <div class="uk-width-expand"
                 style="line-height: 30px;">
                {{ $item->id }} : {{ $item->title }}
            </div>
            <div class="uk-width-xsmall uk-text-left"
                 style="padding: 0 8px; line-height: 36px;">
                {!! $item->status ? '<span class="uk-text-success" uk-icon="icon:ui_visibility" title="'. __('pages.status_visible') .'"></span>' : '<span class="uk-text-danger" uk-icon="icon:ui_visibility_off" title="'. __('pages.status_hidden') .'"></span>' !!}
            </div>
            <div class="uk-width-xsmall uk-text-right"
                 style="line-height: 36px;">
                {!! _l('', 'oleus.advantages.item', ['p' => ['advantages' => $item->advantage_id, 'action' => 'edit', 'id' => $item->id], 'a' => ['class' => 'use-ajax uk-text-primary', 'uk-icon' => 'icon: ui_mode_edit', 'title' => __('fields.button_edit')]]) !!}
            </div>
        </div>
    </div>
</li>