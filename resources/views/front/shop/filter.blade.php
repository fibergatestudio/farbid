<div class="shop-category-filter-card">
    @if($params)
        <div class="filter-category sidebar-contant">
            @if($selected)
                <div class="uk-card uk-margin-small-bottom uk-padding-remove selected-options"
                     id="shop-category-selected-options">
                    <h5>
                        <span>
                            @lang('others.is_selected')
                        </span>
                    </h5>
                    <div
                        class="uk-card-body uk-padding-small uk-padding-remove-top uk-padding-remove-horizontal size mb-20">
                        <ul class="uk-list">
                            @foreach($selected as $_par => $_checked)
                                @if($_checked['type'] == 'select')
                                    @foreach($_checked['checked'] as $_option_key => $_option_value)
                                        <li>
                                            <a href="{{ $_option_value['alias'] }}"
                                               data-view_load="0"
                                               rel="nofollow"
                                               class="uk-padding-remove use-ajax">
                                                 <span class="uk-icon"
                                                       uk-icon="icon: close"></span>
                                                <span class="uk-link-name">{{ $_option_value['name'] }}</span>
                                            </a>
                                        </li>
                                    @endforeach
                                @endif
                                @if($_checked['type'] == 'input_number')
                                    <li>
                                        <a href="{{ $_checked['checked']['alias'] }}"
                                           data-view_load="0"
                                           class="uk-padding-remove use-ajax">
                                            <span class="uk-icon"
                                                  uk-icon="icon: close"></span>
                                            <span
                                                class="uk-link-name">{!! "{$_checked['checked']['min']} - {$_checked['checked']['max']} <span class='suffix'>{$_checked['checked']['unit']}</span>" !!}</span>
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                            <li>
                                <div class="uk-text-right">
                                    <a href="{{ _u($item->_alias->alias) }}"
                                       data-view_load="0"
                                       rel="nofollow"
                                       class="uk-padding-remove use-ajax clear-all uk-display-inline-block">
                                         <span class="uk-icon"
                                               uk-icon="icon: trash"></span>
                                        <span class="uk-link-name">@lang('shop.clear_all')</span>
                                    </a>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            @endif
            <form action=""
                  data-category="{{ $item->class }}"
                  class="shop-category-filter">
                <div class="size mb-20">
                    @foreach($params as $_param)
                        <h5 class="inner-title">
                            {{ $_param['html']['label'] }}
                        </h5>
                        @if(isset($_param['name']) && $_param['name'] == 'color')
                            @foreach($_param['html']['values'] as $_value)
                                @if($count_selected_params >= 3)
                                    @php
                                        $_value = str_replace("rel=''", "rel='nofollow'", $_value)
                                    @endphp
                                @endif
                                {!! $_value !!}
                            @endforeach
                        @else
                            <ul class="uk-list item-param-filter">
                                @foreach($_param['html']['values'] as $_value)
                                    @if($count_selected_params >= 3)
                                        @php
                                            $_value = str_replace("rel=''", "rel='nofollow'", $_value)
                                        @endphp
                                    @endif
                                    <li>{!! $_value !!}</li>
                                @endforeach
                            </ul>
                        @endif
                    @endforeach
                </div>
            </form>
        </div>
    @endif
</div>