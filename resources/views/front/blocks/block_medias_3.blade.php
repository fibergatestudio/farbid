<div id="brand-logo" class="owl-carousel align_center">
        @foreach($items as $_file)
            @php($_cation = $_file->description ? "data-caption=\"{$_file->description}\"" : ($_file->title ? "data-caption=\"{$_file->title}\"" : ''))
        <div class="item">
              {!! image_render($_file, 'thumb_media') !!}
        </div>
        @endforeach
</div>