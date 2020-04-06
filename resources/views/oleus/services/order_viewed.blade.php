@extends('oleus.index')

@section('page')
    <article class="uk-article uk-margin-large-bottom">
        <div class="uk-card uk-card-default uk-padding-small uk-margin-bottom uk-border-rounded">
            <h1 class="uk-article-title uk-margin-remove">
                {!! wrap()->get('page._title') !!}
            </h1>
        </div>
        <div class="uk-card uk-card-default uk-card-small uk-border-rounded">
            <div class="uk-card-header uk-text-right">
                {!! _l('', 'oleus.service_orders', ['a' => ['class' => 'uk-button uk-button-default uk-waves uk-button-icon uk-border-rounded', 'uk-icon' => 'icon: ui_exit_to_app', 'uk-tooltip' => 'title: '. trans('others.link_to_back')]]) !!}
            </div>
            <div class="uk-card-body">
                <dl class="uk-description-list uk-description-list-divider">
                    <dt class="uk-text-muted">@lang('forms.label_first_name')</dt>
                    <dd class="uk-text-bold">{{ $item->name }}</dd>
                    <dt class="uk-text-muted">@lang('forms.label_phone')</dt>
                    <dd class="uk-text-bold">{{ $item->phone }}</dd>
                    <dt class="uk-text-muted">@lang('forms.label_email')</dt>
                    <dd class="uk-text-bold">{{ $item->email }}</dd>
                    <dt class="uk-text-muted">@lang('forms.label_services')</dt>
                    <dd class="uk-text-bold">{!! $item->service_items !!}</dd>
                    <dt class="uk-text-muted">@lang('forms.label_urgently')</dt>
                    <dd class="uk-text-bold uk-text-uppercase">{!! $item->urgently ? '<span class="uk-text-danger">'. trans('others.yes') . '</span>' : '<span class="uk-text-success">'. trans('others.no') . '</span>' !!}</dd>
                    <dt class="uk-text-muted">@lang('forms.label_comment')</dt>
                    <dd class="uk-text-bold">{{ $item->comment }}</dd>
                </dl>
            </div>
        </div>
    </article>
@endsection