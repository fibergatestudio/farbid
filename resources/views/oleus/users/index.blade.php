@extends('oleus.index')

@section('page')
    <article class="uk-article uk-margin-large-bottom">
        <div class="uk-card uk-card-default uk-padding-small uk-margin-bottom uk-border-rounded">
            <h1 class="uk-article-title uk-margin-remove">
                {!! wrap()->get('page._title') !!}
            </h1>
        </div>
        <div class="uk-card uk-card-default uk-card-small uk-border-rounded">
            @can('create_users')
                <div class="uk-card-header uk-text-right">
                    {!! _l(trans('forms.button_add'), 'oleus.users.create', ['a' => ['class' => 'uk-button uk-button-secondary uk-waves uk-border-rounded']]) !!}
                </div>
            @endcan
            <div class="uk-card-body">
                @if($items->count())
                    <table class="uk-table uk-table-small uk-table-hover uk-table-middle">
                        <thead>
                            <tr>
                                <th>@lang('forms.label_user_name')</th>
                                <th class="uk-width-medium">@lang('forms.label_login')</th>
                                <th class="uk-width-medium">Email</th>
                                <th class="uk-width-small uk-text-center">@lang('forms.label_role')</th>
                                <th class="uk-width-xsmall uk-text-center"
                                    uk-tooltip="title: {{ trans('forms.label_email_confirmed') }}">
                                    <span uk-icon="icon: ui_contact_mail"></span>
                                </th>
                                <th class="uk-width-xsmall uk-text-center"
                                    uk-tooltip="title: {{ trans('forms.label_blocked') }}">
                                    <span uk-icon="icon: ui_block"></span>
                                </th>
                                @can('update_users')
                                    <th class="uk-width-xsmall"></th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                                <tr>
                                    <td>
                                        {{ $item->_profile->full_name }}
                                    </td>
                                    <td>
                                        {{ $item->name }}
                                    </td>
                                    <td>
                                        {{ $item->email }}
                                    </td>
                                    <td class="uk-text-center">
                                        {!! trans($item->view_role) !!}
                                    </td>
                                    <td class="uk-text-center">
                                        {!! $item->active ? '<span class="uk-text-success" uk-icon="icon: ui_done" uk-tooltip="title: '. trans('forms.label_unblocked') .'"></span>' : '<span class="uk-text-danger" uk-icon="icon: ui_close" uk-tooltip="title: '. trans('forms.label_blocked') .'"></span>' !!}
                                    </td>
                                    <td class="uk-text-center">
                                        {!! $item->blocked ? '<span class="uk-text-success" uk-icon="icon: ui_done" uk-tooltip="title: '. trans('forms.label_unblocked') .'"></span>' : '<span class="uk-text-danger" uk-icon="icon: ui_close" uk-tooltip="title: '. trans('forms.label_blocked') .'"></span>' !!}
                                    </td>
                                    @can('update_users')
                                        <td class="uk-text-center">
                                            {!! _l('', 'oleus.users.edit', ['p' => ['id' => $item->id], 'a' => ['class' => 'uk-text-primary',  'uk-icon' => 'icon: ui_mode_edit', 'uk-tooltip' => 'title: '. trans('forms.button_edit')]]) !!}
                                        </td>
                                    @endcan
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="uk-clearfix">
                        {{ $items->links('oleus.base.pagination-default') }}
                    </div>
                @else
                    <div class="uk-alert uk-alert-warning">
                        @lang('others.no_items')
                    </div>
                @endif
            </div>
        </div>
    </article>
@endsection