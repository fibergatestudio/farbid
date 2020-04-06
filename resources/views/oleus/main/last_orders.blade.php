<div class="uk-card uk-card-default uk-card-small uk-radius uk-radius-5 uk-margin-bottom">
    <div class="uk-card-header uk-h3">
        Последние заказы
    </div>
    <div class="uk-card-body">
        @if($items->isNotEmpty())
            <table class="uk-table uk-table-small uk-table-hover uk-table-middle uk-table-divider uk-table-small">
                <thead>
                    <tr>
                        <th class="uk-width-xsmall">id</th>
                        <th>{{ trans('forms.label_user_name') }}</th>
                        <th>{{ trans('forms.label_phone') }}</th>
                        <th class="uk-text-right"
                            width="200">@lang('forms.label_amount')</th>
                        <th width="110">Дата</th>
                        <th width="100"
                            class="uk-text-center">@lang('forms.label_status')</th>
                        <th class="uk-width-xsmall"></th>
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
                            <td>{{ $item->created_at->format('d.m.Y H:i') }}</td>
                            <td class="uk-text-center">{{ trans("shop.order_status_{$item->status}") }}</td>
                            <td>
                                {!! _l('', 'oleus.shop_orders.edit', ['p' => ['id' => $item->id], 'a' => ['class' => 'uk-text-success',  'uk-icon' => 'icon: ui_visibility']]) !!}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="uk-alert uk-alert-warning uk-border-rounded">
                {{ trans('others.no_items') }}
            </div>
        @endif
    </div>
</div>