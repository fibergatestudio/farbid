<div id="language-choice-block">
    <ul class="uk-nav"
        id="menu-language-choice">
        @foreach($all_languages as $_id => $_item)
            <li class="uk-item uk-display-inline-block{{ $_id == $current_locale ? ' uk-active' : '' }}">
                @if($_id == $current_locale)
                    <span title="{{ $_item['full_name'] }}">
                        {{ $_item['short_name'] }}
                    </span>
                @else
                    <a href=""
                       title="{{ $_item['full_name'] }}">
                        {{ $_item['short_name'] }}
                    </a>
                @endif
            </li>
        @endforeach
    </ul>
</div>