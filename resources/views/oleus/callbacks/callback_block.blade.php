<div class="uk-card uk-card-default uk-card-small uk-border-rounded uk-margin-bottom">
    <div class="uk-card-header">
        <h2 class="uk-float-left uk-margin-remove">@lang('pages.callbacks_page')</h2>
        <div class="uk-float-right">
            {!! _l(trans('others.viewed_all'), 'oleus.callbacks') !!}
        </div>
    </div>
    <div class="uk-card-body">
        @if($items->count())
            <table class="uk-table uk-table-small uk-table-hover uk-table-middle">
                <thead>
                    <tr>
                        <th>@lang('forms.label_first_name')</th>
                        <th class="uk-width-medium">@lang('forms.label_phone')</th>
                        <th class="uk-width-expand">@lang('forms.label_application_date')</th>
                        <th class="uk-width-xsmall uk-text-center"
                            uk-tooltip="title: {{ trans('forms.label_viewed') }}">
                            <span uk-icon="icon: ui_visibility"></span>
                        </th>
                        @can('viewed_callback')
                            <th class="uk-width-xsmall"></th>
                        @endcan
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                        <tr>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->phone }}</td>
                            <td class="uk-text-nowrap">{{ $item->created_at->format('d/m/Y H:i') }}</td>
                            <td class="uk-text-center">
                                {!! $item->status ? '<span class="uk-text-success" uk-icon="icon: ui_visibility" uk-tooltip="title: '. trans('others.visible') .'"></span>' : '<span class="uk-text-danger" uk-icon="icon: ui_visibility_off" uk-tooltip="title: '. trans('others.hidden') .'"></span>' !!}
                            </td>
                            @can('viewed_callback')
                                <td class="uk-text-center">
                                    {!! _l('', 'oleus.callbacks.show', ['p' => ['id' => $item->id, 'type' => 'callbacks'], 'a' => ['class' => 'uk-button-icon uk-button uk-button-secondary uk-waves uk-border-rounded',  'uk-icon' => 'icon: ui_visibility', 'uk-tooltip' => 'title: '. trans('forms.button_view')]]) !!}
                                </td>
                            @endcan
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @else
            <div class="uk-alert uk-alert-warning uk-border-rounded">
                @lang('others.no_items')
            </div>
        @endif
    </div>
</div>
