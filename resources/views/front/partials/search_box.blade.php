<div class="uk-grid-collapse"
     uk-grid>
    <div class="uk-width-3-4@m uk-flex-last@m">
        {!! $_response['content'] !!}
    </div>
    <div class="uk-width-1-4@m uk-flex-first@m uk-flex uk-flex-column uk-flex-between search-left-bar">
        @if(count($_items['categories']))
            <div>
                <div class="search-cat">
                    @lang('forms.products_found_in_categories')
                </div>
                <ul class="list-cat">
                    @foreach($_items['categories'] as $_category)
                        <li>
                            <a href="{{ _u($_category->_alias->alias) }}"
                               title="{{ $_category->title }}">
                                {{ $_category->title }}
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="search-history">
            <button id="search-history"
                    type="button"
                    class="uk-button uk-button-default uk-width-1-1">
                @lang('forms.search_history')
            </button>
        </div>
    </div>
</div>