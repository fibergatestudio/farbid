{{--<div class="uk-position-relative">--}}
{{--<div class="uk-image-panel">--}}
{{--<button type="button"--}}
{{--data-fid="{{ $file->id }}"--}}
{{--uk-icon="icon: ui_mode_edit"--}}
{{--uk-tooltip="title: {{ trans('forms.button_edit') }}"--}}
{{--class="uk-button uk-button-icon uk-waves uk-button-primary uk-file-remove-button uk-border-rounded">--}}
{{--</button>--}}
{{--<button type="button"--}}
{{--data-fid="{{ $file->id }}"--}}
{{--uk-icon="icon: ui_delete_forever"--}}
{{--uk-tooltip="title: {{ trans('forms.button_delete') }}"--}}
{{--class="uk-button uk-button-icon uk-waves uk-button-danger uk-file-remove-button uk-border-rounded">--}}
{{--</button>--}}
{{--</div>--}}
{{--<div class="uk-image"--}}
{{--uk-lightbox>--}}
{{--<a href="{{ "/uploads/{$file->filename}" }}">--}}
{{--<img src="{{ image_render($file, 'thumb_preview') }}"--}}
{{--alt="{{ $file->filename }}">--}}
{{--</a>--}}
{{--</div>--}}
{{--</div>--}}


<div class="file">
    <div uk-grid
         class="uk-grid-collapse">
        <div class="uk-width-1-3 uk-image uk-border-rounded">
            {!! image_render($file, 'oleus_image_preview') !!}
        </div>
        <div class="uk-width-2-3">
            <div class="uk-margin-small-left">
                <div class="uk-form-row uk-input-title">
                    <div class="uk-form-controls uk-margin-remove">
                        <div class="uk-width-1-1">
                            <input type="text"
                                   name="{{ $field }}[{{ $file->id }}][title]"
                                   class="uk-input uk-form-small uk-border-rounded"
                                   placeholder="{{ trans('forms.label_attribute_title') }}"
                                   value="{{ $file->title }}"
                                   autocomplete="off">
                        </div>
                    </div>
                </div>
                <div class="uk-form-row uk-input-alt">
                    <div class="uk-form-controls uk-margin-remove">
                        <div class="uk-width-1-1">
                            <input type="text"
                                   name="{{ $field }}[{{ $file->id }}][alt]"
                                   class="uk-input uk-form-small uk-border-rounded"
                                   placeholder="{{ trans('forms.label_attribute_alt') }}"
                                   value="{{ $file->alt }}"
                                   autocomplete="off">
                        </div>
                    </div>
                </div>
                <div class="uk-form-row uk-input-description">
                    <div class="uk-form-controls uk-margin-remove">
                        <div class="uk-width-1-1">
<textarea name="{{ $field }}[{{ $file->id }}][description]"
          placeholder="{{ trans('forms.label_attribute_description') }}"
          class="uk-textarea uk-form-small uk-border-rounded"
          rows="2">{{ $file->description }}</textarea>
                        </div>
                    </div>
                </div>
                <div class="uk-form-row uk-text-right">
                    <button type="button"
                            data-fid="{{ $file->id }}"
                            uk-icon="icon: ui_delete_forever"
                            uk-tooltip="title: {{ trans('forms.button_delete') }}"
                            class="uk-button uk-button-icon uk-waves uk-button-danger uk-file-remove-button uk-border-rounded">
                    </button>
                </div>
                <input type="hidden"
                       name="{{ $field }}[{{ $file->id }}][id]"
                       value="{{ $file->id }}">
                <input type="hidden"
                       name="{{ $field }}[{{ $file->id }}][view]"
                       value="{{ $view }}">
            </div>
        </div>
    </div>
</div>