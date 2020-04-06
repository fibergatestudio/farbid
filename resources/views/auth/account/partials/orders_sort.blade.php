@php
    $_years = $item->order_years;
    $_choice_year = isset($_year) ? $_year :  Session::get('view_user_order_year', date('Y'));
    $_choice_month = isset($_month) ? $_month : Session::get('view_user_order_month', date('F'));
    $_months = [
        'January',
        'February',
        'March',
        'April',
        'May',
        'June',
        'July',
        'August',
        'September',
        'October',
        'November',
        'December'
    ]
@endphp
@if($_years->isNotEmpty())
    <div id="account-orders-sort-view"
         class="sort-my-orders">
        <div class="filter-price sort">
            <div class="uk-display-inline-block uk-margin-small-right">
                <button class="uk-button uk-button-default"
                        type="button">
                    @lang("others.{$_choice_month}")
                </button>
                <div uk-dropdown="mode: click"
                     class="uk-dropdown">
                    <ul class="uk-list">
                        @foreach($_months as $_month)
                            @if($_month == $_choice_month)
                                <li class="uk-active">
                                    <span>@lang("others.{$_month}")</span>
                                </li>
                            @else
                                <li class="">
                                    <button type="button"
                                            data-path="{{ _r('ajax.shop.view_orders') }}"
                                            data-month="{{ $_month }}"
                                            class="use-ajax uk-button-link">
                                        @lang("others.{$_month}")
                                    </button>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>
            <div class="uk-display-inline-block">
                <button class="uk-button uk-button-default"
                        type="button">
                    {{ $_choice_year }}
                </button>
                <div uk-dropdown="mode: click"
                     class="uk-dropdown">
                    <ul class="uk-list">
                        @foreach($_years as $_year)
                            @if($_year == $_choice_year)
                                <li class="uk-active">
                                    <span>{{ $_year }}</span>
                                </li>
                            @else
                                <li class="">
                                    <button type="button"
                                            data-path="{{ _r('ajax.shop.view_orders') }}"
                                            data-year="{{ $_year }}"
                                            class="use-ajax uk-button-link">
                                        {{ $_year }}
                                    </button>
                                </li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endif