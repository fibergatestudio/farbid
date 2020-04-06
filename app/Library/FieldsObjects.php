<?php

    namespace App\Library;

    class FieldsObjects
    {
        protected $params;
        protected $errors;
        protected $required;
        protected $variables;
        protected $name;
        protected $field_name;
        protected $base_name;
        protected $old_name;


        public function __construct($name, $variables = [])
        {


            $this->variables = $variables = collect($variables);
            $this->errors = session('errors');
            $this->required = $variables->get('required', FALSE);
            if(str_contains($name, '.')) {
                $this->name = $this->render_field_name($name);
            } else {
                $this->name = $name;
            }
            $this->params = collect([
                'type'         => $variables->get('type', 'text'),
                'id'           => $variables->has('id') ? str_slug('form_field_' . str_replace('.', '_', $variables->get('id')), '_') : str_slug('form_field_' . str_replace('.', '_', $name), '_'),
                'class'        => $variables->get('class'),
                'label'        => ($_label = $variables->get('label')) ? trans($_label) : NULL,
                'icon'         => $variables->get('icon'),
                'name'         => $this->name,
                'old'          => $name,
                'values'       => $variables->get('values', []),
                'selected'     => old($name, ($variables->has('selected') ? $variables->get('selected') : ($variables->has('value') ? $variables->get('value') : NULL))),
                'attributes'   => $variables->has('attributes') ? render_attributes($variables->get('attributes')) : NULL,
                'help'         => $variables->get('help'),
                'error'        => $this->errors && $this->errors->has($name) ? $this->errors->first($name) : NULL,
                'required'     => $this->required,
                'prefix'       => $variables->get('prefix'),
                'suffix'       => $variables->get('suffix'),
                'options'      => $variables->get('options', []),
                'multiple'     => $variables->has('multiple') ? TRUE : FALSE,
                'ajax_url'     => $variables->get('ajax_url', _r('ajax.file.upload')),
                'upload_allow' => ($_allow = $variables->get('allow')) ? '*.(' . $_allow . ')' : '*.(jpg|jpeg|gif|png)',

                //                'value'      => $variables->get('value'),
                //                'base_name'          => $this->base_name,

                //                'values'             => $variables->get('values', []),

                //                'editor'             => $variables->has('editor') ? TRUE : FALSE,

                //                'upload_allow'       => $variables->get('allow', '*.(jpg|jpeg|gif|png)'),
                //                'autocomplete_name'  => $this->renderFieldName('value'),
                //                'autocomplete_old'   => $this->renderOldFieldName('value'),
                //                'autocomplete_value' => old($this->renderOldFieldName('value'), $variables->get('value') ? $variables->get('value') : NULL),
            ]);
        }

        /**
         * @return null|string
         * @throws \Throwable
         */
        public function _render()
        {
            $_params = $this->params;
            $_field = NULL;
            if($_params->has('name') && $_params->get('name')) {
                switch($_params->get('type')) {
                    case 'checkbox':
                        $_params->put('value', (is_null($_params->get('value')) ? 1 : $_params->get('value')));
                        $_field = view('oleus.base.forms.field_checkbox')
                            ->with('params', $_params)
                            ->render();
                        break;
                    case 'checkboxes':
                        $selected = $_params->get('selected') ? : NULL;
                        if($selected && old()) $selected = array_keys($selected);
                        $_params->put('selected', $selected);
                        $_field = view('oleus.base.forms.field_checkboxes')
                            ->with('params', $_params)
                            ->render();
                        break;
                    case 'textarea':
                        if($_params->get('editor')) {
                            $class = $_params->get('class');
                            $_params->put('class', ($class ? "{$class} uk-ckeditor" : 'uk-ckeditor'));
                        }
                        $_field = view('oleus.base.forms.field_textarea')
                            ->with('params', $_params)
                            ->render();
                        break;
                    case 'file':
                        $_field = view('oleus.base.forms.field_file')
                            ->with('params', $_params)
                            ->render();
                        break;
                    case 'avatar':
                        $_field = view('oleus.base.forms.field_avatar')
                            ->with('params', $_params)
                            ->render();
                        break;
                    case 'radio':
                        $_field = view('oleus.base.forms.field_radio')
                            ->with('params', $_params)
                            ->render();
                        break;
                    case 'select':
                        if($options = $_params->get('options')) {
                            if(!isset($options['maxItemCount'])) $options['maxItemCount'] = -1;
                            if(!isset($options['searchEnabled'])) $options['searchEnabled'] = FALSE;
                            $_params->put('options', $options);
                        } else {
                            $_params->put('options', [
                                'maxItemCount'  => -1,
                                'searchEnabled' => FALSE
                            ]);
                        }
                        $_field = view('oleus.base.forms.field_select')
                            ->with('params', $_params)
                            ->render();
                        break;
                    case 'autocomplete':
                        $_field = view('oleus.base.forms.field_autocomplete')
                            ->with('params', $_params)
                            ->render();
                        break;
                    case 'hidden':
                        $_field = view('oleus.base.forms.field_hidden')
                            ->with('params', $_params)
                            ->render();
                        break;
                    case 'password_confirmation':
                        if(str_contains($_params->get('old'), '.')) {
                            $_confirmation = "{$_params->old}_confirmation";
                            $_params->put('name_confirmation', $this->render_field_name($_confirmation));
                        } else {
                            $_params->put('name_confirmation', $_params->get('old') . '_confirmation');
                        }
                        $_params->put('label_confirmation', (($_label = $this->variables->get('label')) ? trans("{$_label}_confirmation") : NULL));
                        $_params->put('attributes', (($_label = $this->variables->get('label')) ? trans("{$_label}_confirmation") : NULL));
                        $_field = view('oleus.base.forms.field_password_confirmation')
                            ->with('params', $_params)
                            ->render();
                        break;
                    default:
                        $_field = view('oleus.base.forms.field_text')
                            ->with('params', $_params)
                            ->render();
                        break;
                }
                if($_field) $_field = $_params->get('prefix') . $_field . $_params->get('suffix');
            }

            return $_field;
        }

        /**
         * @param string|null $name
         * @return mixed
         */
        protected function render_field_name($name)
        {
            $name = explode('.', $name);
            $_name = NULL;
            foreach($name as $item) $_name .= is_null($_name) ? (string)$item : (string)"[{$item}]";

            return $_name;
        }

        /**
         * @param $name
         * @return mixed
         */
        public function renderOldFieldName($name = NULL)
        {
            if(is_null($this->name) && is_null($name)) return NULL;
            $name = $name ? $this->renderFieldName($name) : $this->field_name;

            $_name = str_replace(']', '', str_replace('[', '.', $name));

            return $_name;
        }

        public function renderFieldAttributes($attributes = [])
        {
            $_attributes = '';

            if(count($attributes)) {
                $_attributes = [];
                foreach($attributes as $_key => $_value) {
                    $_attributes[] = "{$_key}=\"{$_value}\"";
                }
                $_attributes = implode(' ', $_attributes);
            }

            return $_attributes;
        }
    }

