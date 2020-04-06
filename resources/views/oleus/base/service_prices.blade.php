@isset($prices)
    <table class="uk-table uk-table-small uk-table-hover uk-table-middle uk-margin-bottom">
        <thead>
            <tr>
                <th>@lang('forms.label_name')</th>
                <th class="uk-width-small">@lang('forms.label_price')</th>
            </tr>
        </thead>
        <tbody>
            @foreach($prices as $_price)
                <tr>
                    <td>
                        {{ $_price->title }}
                        @if($_price->sub_title)
                            <div class="uk-text-muted uk-text-small">{{ $_price->sub_title }}</div>
                        @endif
                    </td>
                    <td>{{ $_price->price ? $_price->price : ' - ' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endisset
