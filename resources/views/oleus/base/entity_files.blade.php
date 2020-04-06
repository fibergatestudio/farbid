<div class="entity-related-files">
    <ul>
        @foreach($items as $_file)
            <li>
                @php($_file_extension = preg_replace('/.+\./', '', $_file->filename))
                <a href="{{ formalize_path("uploads/{$_file->filename}")  }}"
                   target="_blank"
                   title="{{ $_file->title ? $_file->title : '' }}"
                   class="file-type-{{ $_file_extension }} icon-{{ $_file_extension }}">
                    <span uk-icon="icon: ui_attachment"></span>
                    {{ $_file->title }}
                </a>
            </li>
        @endforeach
    </ul>
</div>