@extends('oleus.index')

@section('page')
    <article class="uk-article">
        <div class="uk-card uk-card-default uk-padding-small uk-margin-bottom uk-border-rounded">
            <h1 class="uk-article-title uk-margin-remove">
                {!! wrap()->get('page._title') !!}
            </h1>
        </div>
        <form class="uk-form uk-form-stacked uk-form-horizontal uk-width-1-1 uk-margin-large-bottom"
              method="POST"
              action="{{ $item->exists ? _r("oleus.{$form->route_tag}.update", ['id' => $item->id]) : _r("oleus.{$form->route_tag}.store") }}">
            {{ csrf_field() }}
            {{ $item->exists ? method_field('PUT') : method_field('POST') }}
            <div class="uk-card uk-card-default uk-card-small uk-border-rounded">
                <div class="uk-card-header uk-text-right">
                    @if($item->exists)
                        @can($form->permission['update'])
                            <button type="submit"
                                    name="save"
                                    value="1"
                                    class="uk-button uk-button-secondary uk-waves uk-border-rounded uk-margin-small-left">
                                @lang('forms.button_save')
                            </button>
                        @endcan
                    @else
                        @can($form->permission['create'])
                            <button type="submit"
                                    name="save"
                                    value="1"
                                    class="uk-button uk-button-secondary uk-waves uk-border-rounded uk-margin-small-left">
                                @lang('forms.button_add')
                            </button>
                        @endcan
                    @endif
                    @if($item->exists)
                        @can($form->permission['update'])
                            <button type="submit"
                                    name="save_close"
                                    value="1"
                                    class="uk-button uk-button-primary uk-waves uk-border-rounded uk-margin-small-left">
                                @lang('forms.button_save_and_close')
                            </button>
                        @endcan
                        @if($item->exists && $item->relation)
                            {!! _l(trans('others.link_to_back'), "oleus.{$form->route_tag}.edit", ['p' => ['id' => $item->relation], 'a' => ['class' => 'uk-button uk-button-default uk-waves uk-border-rounded uk-margin-small-left']]) !!}
                        @endif
                        @can($form->permission['delete'])
                            <button type="button"
                                    name="delete"
                                    value="1"
                                    uk-icon="icon: ui_delete_forever"
                                    uk-tooltip="title: {{ trans('forms.button_delete') }}"
                                    class="uk-button uk-button-danger uk-waves uk-button-icon uk-border-rounded uk-margin-small-left">
                            </button>
                        @endcan
                    @endif
                    {!! _l('', "oleus.{$form->route_tag}", ['a' => ['class' => 'uk-button uk-button-default uk-button-icon uk-border-rounded uk-margin-small-left', 'uk-icon' => 'icon: ui_exit_to_app']]) !!}
                </div>
                <div class="uk-card-body uk-grid-match"
                     uk-grid>
                    <div class="uk-width-1-4">
                        <ul class="uk-tab uk-tab-left"
                            uk-tab="connect: #uk-tab-body; animation: uk-animation-fade; swiping: false;">
                            @foreach($form->tabs as $tab)
                                @if($tab)
                                    <li class="{{ $loop->index == 0 ? 'uk-active' : '' }}">
                                        <a href="#">{{ $tab['title'] }}</a>
                                    </li>
                                @endif
                            @endforeach
                            @if($form->relation['count'] && $item->exists && is_null($item->relation))
                                <li>
                                    <a href="#">@lang('others.tab_related_items')</a>
                                </li>
                            @endif
                            @if($form->seo)
                                <li>
                                    <a href="#">@lang('others.tab_meta_tags')</a>
                                </li>
                            @endif
                        </ul>
                    </div>
                    <div class="uk-width-3-4">
                        <ul id="uk-tab-body"
                            class="uk-switcher uk-margin">
                            @foreach($form->tabs as $tab)
                                @if($tab)
                                    <li class="{{ $loop->index == 0 ? 'uk-active' : '' }}">
                                        @foreach($tab['content'] as $content)
                                            {!! $content !!}
                                        @endforeach
                                    </li>
                                @endif
                            @endforeach
                            @if($form->relation['count'] && $item->exists && is_null($item->relation))
                                <li>
                                    <div class="uk-text-muted uk-text-small uk-display-block uk-margin-small-bottom">
                                        <span uk-icon="icon: ui_info; ratio: .8"
                                              class="uk-margin-small-right uk-text-primary"></span>
                                        @lang('forms.help_duplicate_entity')
                                    </div>
                                    @include('oleus.base.forms.fields_group_relations', ['_item' => $item])
                                </li>
                            @endif
                            @if($form->seo)
                                <li>
                                    @include('oleus.base.forms.fields_group_meta_tags')
                                </li>
                            @endif
                        </ul>
                    </div>
                </div>
            </div>
        </form>
        @if($item->exists)
            @can($form->permission['delete'])
                <form action="{{ _r("oleus.{$form->route_tag}.destroy", ['id' => $item->id]) }}"
                      id="form-delete-object"
                      method="POST">
                    {{ csrf_field() }}
                    {{ method_field('DELETE') }}
                </form>
            @endcan
        @endif
    </article>
@endsection
