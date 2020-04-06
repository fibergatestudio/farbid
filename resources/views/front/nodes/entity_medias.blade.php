<div class="entity-related-medias">
    <ul class="uk-thumbnav uk-margin-bottom uk-margin-top"
        uk-lightbox>
        @foreach($items as $_file)
            @php($_caption = $_file->description ? "data-caption=\"{$_file->description}\"" : ($_file->title ? "data-caption=\"{$_file->title}\"" : ''))
            <li>
                <a href="{{ "/uploads/{$_file->filename}" }}"
                   title="{{ $_file->title ? $_file->title : '' }}"
                   uk-tooltip
                    {!! $_caption !!}>
                    {!! image_render($_file, 'thumb_media') !!}
                </a>
            </li>
        @endforeach
    </ul>
</div>