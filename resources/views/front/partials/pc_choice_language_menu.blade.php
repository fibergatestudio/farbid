<div id="language-choice-pc-block">
    <ul id="menu-language-choice-pc"
        class="uk-navbar-nav language">
        @foreach($all_languages as $_id => $_item)
            @if($_id == $current_locale)
                <li class="active">
                    <span uk-tooltip
                          title="{{ $_item['full_name'] }}">
                        {{ $_item['short_name'] }}
                    </span>
                </li>
            @else
                <li>
                    <a href="#"
                       uk-tooltip
                       title="{{ $_item['full_name'] }}">
                        {{ $_item['short_name'] }}
                    </a>
                </li>
            @endif
        @endforeach
    </ul>
</div>