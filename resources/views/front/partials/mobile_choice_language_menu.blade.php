<div id="language-choice-mobile-block">
    <ul id="menu-language-choice-mobile"
        class="uk-navbar-nav language mobile">
        @foreach($all_languages as $_id => $_item)
            @if($_id == $current_locale)
                <li class="active">
                    <span title="{{ $_item['full_name'] }}">
                        {{ $_item['short_name'] }}
                    </span>
                </li>
            @else
                <li>
                    <a href="#"
                       title="{{ $_item['full_name'] }}">
                        {{ $_item['short_name'] }}
                    </a>
                </li>
            @endif
        @endforeach
    </ul>
</div>