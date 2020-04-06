@extends('oleus.index')

@section('page')
    <article class="uk-article uk-margin-large-bottom">
        <div class="uk-card uk-card-default uk-padding-small uk-margin-bottom uk-border-rounded">
            <h1 class="uk-article-title uk-margin-remove">
                {!! wrap()->get('page._title') !!}
            </h1>
        </div>
        <div class="uk-card uk-card-default uk-card-small uk-border-rounded">
            @can('create_roles')
                <div class="uk-card-header uk-text-right">
                    {!! _l(trans('forms.button_add'), 'oleus.roles.create', ['a' => ['class' => 'uk-button uk-button-secondary uk-waves uk-border-rounded']]) !!}
                </div>
            @endcan
            <div class="uk-card-body">
                @if($items->count())
                    <table class="uk-table uk-table-small uk-table-hover uk-table-middle uk-table-divider uk-table-small">
                        <thead>
                            <tr>
                                <th class="uk-width-medium">@lang('forms.label_machine_name')</th>
                                <th>@lang('forms.label_name')</th>
                                <th class="uk-width-xsmall uk-text-center"
                                    uk-tooltip="title: {{ trans('forms.label_count_users_who_have_a_role') }}">
                                    <span uk-icon="icon: ui_person"></span>
                                </th>
                                <th class="uk-width-xsmall uk-text-center"
                                    uk-tooltip="title: {{ trans('forms.label_count_permissions') }}">
                                    <span uk-icon="icon: ui_fiber_pin"></span>
                                </th>
                                @can('update_roles')
                                    <th class="uk-width-xsmall"></th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                                <tr>
                                    <td>
                                        {{ $item->name }}
                                    </td>
                                    <td>
                                        {{ trans($item->display_name) }}
                                    </td>
                                    <td class="uk-text-center">
                                        {{ $item->count_users }}
                                    </td>
                                    <td class="uk-text-center">
                                        {{ $item->permissions->count() }}
                                    </td>
                                    @can('update_roles')
                                        <td class="uk-text-center">
                                            {!! _l('', 'oleus.roles.edit', ['p' => ['id' => $item->id], 'a' => ['class' => 'uk-text-primary',  'uk-icon' => 'icon: ui_mode_edit', 'uk-tooltip' => 'title: '. trans('forms.button_edit')]]) !!}
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