<div class="shop-category-filter-card">
    <div class="size mb-20">
        <ul class="uk-nav-default uk-nav-parent-icon"
            uk-nav>
            @foreach($items as $_item)
                <?
                $_sub_category_alias = $_item->_alias;
                $_sub_category_alias = $_sub_category_alias->language != DEFAULT_LANGUAGE ? "{$_sub_category_alias->language}/{$_sub_category_alias->alias}" : $_sub_category_alias->alias;
                ?>
                <li>
                    <a href="{{ _u($_sub_category_alias) }}"
                       class="uk-padding-remove-left">
                        {{ $_item->title }}
                    </a>
                </li>
            @endforeach
        </ul>
    </div>
</div>