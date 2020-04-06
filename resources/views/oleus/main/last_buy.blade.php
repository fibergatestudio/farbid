<div class="uk-card uk-card-default uk-card-small uk-radius uk-radius-5 uk-margin-bottom">
    <div class="uk-card-header uk-h3">
        Купить в один клик
    </div>
    <div class="uk-card-body">
        @if($items->isNotEmpty())
            <table class="uk-table uk-table-small uk-table-hover uk-table-middle uk-table-divider uk-table-small">
                <thead>
                <tr>
                    <th class="uk-width-medium">
                        {{ trans('forms.label_user_name') }}
                    </th>
                    <th class="uk-width-medium">
                        {{ trans('forms.label_user_name') }}
                    </th>
                    <th>
                        Товар
                    </th>
                    <th class="uk-width-expand">{{ trans('forms.label_application_date') }}</th>
                    <th class="uk-width-xsmall"></th>
                </tr>
                </thead>
                <tbody>
                @foreach($items as $item)
                    <tr>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->phone }}</td>
                        <td>{!! _l($item->_product->title, 'oleus.shop_products.edit', ['p' => ['id' => $item->_product->id], 'a' => ['target' => '_blank']]) !!}</td>
                        <td class="uk-text-nowrap">{{ $item->created_at->format('d/m/Y H:i:s') }}</td>
                        <td class="uk-text-right">
                            {!! _l('', 'oleus.shop_products_form_buy_one_click.edit', ['p' => ['id' => $item->id], 'a' => ['class' => 'uk-text-success',  'uk-icon' => 'icon: ui_visibility']]) !!}
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