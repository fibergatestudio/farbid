@php($_back = request('type'))

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
                {!! _l('', "oleus.{$_back}", ['a' => ['class' => 'uk-button uk-button-default uk-waves uk-button-icon uk-border-rounded', 'uk-icon' => 'icon: ui_exit_to_app', 'uk-tooltip' => 'title: '. trans('others.link_to_back')]]) !!}
            </div>
            <div class="uk-card-body">
                <dl class="uk-description-list uk-description-list-divider">
                    <dt class="uk-text-muted">@lang('forms.label_first_name')</dt>
                    <dd class="uk-text-bold">{{ $item->name }}</dd>
                    <dt class="uk-text-muted">@lang('forms.label_phone')</dt>
                    <dd class="uk-text-bold">{{ $item->phone }}</dd>
                    @if($item->type == 1)
                        <dt class="uk-text-muted">@lang('forms.label_email')</dt>
                        <dd class="uk-text-bold">{{ $item->email }}</dd>
                        <dt class="uk-text-muted">@lang('forms.label_comment')</dt>
                        <dd class="uk-text-bold">{{ $item->comment }}</dd>
                    @endif
                </dl>
            </div>
        </div>
    </article>
@endsection