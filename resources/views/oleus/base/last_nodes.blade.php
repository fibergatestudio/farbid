<div class="uk-clearfix uk-block last-nodes last-nodes-{{ str_slug($item->type, '-') }}">
    @if($_options['title'])
        <div class="uk-block-heading">
            <h2 class="uk-h2 uk-text-uppercase block-title">
                {!! $_options['title'] !!}
            </h2>
        </div>
    @endif
    <div class="uk-block-body">
        <div class="uk-grid-match uk-grid-small uk-child-width-1-{{ $_options['take'] }}@l"
             uk-grid>
            @foreach($items as $_item)
                @include('oleus.base.teaser_node', ['item' => $_item])
            @endforeach
        </div>
    </div>
    @if($_options['more-link'])
        <div class="uk-block-more-link uk-text-right">
            {!! _l(trans('others.more'), $item->_alias->alias) !!}
        </div>
    @endif
</div>