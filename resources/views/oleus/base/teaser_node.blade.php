@php($_teaser_image = $item->_preview_asset('thumb_blog'))
<div class="node-teaser node-teaser-item-{{ $item->id }} node-item-{{ $item->id }}">
    <div class="uk-card uk-card-default uk-margin-bottom">
        @if($_teaser_image)
            <div class="uk-card-media-top">
                <a href="{{ _u($item->_alias->alias, [], TRUE) }}"
                   title="{{ $item->title }}">
                    {!! $_teaser_image !!}
                </a>
            </div>
        @endif
        <div class="uk-card-body">
            <h3 class="uk-card-title node-title">
                <a href="{{ _u($item->_alias->alias, [], TRUE) }}"
                   title="{{ $item->title }}">
                    {{ $item->title }}
                </a>
            </h3>
            <p class="uk-text-meta node-published-date">
                {{ $item->published_at->format('d/m/Y') }}
            </p>
            <div class="node-body">
                @php($_node_body = $item->teaser ? strip_tags($item->teaser) : strip_tags($item->body))
                {!! str_limit($_node_body, 150) !!}
            </div>
        </div>
        <hr>
        <div class="uk-text-right node-more-link">
            <noindex>
                <a href="{{ _u($item->_alias->alias) }}"
                   rel="nofollow"
                   title="{{ $item->title }}">
                    @lang('others.more')
                </a>
            </noindex>
        </div>
    </div>
</div>