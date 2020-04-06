@if(is_null($params))
    <div
        class="uk-alert uk-alert-warning uk-border-rounded uk-margin-small-top uk-margin-remove-bottom">@lang('forms.help_product_params_is_empty')</div>
@else
    @foreach($params as $_param)
        {!! $_param->view !!}
    @endforeach
@endif