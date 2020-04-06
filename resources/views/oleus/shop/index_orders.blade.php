@extends('oleus.index')

@section('page')
    <article class="uk-article uk-margin-large-bottom">
        <div class="uk-card uk-card-default uk-padding-small uk-margin-bottom uk-border-rounded">
            <h1 class="uk-article-title uk-margin-remove">
                {!! wrap()->get('page._title') !!}
            </h1>
        </div>
        <div class="uk-card uk-card-default uk-card-small uk-border-rounded">
            <div class="uk-card-body">
                @if($items->count())
                    <table
                        id="orders"
                        class="uk-table uk-table-small uk-table-hover uk-table-middle uk-table-divider uk-table-small">
                        <thead>
                            <tr>
                                <th class="uk-width-xsmall uk-text-center"
                                    uk-tooltip="title: {{ trans('forms.label_id') }}">
                                    <span uk-icon="icon: ui_more_horiz"></span>
                                </th>
                                <th>@lang('forms.label_first_name')</th>
                                <th class="uk-width-medium">@lang('forms.label_phone')</th>
                                <th class="uk-text-right"
                                    width="200">@lang('forms.label_amount')</th>
                                <th width="100"
                                    class="uk-text-center">@lang('forms.label_status')</th>
                                @can('update_shop_order')
                                    <th class="uk-width-xsmall"></th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                                <? $_data_item = unserialize($item->data) ?>
                                <tr class="{{ "order-status-{$item->status}" }}">
                                    <td>{{ $item->id }}</td>
                                    <td>{{ $item->user_name ?? $item->name }}</td>
                                    <td>{{ $item->phone }}</td>
                                    <td class="uk-text-right">
                                        {!! isset($_data_item['total']) && $_data_item['total'] ? '<span class="uk-text-bold">'. $_data_item['total']['currency']['prefix'] . ' ' .  $_data_item['total']['format']['view_price'] . ' '. $_data_item['total']['currency']['suffix'] . '</span>' : trans('others.not_indicated') !!}
                                    </td>
                                    <td class="uk-text-center">{{ trans("shop.order_status_{$item->status}") }}</td>
                                    <td>
                                        {!! _l('', 'oleus.shop_orders.edit', ['p' => ['id' => $item->id], 'a' => ['class' => 'uk-text-primary',  'uk-icon' => 'icon: ui_mode_edit', 'uk-tooltip' => 'title: '. trans('forms.button_edit')]]) !!}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="uk-alert uk-alert-warning uk-border-rounded">
                        @lang('others.no_items')
                    </div>
                @endif
            </div>
        </div>
    </article>
@endsection
