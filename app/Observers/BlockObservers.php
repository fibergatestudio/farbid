<?php

    namespace App\Observers;

    use App\Models\Block;
    use App\Models\File;
    use App\Models\FilesReference;

    class BlockObservers
    {
        public function created(Block $item)
        {
        }

        public function saved(Block $item)
        {
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
        }

        public function deleting(Block $item)
        {
            FilesReference::where('model_type', $item->getMorphClass())
                ->where('model_id', $item->id)
                ->delete();
        }
    }