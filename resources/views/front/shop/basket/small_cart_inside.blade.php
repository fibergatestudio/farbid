<div id="card-basket-inside"
     class="uk-visible@s">
    @if($_count)
        <span class="pull-left">{{ plural_string($_count,[trans('others.plural_shop_product'), trans('others.plural_shop_products'), trans('others.plural_shop_products2')], FALSE) }} на&nbsp;</span>
        <span class="pull-right">
            <span class="price-box">
                <?= ($_basket['total']['currency']['prefix'] ? "{$_basket['total']['currency']['prefix']}&nbsp;" : NULL ), $_basket['total']['format']['view_price'], ($_basket['total']['currency']['suffix'] ? "&nbsp;{$_basket['total']['currency']['suffix']}" : NULL) ?>
            </span>
        </span>
    @else
        <div class="basket-empty">@lang('forms.basket_empty')</div>
    @endif
</div>
