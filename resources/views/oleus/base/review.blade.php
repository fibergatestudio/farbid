<div class="uk-card uk-margin-bottom review-item review-item-{{ $item->id }}">
    @if($item->subject)
        <div class="uk-margin-small-bottom uk-h3 review-subject uk-text-uppercase">
            {{ $item->subject }}
        </div>
    @endif
    <div class="uk-grid uk-grid-small review-header">
        <div class="uk-width-auto review-rating">
            @for($i = 1; $i < 6; $i++)
                @php($_star_class = $i <= $item->rating ? 'checked' : '')
                <span uk-icon="icon: ui_star; ratio: .8"
                      class="{{ $_star_class }}"></span>
            @endfor
        </div>
        <div class="uk-width-auto review-date">
            {{ $item->created_at->format('d/m/Y') }}
        </div>
        <div class="uk-width-expand review-name">
            {{ $item->name }}
        </div>
    </div>
    <div class="review-body">
        {{ $item->review }}
    </div>
</div>
