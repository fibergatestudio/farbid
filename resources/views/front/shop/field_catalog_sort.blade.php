@php
    $_wrap = wrap()->get();
    $_request_query = $_wrap['shop_filter_page_query'] ?? [];
    $_choice_sort =  $_wrap['shop_filter_sort'] ?? 'name_asc';
    $_alias = $_wrap['alias'];
    $_sorts_list = [
        'price_asc' => __('Цена по возрастанию'),
        'price_desc' => __('Цена по убыванию'),
        'name_asc' => __('По названию'),
        //'name_desc' => 'Сортировать по названию <span uk-icon="arrow-down"></span>',
        //'popular_asc' => 'По популярности',
        'popular_desc' => __('По популярности'),
        // 'new_asc' => 'Сортировать новые <span uk-icon="arrow-up"></span>',
        // 'new_desc' => 'Сортировать новые <span uk-icon="arrow-down"></span>',
        // 'discount_asc' => 'Сортировать акционные <span uk-icon="arrow-up"></span>',
        // 'discount_desc' => 'Сортировать акционные <span uk-icon="arrow-down"></span>',
    ];
@endphp
<div class="filter-price sort">
    <div class="uk-visible@m">
        <button class="uk-button uk-button-default"
                type="button">
            {!! $_sorts_list[$_choice_sort] !!}
        </button>
        <div uk-dropdown="mode: click">
            <ul class="uk-list">
                @foreach($_sorts_list as $_sort_key => $_sort_data)
                    @if($_sort_key == $_choice_sort)
                        <li class="uk-active">
                            <span>{!! $_sort_data !!}</span>
                        </li>
                    @else
                        <li class="{{ $_sort_key == $_choice_sort ? 'uk-active' : '' }}">
                            @php($_request_query['sort'] = $_sort_key)
                            <button type="button"
                                    data-path="{{ _u(request()->url()) . formalize_url_query($_request_query) }}"
                                    data-view_load="0"
                                    class="use-ajax uk-button-link">
                                {!! $_sort_data !!}
                            </button>
                        </li>
                    @endif
                @endforeach
            </ul>
        </div>
    </div>
    <div class="uk-hidden@m">
        <ul class="uk-list">
            @foreach($_sorts_list as $_sort_key => $_sort_data)
                @if($_sort_key == $_choice_sort)
                    <li class="uk-active">
                        <span>{!! $_sort_data !!}</span>
                    </li>
                @else
                    <li class="{{ $_sort_key == $_choice_sort ? 'uk-active' : '' }}">
                        @php($_request_query['sort'] = $_sort_key)
                        <button type="button"
                                data-path="{{ _u(request()->url()) . formalize_url_query($_request_query) }}"
                                data-view_load="0"
                                class="use-ajax uk-button-link">
                            {!! $_sort_data !!}
                        </button>
                    </li>
                @endif
            @endforeach
        </ul>
    </div>
</div>