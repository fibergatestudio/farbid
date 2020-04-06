@php
    $_item_alias = $item->_alias;
    $_item_alias = $_item_alias->language != DEFAULT_LANGUAGE ? "{$_item_alias->language}/{$_item_alias->alias}" : $_item_alias->alias;
@endphp
<a href="{{ _u($_item_alias) }}"
   title="{{ $item->title }}"
   class="preview read">
    <div class="blog-item uk-position-relative uk-flex uk-flex-bottom">
        @if($_teaser_image = $item->_preview_asset('thumb_blog', ['only_way' => TRUE]))
            <div class="blog-media uk-background-cover uk-position-cover"
                 style="background-image: url({{ $_teaser_image }})">
            </div>
        @else
            <img src="{{ formalize_path('template/img/no-image.png') }}"
                 alt="{{ $item->title }}">
        @endif
        <div class="blog-detail">
            <div class="blog-title">
                <h3>
                    {{ $item->title }}
                </h3>
            </div>
            @php($_node_body = $item->teaser ? strip_tags($item->teaser) : strip_tags($item->body))
            <div class="blog-teaser">
                {!! str_limit($_node_body, 290) !!}
            </div>
            <div class="">
                <div class="post-date">
                    {{ $item->published_at->format('d.m.Y') }}
                </div>
            </div>
        </div>
    </div>
</a>