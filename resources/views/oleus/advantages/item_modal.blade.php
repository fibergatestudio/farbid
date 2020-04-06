<button class="uk-modal-close-default"
        type="button"
        uk-close></button>
<form class="uk-form uk-form-stacked uk-form-horizontal"
      method="POST"
      action="{{ $item->exists ? _r('oleus.advantages.item', ['advantages' => $advantages, 'action' => 'update', 'id' => $item->id]) : _r('oleus.advantages.item', ['advantages' => $advantages, 'action' => 'save']) }}">
    <input type="hidden"
           value="{{ $item->exists ? $item->id : NULL }}"
           name="advantage_item[id]">
    <div class="uk-modal-header">
        <h2 class="uk-modal-title">{{ $item->exists ? trans('pages.advantages_item_update') : trans('pages.advantages_item_create') }}</h2>
    </div>
    <div class="uk-modal-body">
        {!!
            field_render('advantage_item.title', [
                'label'=> trans('forms.label_title'),
                'value' => $item->exists ? $item->title : NULL,
                'required' => TRUE
            ])
        !!}
        {!!
            field_render('advantage_item.sub_title', [
                'label'=> trans('forms.label_sub_title'),
                'value' => $item->exists ? $item->sub_title : NULL
            ])
        !!}
        {!!
            field_render('advantage_item.icon_fid', [
                'type' => 'file',
                'label' => trans('forms.label_icon'),
                'allow'  => 'jpg|jpeg|gif|png|svg',
                'values' => $item->exists && $item->_icon? [$item->_icon] : NULL
            ])
        !!}
        {!!
            field_render('advantage_item.body', [
                'label'      => trans('forms.label_body'),
                'type'       => 'textarea',
                'editor'     => TRUE,
                'class'      => 'editor-short',
                'value'      => $item->exists ? $item->body : NULL,
                'attributes' => [
                    'rows' => 3,
                ],
                'required' => TRUE
            ])
        !!}
        <hr class="uk-divider-icon">
        {!!
            field_render('advantage_item.sort', [
                'type' => 'select',
                'label' => trans('forms.label_description_position'),
                'selected' => $item->exists ? $item->sort : 0,
                'values' => sort_field(),
                'class' => 'uk-select2'
            ])
        !!}
        {!!
            field_render('advantage_item.hidden_title', [
                'type' => 'checkbox',
                'label' => trans('forms.label_hidden_title'),
                'selected' => $item->exists ? $item->hidden_title : 0
            ])
        !!}
        {!!
            field_render('advantage_item.status', [
                'type' => 'checkbox',
                'label' => trans('forms.label_visible_advantage'),
                'selected' => $item->exists ? $item->status : 1
            ])
        !!}
    </div>
    <div class="uk-modal-footer uk-text-right">
        <button type="submit"
                name="save"
                value="1"
                class="uk-button uk-button-secondary use-ajax uk-waves uk-border-rounded">
            @lang('forms.button_save')
        </button>
        @if($item->exists)
            <button type="button"
                    name="delete"
                    value="1"
                    title="@lang('forms.button_delete')"
                    uk-icon="icon: ui_delete_forever"
                    class="uk-button uk-button-danger use-ajax uk-waves uk-button-icon uk-border-rounded"></button>
            <button class="uk-button uk-button-default uk-modal-close uk-waves uk-button-icon uk-border-rounded"
                    title="@lang('forms.button_close')"
                    uk-icon="icon: ui_close"
                    type="button"></button>
        @endif
    </div>
</form>
@if($item->exists)
    <form
        action="{{ _r('oleus.advantages.item', ['advantages' => $advantages, 'action' => 'destroy', 'id' => $item->id]) }}"
        id="form-delete-object"
        method="POST">
        {{ csrf_field() }}
        {{ method_field('DELETE') }}
    </form>
@endif