<div class="uk-card uk-card-default uk-card-small uk-border-rounded uk-margin-bottom">
    <div class="uk-card-header">
        <h2 class="uk-float-left uk-margin-remove">@lang('pages.reviews_page')</h2>
        <div class="uk-float-right">
            {!! _l(trans('others.viewed_all'), 'oleus.reviews') !!}
        </div>
    </div>
    <div class="uk-card-body">
        @if($items->count())
            <table class="uk-table uk-table-small uk-table-hover uk-table-middle">
                <thead>
                    <tr>
                        <th class="uk-width-medium">@lang('forms.label_first_name')</th>
                        <th>@lang('forms.label_subject')</th>
                        <th class="uk-width-xsmall uk-text-center"
                            uk-tooltip="title: {{ trans('forms.label_rating') }}">
                            <span uk-icon="icon: ui_star"></span>
                        </th>
                        <th class="uk-width-xsmall uk-text-center"
                            uk-tooltip="title: {{ trans('forms.label_checked') }}">
                            <span uk-icon="icon: ui_check"></span>
                        </th>
                        <th class="uk-width-xsmall uk-text-center"
                            uk-tooltip="title: {{ trans('forms.label_viewed') }}">
                            <span uk-icon="icon: ui_visibility"></span>
                        </th>
                        <th class="uk-width-small">
                            @lang('forms.label_publication_date')
                        </th>
                        @can('update_reviews')
                            <th class="uk-width-xsmall"></th>
                        @endcan
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $item)
                        <tr>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->subject }}</td>
                            <td class="uk-text-center">{{ $item->rating ? $item->rating : '-' }}</td>
                            <td>
                                {!! $item->check ? '<span class="uk-text-success" uk-icon="icon: ui_check" uk-tooltip="title: '. trans('others.checked') .'"></span>' : '<span class="uk-text-danger" uk-icon="icon: ui_close" uk-tooltip="title: '. trans('others.unchecked') .'"></span>' !!}
                            </td>
                            <td class="uk-text-center">
                                {!! $item->status ? '<span class="uk-text-success" uk-icon="icon: ui_visibility" uk-tooltip="title: '. trans('others.viewed') .'"></span>' : '<span class="uk-text-danger" uk-icon="icon: ui_visibility_off" uk-tooltip="title: '. trans('others.not_viewed') .'"></span>' !!}
                            </td>
                            <td>{{ $item->created_at->format('d.m.Y H:i') }}</td>
                            @can('update_reviews')
                                <td class="uk-text-center">
                                    {!! _l('', 'oleus.reviews.edit', ['p' => ['id' => $item->id], 'a' => ['class' => 'uk-button-icon uk-button uk-button-primary uk-waves uk-border-rounded',  'uk-icon' => 'icon: ui_mode_edit', 'uk-tooltip' => 'title: '. trans('forms.button_edit')]]) !!}
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
