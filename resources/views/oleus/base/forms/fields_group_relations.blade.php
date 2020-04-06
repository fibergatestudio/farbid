<div class="uk-form-row">
    <div id="list-relation-items">
        @include('oleus.base.forms.fields_group_relations_items', ['related_items' => $item->related, 'route' => $form->route_tag])
    </div>
    <div class="uk-clearfix uk-text-right">
        {!! _l(trans('forms.button_add_element'), "oleus.{$form->route_tag}.relation", ['a' => ['class' => 'uk-button uk-button-medium uk-waves uk-button-secondary use-ajax uk-border-rounded', 'data-id' => $_item->id]]) !!}
    </div>
</div>