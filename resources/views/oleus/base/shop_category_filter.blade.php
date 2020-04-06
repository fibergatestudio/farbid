@if($params)
    @if($selected)
        <div class="uk-card uk-card-default uk-margin-small-bottom">
            <div class="uk-card-body uk-padding-small">
                @foreach($selected as $_par => $_checked)
                    @if($_checked['type'] == 'select')
                        @foreach($_checked['checked'] as $_option_key => $_option_value)
                            <div>{{ $_option_value }}</div>
                        @endforeach
                    @endif
                    @if($_checked['type'] == 'input_number')
                        <div>{{ "{$_checked['checked']['min']} - {$_checked['checked']['max']} {$_checked['checked']['unit']}" }}</div>
                    @endif
                @endforeach
            </div>
        </div>
    @endif
    <div class="uk-card uk-card-default uk-margin-small-bottom">
        <div class="uk-card-body uk-padding-small">
            <div>
                <ul class="uk-nav-default uk-nav-parent-icon" uk-nav>
                    @foreach($params as $_param)
                        <li class="uk-parent">
                            <a href="#"
                               class="uk-padding-remove-left uk-padding-remove-right">{{ $_param['html']['label'] }}</a>
                            <ul class="uk-nav-sub uk-padding-remove-left">
                                @foreach($_param['html']['values'] as $_value)
                                    <li class="uk-margin-small-left">{!! $_value !!}</li>
                                @endforeach
                            </ul>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>
@endif