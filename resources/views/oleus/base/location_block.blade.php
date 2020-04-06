<div id="location-choice-block">
    <h4>Ваш город:</h4>
    <button class="uk-button"
            type="button">{{ $current_location['city'] }}</button>
    <div uk-dropdown="mode: click">
        <ul class="uk-nav uk-dropdown-nav">
            @foreach($all_location as $_key_location => $_data_location)
                @if($_key_location != $current_location['id'])
                    <li>
                        <a href="{{ _r('ajax.choice_location.form') }}"
                           data-location="{{ $_key_location }}"
                           class="use-ajax">{{ $_data_location['city'] }}</a>
                    </li>
                @endif
            @endforeach
        </ul>
    </div>
</div>