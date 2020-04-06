@php
    $_product = shop_product_load($item->product_id);
@endphp

@extends('mail.mail')

@section('body')
    <h1 style="font-weight: 500; font-size: 1.3em;">
        Заявка на покупку товара!
    </h1>
    <p>
        На сайте пользователь оставил заявку на покупку товара воспользовавшись функцией "Купить в один клик"
    </p>
    <div>
        <table cellpadding="8"
               cellspacing="0"
               border="0"
               width="100%">
            <tr bgcolor="#ffde02">
                <td colspan="2">
                    <h3 style="font-size: 16px; margin: 0; color: #000; padding: 5px 0;">
                        Информация по заказу
                    </h3>
                </td>
            </tr>
            <tr>
                <td width="300"
                    align="right">
                    <b>Имя клиента</b>
                </td>
                <td>{{ $item->name }}</td>
            </tr>
            <tr>
                <td width="300" align="right">
                    <b>Номер телефона</b>
                </td>
                <td>{{ $item->phone }}</td>
            </tr>
            <tr>
                <td width="300" align="right">
                    <b>Товар</b>
                </td>
                <td>{{ $_product->title }}</td>
            </tr>
            <tr>
                <td width="300" align="right">
                    <b>Артикул</b>
                </td>
                <td>{{ $_product->sky }}</td>
            </tr>
        </table>
    </div>
@endsection