@php
    $_more_load = isset($_more_load) && $_more_load ? TRUE : FALSE;
@endphp
@foreach($items as $key => $_item)
    @php
        if($loop->index > 5 || $_more_load){
            $_class = 'uk-width-1-4@l uk-width-1-2@s uk-width-1-1';
        }else{
            $_class = 'uk-width-1-3@l uk-width-1-2@s uk-width-1-1';
        }
    @endphp
    <div class="{{ $_class }}">
        @include('front.nodes.node_type_19_teaser_blog', ['item' => $_item])
    </div>
@endforeach
@if(method_exists($items, 'links'))
    <div id="pagination-page"
         class="uk-width-1-4@l uk-width-1-2@s uk-width-1-1">
        <div class="box-pagination uk-text-center">
            {!! $items->links('front.partials.pagination_ajax', ['page_alias' => (isset($alias) && $alias ? $alias : NULL)]) !!}
        </div>
    </div>
@endif