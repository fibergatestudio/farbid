<div class="file">
    <div uk-grid
         class="uk-grid-collapse">
        <div class="uk-width-2-5 uk-info">
            <div class="uk-card uk-card-small">
                <div class="uk-card-body">
                    <dl class="uk-description-list uk-text-small">
                        <dt class="uk-text-primary">@lang('forms.file_name')</dt>
                        <dd>{{ str_limit($file->filename, 40) }}</dd>
                        <dt class="uk-text-primary uk-margin-small-top">@lang('forms.file_mime')</dt>
                        <dd>{{ $file->filemime }}</dd>
                        <dt class="uk-text-primary uk-margin-small-top">@lang('forms.file_size')</dt>
                        <dd>{{ $file->filesize }} <strong>KB</strong></dd>
                    </dl>
                </div>
            </div>
        </div>
        <div class="uk-width-3-5">
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
                            class="uk-button uk-button-danger uk-button-icon uk-file-remove-button uk-border-rounded">
                    </button>
                </div>
                <input type="hidden"
                       name="{{ $field }}[{{ $file->id }}][id]"
                       value="{{ $file->id }}">
            </div>
        </div>
    </div>
</div>