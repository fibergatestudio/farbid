<div id="shop-category-filter-card">
    <div class="selected-options-title sidebar-title">
        <h3>
          <span>
           @lang('others.is_selected')
           </span>
        </h3>
    </div>
    @if($params)
        <div class="filter-category sidebar-contant">
            @if($selected)
                <div class="uk-card uk-margin-small-bottom uk-padding-remove selected-options"
                     id="shop-category-selected-options">
                    <div
                        class="uk-card-body uk-padding-small uk-padding-remove-top uk-padding-remove-horizontal size mb-20">
                        <ul class="uk-list">
                            @foreach($selected as $_par => $_checked)
                                @if($_checked['type'] == 'select')
                                    @foreach($_checked['checked'] as $_option_key => $_option_value)
                                        <li>
                                            <a href="{{ $_option_value['alias'] }}"
                                               data-view_load="0"
                                               class="uk-padding-remove use-ajax">
                                            <span class="uk-icon"
                                                  uk-close></span>
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
                                                  uk-close></span>
                                            <span
                                                class="uk-link-name">{!! "{$_checked['checked']['min']} - {$_checked['checked']['max']} <span class='suffix'>{$_checked['checked']['unit']}</span>" !!}</span>
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                            <li>
                                <div class="uk-text-right">
                                    <a href="{{ _u(wrap()->get('shop_category')->_alias->alias) }}"
                                       data-view_load="0"
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
                  data-category="{{ $item->id }}"
                  id="shop-category-filter">
                <div class="size mb-20">
                    <ul class="uk-nav-default uk-nav-parent-icon"
                        uk-nav>
                        @foreach($params as $_param)
                            <li class="uk-parent">
                                <a href="javascript:void(0);" class="inner-title">
                                    {{ $_param['html']['label'] }}
                                </a>
                                <ul class="uk-nav-sub uk-padding-remove-left">
                                    @foreach($_param['html']['values'] as $_value)
                                        <li>{!! $_value !!}</li>
                                    @endforeach
                                </ul>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </form>
        </div>
    @endif
</div>