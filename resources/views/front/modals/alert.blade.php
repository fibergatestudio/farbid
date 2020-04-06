<button class="uk-modal-close-default"
        type="button"
        uk-close></button>
<div class="uk-modal-body">
    @isset($title)
        <h3 class="uk-text-center">
            {!! $title !!}
        </h3>
    @endisset
    @isset($alert)
        <div class="message uk-text-center">
            {!! $alert !!}
        </div>
    @endisset
</div>
