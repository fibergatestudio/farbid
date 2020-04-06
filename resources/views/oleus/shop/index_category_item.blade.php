@php
    $_level = isset($level) ? $level + 1 : 0;
    $_level_padding_left = $_level * 25 + ($_level != 1 ? 12 : 0);
@endphp
<tr>
    <td class="uk-text-center uk-text-bold">{{ $item->id }}</td>
    <td style="padding-left: {{ "{$_level_padding_left}px" }}">
        @if($_level)
            <span uk-icon="icon : ui_subdirectory_arrow_right; ratio: .8"
                  class="uk-position-relative"
                  style="top: -3px;"></span>
        @endif
        {!! _l($item->title, 'oleus.shop_categories.edit', ['p' => ['id' => $item->id]]) !!}
    </td>
    <td>{{ $item->sort }}</td>
    @if($percentage_of_relation->count)
        <td class="uk-text-center uk-text-small uk-text-primary">
            {{ $item->percentage_of_relation }}%
        </td>
    @endif
    {{--<td>--}}
        {{--{!! $item->_alias ? _l('', $item->_alias->alias, ['a' => ['uk-tooltip' => "title: {$item->title}", 'target' => '_blank', 'uk-icon' => 'icon: ui_link', 'class' => 'uk-text-primary']]) : '-' !!}--}}
    {{--</td>--}}
    <td class="uk-text-center">
        {!! $item->status ? '<span class="uk-text-success" uk-icon="icon: ui_visibility" uk-tooltip="title: '. trans('others.visible') .'"></span>' : '<span class="uk-text-danger" uk-icon="icon: ui_visibility_off" uk-tooltip="title: '. trans('others.hidden') .'"></span>' !!}
    </td>
    @can('update_shop_categories')
        <td class="uk-text-center">
            {!! _l('', 'oleus.shop_categories.edit', ['p' => ['id' => $item->id], 'a' => ['class' => 'uk-text-primary',  'uk-icon' => 'icon: ui_mode_edit', 'uk-tooltip' => 'title: '. trans('forms.button_edit')]]) !!}
        </td>
    @endcan
</tr>
@if($_children = $item->children)
    @foreach($_children as $_child)
        @include('oleus.shop.index_category_item', [
            'item' => $_child,
            'level' => $_level,
        ])
    @endforeach
@endif