@php
    $_level = isset($level) ? $level + 1 : 0;
    $_level_padding_left = $_level * 25;
@endphp
<tr>
    <td class="uk-text-center uk-text-bold">{{ $item->id }}</td>
    <td style="padding-left: {{ "{$_level_padding_left}px" }}">
        @if($_level)
            <span uk-icon="icon : ui_subdirectory_arrow_right; ratio: .8"
                  class="uk-position-relative"
                  style="top: -3px;"></span>
        @endif
        {!! $item->title !!}
    </td>
    <td>{{ $item->sort }}</td>
    <td class="uk-text-center">
        {!! $item->status ? '<span class="uk-text-success" uk-icon="icon: ui_visibility" uk-tooltip="title: '. trans('others.visible') .'"></span>' : '<span class="uk-text-danger" uk-icon="icon: ui_visibility_off" uk-tooltip="title: '. trans('others.hidden') .'"></span>' !!}
    </td>
    <td class="uk-text-center">
        {!! _l('', 'oleus.menus.item', ['p' => ['menu' => $item->menu_id, 'action' => 'edit', 'id' => $item->id], 'a' => ['class' => 'use-ajax uk-text-primary', 'uk-icon' => 'icon: ui_mode_edit', 'title' => trans('fields.button_edit')]]) !!}
    </td>
</tr>
@if($item->_children->isNotEmpty())
    @foreach($item->_children as $_child)
        @include('oleus.menus.item', [
            'item' => $_child,
            'level' => $_level,
        ])
    @endforeach
@endif