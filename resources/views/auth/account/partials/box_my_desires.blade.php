@php
    $_desires = $item->_my_desires;
@endphp
<div class="product-like uk-position-relative">
    <div class="uk-container uk-container-large">
        <div class="product-listing uk-padding uk-padding-remove-horizontal uk-padding-remove-bottom">
            <h2 class="block-title uk-heading-bullet title-block uk-text-uppercase">
                <span>@lang('forms.my_desires')</span>
            </h2>
            @include('auth.account.partials.desires_items', compact('_desires', 'language'))
        </div>
    </div>
</div>