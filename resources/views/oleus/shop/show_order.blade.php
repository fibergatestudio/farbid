@if($order)
<table
    class="uk-table uk-table-small uk-table-hover uk-table-middle uk-table-divider uk-table-small uk-table-justify">
    <thead>
    <tr>
        <td>@lang('forms.label_name_product')</td>
        <td width="100">@lang('forms.label_count')</td>
        <td width="100">@lang('forms.label_price')</td>
        <td width="100"
            class="uk-text-right">@lang('forms.label_amount')</td>
    </tr>
    </thead>
    <tbody>
    @foreach($order['items'] as $_product)
        @if($_product['type'] == 'product')
            <tr>
                <td>
                    @if($_product['entity'])
                        <a href="{{ _u($_product['entity']->_alias->alias) }}"
                           target="_blank">
                            {{ $_product['title'] }}
                        </a>
                    @else
                        {{ $_product['title'] }}
                    @endif
                </td>
                <td>
                    {{ $_product['count'] }}
                </td>
                <td>
                    <span>{{ $_product['price']['price']['currency']['prefix'] }}</span>
                    {{ $_product['price']['price']['format']['view_price'] }}
                    <span>{{ $_product['price']['price']['currency']['suffix'] }}</span>
                </td>
                <td class="uk-text-right">
                    <span>{{ $_product['amount']['currency']['prefix'] }}</span>
                    {{ $_product['amount']['format']['view_price'] }}
                    <span>{{ $_product['amount']['currency']['suffix'] }}</span>
                </td>
            </tr>
        @else
            <tr>
                <td colspan="4">
                    <table class="uk-table uk-table-small uk-table-middle uk-table-small">
                        <tbody>
                        @foreach($_product['products'] as $type => $_product_inside)
                            <tr>
                                <td>
                                    @if($_product_inside['entity'])
                                        <a href="{{ _u($_product_inside['entity']->_alias->alias) }}"
                                           target="_blank">
                                            {{ $_product_inside['title'] }}
                                        </a>
                                    @else
                                        {{ $_product_inside['title'] }}
                                    @endif
                                </td>
                                @if($loop->index == 0)
                                    <td width="100"
                                        rowspan="2">
                                        {{ $_product['count'] }}
                                    </td>
                                @endif
                                <td width="100">
                                    @if($type == 'primary')
                                        <span>{{ $_product_inside['price']['price']['currency']['prefix'] }}</span>
                                        {{ $_product_inside['price']['price']['format']['view_price'] }}
                                        <span>{{ $_product_inside['price']['price']['currency']['suffix'] }}</span>
                                    @else
                                        <span>{{ $_product_inside['discount_price']['price']['currency']['prefix'] }}</span>
                                        {{ $_product_inside['discount_price']['price']['format']['view_price'] }}
                                        <span>{{ $_product_inside['discount_price']['price']['currency']['suffix'] }}</span>
                                    @endif
                                </td>
                                @if($loop->index == 0)
                                    <td width="100"
                                        rowspan="2"
                                        class="uk-text-right">
                                        <span>{{ $_product['amount']['currency']['prefix'] }}</span>
                                        {{ $_product['amount']['format']['view_price'] }}
                                        <span>{{ $_product['amount']['currency']['suffix'] }}</span>
                                    </td>
                                @endif
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </td>
            </tr>
        @endif
    @endforeach
    <tr>
        <td colspan="3"
            class="uk-text-right uk-text-bold">
            @lang('shop.form_label_total_amount'):
        </td>
        <td class="uk-text-bold uk-text-right uk-text-primary">
            <span>{{ $order['total']['currency']['prefix'] }}</span>
            {{ $order['total']['format']['view_price'] }}
            <span>{{ $order['total']['currency']['suffix'] }}</span>
        </td>
    </tr>
    </tbody>
</table>
@else
<div class="uk-alert uk-alert-warning">
    Во время записи данных в таблицу произошел сбой.
</div>
@endif