<div class="my-orders">
    <div class="uk-container uk-container-large">
        <div class="uk-flex uk-flex-between uk-flex-middle">
            <h2 class="uk-heading-bullet title-block uk-text-uppercase uk-margin-top">
                @lang('forms.my_orders')
            </h2>
           @include('auth.account.partials.orders_sort')
        </div>
        @include('auth.account.partials.orders_items')
    </div>
</div>