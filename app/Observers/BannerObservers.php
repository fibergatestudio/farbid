<?php

    namespace App\Observers;

    use App\Models\Banner;
    use App\Models\Block;
    use App\Models\File;
    use App\Models\FilesReference;
    use Illuminate\Support\Facades\DB;

    class BannerObservers
    {
        public function created(Banner $item)
        {
        }

        public function saved(Banner $item)
        {
            if(request()->has('banner_fid') && ($_banner = request()->input('banner_fid'))) {
                $_banner = array_shift($_banner);
                File::where('id', $_banner['id'])
                    ->update([
                        'title'       => $_banner['title'],
                        'alt'         => $_banner['alt'],
                        'description' => $_banner['description'],
                    ]);
            }
            if(request()->has('link') && ($_link = request()->input('link'))) {
                $_default = [
                    '<front>',
                    '<none>'
                ];
                $entity_id = NULL;
                $link = NULL;
                if(in_array($_link['name'], $_default)) {
                    $link = $_link['name'];
                } elseif($_link['value']) {
                    $entity_id = $_link['value'];
                } elseif($_link['name']) {
                    $link = $_link['name'];
                }
                DB::table('banners')
                    ->where('id', $item->id)
                    ->update([
                        'alias_id' => $entity_id,
                        'link'     => $link,
                    ]);
            }
        }

        public function deleting(Banner $item)
        {
        }
    }