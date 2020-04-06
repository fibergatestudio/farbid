@php
    $order = $item->info;
@endphp

@extends('mail.mail')

@section('body')
    <h1 style="font-weight: 500; font-size: 1.3em;">
        Оформлен заказ на покупку товара
    </h1>
    <div>
        <table cellpadding="8"
               cellspacing="0"
               border="0"
               width="100%">
            <tr bgcolor="#ffde02">
                <td colspan="2">
                    <h3 style="font-size: 16px; margin: 0; color: #000; padding: 5px 0;">
                        Информация о заказчике
                    </h3>
                </td>
            </tr>
            @if($item->user_id)
                <tr>
                    <td width="300"
                        align="right">
                        <b>Имя клиента</b>
                    </td>
                    <td>{{ $item->user_name }}</td>
                </tr>
                <tr>
                    <td width="300"
                        align="right">
                        <b>Email</b>
                    </td>
                    <td>{{ $item->user_email }}</td>
                </tr>
            @else
                <tr>
                    <td width="300"
                        align="right">
                        <b>Имя клиента</b>
                    </td>
                    <td>{{ $item->name }}</td>
                </tr>
                <tr>
                    <td width="300"
                        align="right">
                        <b>Email</b>
                    </td>
                    <td>{{ $item->email }}</td>
                </tr>
            @endif
            <tr>
                <td width="300"
                    align="right">
                    <b>Номер телефона</b>
                </td>
                <td>{{ $item->phone }}</td>
            </tr>
            <tr bgcolor="#ffde02">
                <td colspan="2">
                    <h3 style="font-size: 16px; margin: 0; color: #000; padding: 5px 0;">
                        Информация заказе
                    </h3>
                </td>
            </tr>
            <tr>
                <td width="300"
                    align="right">
                    <b>Номер заказа</b>
                </td>
                <td>{{ $item->order }}</td>
            </tr>
            <tr>
                <td width="300"
                    align="right">
                    <b>Дата оформления</b>
                </td>
                <td>{{ $item->created_at->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <td width="300"
                    align="right">
                    <b>Способ доставки</b>
                </td>
                <td>{{ trans('shop.delivery_type_' . $item->delivery) }}</td>
            </tr>
            @if($item->delivery == 2)
                <tr>
                    <td width="300"
                        align="right">
                        <b>Адрес доставки</b>
                    </td>
                    <td>{{ $item->address }}</td>
                </tr>
            @elseif($item->delivery == 3)
                <tr>
                    <td width="300"
                        align="right">
                        <b>Отделение доставки</b>
                    </td>
                    <td>{{ $item->address }}</td>
                </tr>
            @endif
            <tr>
                <td width="300"
                    align="right">
                    <b>Способ оплаты</b>
                </td>
                <td>{{ trans('shop.payment_type_' . $item->payment) }}</td>
            </tr>
            <tr>
                <td width="300"
                    align="right">
                    <b>Комментарий</b>
                </td>
                <td>{!! $item->comment ?? '-' !!}</td>
            </tr>
            <tr bgcolor="#ffde02">
                <td colspan="2">
                    <h3 style="font-size: 16px; margin: 0; color: #000; padding: 5px 0;">
                        Состав заказа
                    </h3>
                </td>
            </tr>
            <tr>
                <td colspan="2">
                    <table cellpadding="8"
                           cellspacing="0"
                           border="0"
                           width="100%">
                        <thead>
                            <tr>
                                <td style="border-bottom: 2px #ddd solid;">
                                    Название товара
                                </td>
                                <td width="100"
                                    style="border-bottom: 2px #ddd solid;">
                                    Количество
                                </td>
                                <td width="100"
                                    style="border-bottom: 2px #ddd solid;">
                                    Цена
                                </td>
                                <td width="100"
                                    style="border-bottom: 2px #ddd solid; text-align: right;">
                                    Сумма
                                </td>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order['items'] as $_product)
                                @if($_product['type'] == 'product')
                                    <tr>
                                        <td style="border-bottom: 1px #ddd solid; border-right: 1px #ddd solid;">
                                            @if($_product['entity'])
                                                <a href="{{ _u($_product['entity']->_alias->alias) }}"
                                                   target="_blank">
                                                    {{ $_product['title'] }}
                                                </a>
                                            @else
                                                {{ $_product['title'] }}
                                            @endif
                                        </td>
                                        <td style="border-bottom: 1px #ddd solid; border-right: 1px #ddd solid; text-align: center;">
                                            {{ $_product['count'] }}
                                        </td>
                                        <td style="border-bottom: 1px #ddd solid; border-right: 1px #ddd solid; text-align: right;">
                                            <span>{{ $_product['price']['price']['currency']['prefix'] }}</span>
                                            {{ $_product['price']['price']['format']['view_price'] }}
                                            <span>{{ $_product['price']['price']['currency']['suffix'] }}</span>
                                        </td>
                                        <td style="border-bottom: 1px #ddd solid; text-align: right;">
                                            <span>{{ $_product['amount']['currency']['prefix'] }}</span>
                                            {{ $_product['amount']['format']['view_price'] }}
                                            <span>{{ $_product['amount']['currency']['suffix'] }}</span>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                            <tr>
                                <td colspan="3"
                                    style="font-weight: bold; text-align: right; padding-rigth: 15px; text-transform: uppercase; font-size: 14px;">
                                    @lang('shop.form_label_total_amount'):
                                </td>
                                <td style="font-size: 22px; font-weight: bold; text-align: center;">
                                    <span>{{ $order['total']['currency']['prefix'] }}</span>
                                    {{ $order['total']['format']['view_price'] }}
                                    <span>{{ $order['total']['currency']['suffix'] }}</span>
                                </td>
                            </tr>
                        <tbody>
                    </table>
                </td>
            </tr>
        </table>
    </div>
@endsection
