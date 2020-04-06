<button class="uk-modal-close-default"
        type="button"
        uk-close></button>
<form class="uk-form uk-form-stacked uk-width-1-1"
      method="POST"
      action="{{ $form->route }}">
    {{ csrf_field() }}
    {{ method_field('POST') }}
    @if($form->title)
        <div class="uk-modal-header">
            <h2 class="uk-modal-title">{{ $form->title }}</h2>
        </div>
    @endif
    <div class="uk-modal-body">
        @foreach($form->tabs as $field)
            {!! $field !!}
        @endforeach
    </div>
    <div class="uk-modal-footer uk-text-right">
        <button type="submit"
                name="save"
                value="1"
                class="uk-button uk-button-secondary uk-border-rounded use-ajax uk-waves">
            {{ $form->button_name }}
        </button>
        <button class="uk-button uk-button-default uk-modal-close uk-waves uk-button-icon uk-border-rounded"
                title="@lang('forms.button_close')"
                uk-icon="icon: ui_close"
                type="button"></button>
    </div>
</form>
