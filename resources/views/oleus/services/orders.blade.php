@extends('oleus.index')

@section('page')
    <article class="uk-article uk-margin-large-bottom">
        <div class="uk-card uk-card-default uk-padding-small uk-margin-bottom uk-border-rounded">
            <h1 class="uk-article-title uk-margin-remove">
                {!! wrap()->get('page._title') !!}
            </h1>
        </div>
        <div class="uk-card uk-card-default uk-card-small uk-border-rounded">
            <div class="uk-card-body">
                @if($items->count())
                    <table class="uk-table uk-table-small uk-table-hover uk-table-middle">
                        <thead>
                            <tr>
                                <th>@lang('forms.label_first_name')</th>
                                <th class="uk-width-medium">@lang('forms.label_phone')</th>
                                <th class="uk-width-medium">@lang('forms.label_services')</th>
                                <th class="uk-width-expand">@lang('forms.label_application_date')</th>
                                <th class="uk-width-xsmall uk-text-center"
                                    uk-tooltip="title: {{ trans('forms.label_urgently') }}">
                                    <span uk-icon="icon: ui_whatshot"></span>
                                </th>
                                <th class="uk-width-xsmall uk-text-center"
                                    uk-tooltip="title: {{ trans('forms.label_viewed') }}">
                                    <span uk-icon="icon: ui_visibility"></span>
                                </th>
                                @can('viewed_service_orders')
                                    <th class="uk-width-xsmall"></th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($items as $item)
                                <tr>
                                    <td>{{ $item->name }}</td>
                                    <td>{{ $item->phone }}</td>
                                    <td>{!! $item->service_items !!}</td>
                                    <td class="uk-text-nowrap">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="uk-text-center">
                                        {!! $item->urgently ? '<span class="uk-text-success" uk-icon="icon: ui_done" uk-tooltip="title: '. trans('others.urgently') .'"></span>' : '<span class="uk-text-danger" uk-icon="icon: ui_close" uk-tooltip="title: '. trans('others.do_not_rush') .'"></span>' !!}
                                    </td>
                                    <td class="uk-text-center">
                                        {!! $item->status ? '<span class="uk-text-success" uk-icon="icon: ui_visibility" uk-tooltip="title: '. trans('others.visible') .'"></span>' : '<span class="uk-text-danger" uk-icon="icon: ui_visibility_off" uk-tooltip="title: '. trans('others.hidden') .'"></span>' !!}
                                    </td>
                                    @can('viewed_service_orders')
                                        <td class="uk-text-center">
                                            {!! _l('', 'oleus.service_orders.show', ['p' => ['id' => $item->id], 'a' => ['class' => 'uk-button-icon uk-button uk-button-secondary uk-waves uk-border-rounded',  'uk-icon' => 'icon: ui_visibility', 'uk-tooltip' => 'title: '. trans('forms.button_view')]]) !!}
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
                    <div class="uk-alert uk-alert-warning uk-border-rounded">
                        @lang('others.no_items')
                    </div>
                @endif
            </div>
        </div>
    </article>
@endsection