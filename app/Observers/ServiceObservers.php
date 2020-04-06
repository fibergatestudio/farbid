<?php

    namespace App\Observers;

    use App\Models\File;
    use App\Models\FilesReference;
    use App\Models\Page;
    use App\Models\Service;
    use App\Models\ServicePrice;
    use App\Models\UrlAlias;

    class ServiceObservers
    {
        public function created(Service $item)
        {
        }

        public function saved(Service $item)
        {
            if(request()->has('url_alias')) {
                $_url_alias = new UrlAlias($item);
                $_url_alias->set(request()->input('url_alias'));
            }
            if(request()->has('background_fid') && ($_background = request()->input('background_fid'))) {
                $_background = array_shift($_background);
                File::where('id', $_background['id'])
                    ->update([
                        'title'       => $_background['title'],
                        'alt'         => $_background['alt'],
                        'description' => $_background['description'],
                    ]);
            }
            if(request()->has('medias') && request()->input('medias')) {
                $_medias = new FilesReference($item, 'medias');
                $_medias->set();
            } else {
                FilesReference::where('type', 'medias')
                    ->where('model_type', $item->getMorphClass())
                    ->where('model_id', $item->id)
                    ->delete();
            }
            if(request()->has('files') && request()->input('files')) {
                $_medias = new FilesReference($item, 'files');
                $_medias->set();
            } else {
                FilesReference::where('type', 'files')
                    ->where('model_type', $item->getMorphClass())
                    ->where('model_id', $item->id)
                    ->delete();
            }
            if(request()->has('service_prices') && request()->input('service_prices')) {
                $_medias = new ServicePrice($item);
                $_medias->set();
            } else {
//                FilesReference::where('type', 'medias')
//                    ->where('model_type', $item->getMorphClass())
//                    ->where('model_id', $item->id)
//                    ->delete();
            }
        }

        public function deleting(Service $item)
        {
            if($item->alias_id) {
                UrlAlias::where('id', $item->alias_id)
                    ->delete();
            }
            FilesReference::where('model_type', $item->getMorphClass())
                ->where('model_id', $item->id)
                ->delete();
        }
    }