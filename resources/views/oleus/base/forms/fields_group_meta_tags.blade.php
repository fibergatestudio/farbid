@if(($item->exists && $item->alias_id && $item->_alias) || !$item->exists)
    {!!
        field_render('url.alias', [
            'label' => 'URL',
            'value' => $item->exists ? $item->_alias->alias : NULL,
            'help' => trans('forms.help_alias')
        ])
    !!}
    @if($item->exists)
        {!!
            field_render('url.re_render', [
                'type' => 'checkbox',
                'label' => trans('forms.label_render_url'),
                'selected' => $item->exists ? $item->_alias->re_render : 0,
            ])
        !!}
    @endif
@endif
{!!
    field_render('meta_title', [
        'label' => 'Title',
        'value' => $item->exists ? $item->meta_title : NULL
    ])
!!}
{!!
    field_render('meta_description', [
        'type' => 'textarea',
        'label' => 'Description',
        'value' => $item->exists ? $item->meta_description : NULL,
        'attributes' => [
            'rows' => 5,
        ]
    ])
!!}
{!!
    field_render('meta_keywords', [
        'type' => 'textarea',
        'label' => 'Keywords',
        'value' => $item->exists ? $item->meta_keywords : NULL,
        'attributes' => [
            'rows' => 5,
        ]
    ])
!!}
{!!
    field_render('meta_robots', [
        'type' => 'select',
        'label' => 'Robots',
        'value' => $item->exists ? $item->meta_robots : NULL,
        'values' => [
            'index, follow' => 'index, follow',
            'noindex, follow' => 'noindex, follow',
            'index, nofollow' => 'index, nofollow',
            'noindex, nofollow' => 'noindex, nofollow'
        ],
        'class' => 'uk-select2'
    ])
!!}
@if(($item->exists && !in_array($item->type, [
    'front',
    'sitemap',
    'error_401',
    'error_403',
    'error_404',
    'error_500',
    'error_503',
    'login',
    'register',
    'password_reset',
])) || !$item->exists)
    {!!
        field_render('sitemap', [
            'type'     => 'checkbox',
            'label'    => trans('forms.label_published_in_sitemap'),
            'name'     => 'meta_sitemap',
            'base_name' => 'seo',
            'selected' => $item->exists ? $item->sitemap : 1
        ])
    !!}
@endif