<?php

    namespace App\Observers;

    use App\Models\File;
    use App\Models\FilesReference;
    use App\Models\Node;
    use App\Models\UrlAlias;

    class NodeObservers
    {
        public function created(Node $item)
        {
        }

        public function saved(Node $item)
        {
            if(request()->has('url')) {
                $_founder = NULL;
                if($item->_related_page && ($_related_page_alias = $item->_related_page->_alias)) $_founder[] = $_related_page_alias->alias;
                $_url_alias = new UrlAlias($item, $_founder);
                $_url_alias->set();
            }
            if(request()->has('preview_fid') && ($_preview = request()->input('preview_fid'))) {
                $_preview = array_shift($_preview);
                File::where('id', $_preview['id'])
                    ->update([
                        'title'       => $_preview['title'],
                        'alt'         => $_preview['alt'],
                        'description' => $_preview['description'],
                    ]);
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
            if(request()->has('medias')) {
                $_medias = new FilesReference($item, 'medias');
                $_medias->set();
            }
            if(request()->has('files')) {
                $_medias = new FilesReference($item, 'files');
                $_medias->set();
            }
            $item->forgetCache();
        }

        public function deleting(Node $item)
        {
            if($item->alias_id) {
                UrlAlias::where('id', $item->alias_id)
                    ->delete();
            }
            FilesReference::where('model_type', $item->getMorphClass())
                ->where('model_id', $item->id)
                ->delete();
            $_relation_items = Node::where('relation', $item->id)
                ->get();
            if($_relation_items->isNotEmpty()) {
                $_relation_items->each(function ($_node) {
                    UrlAlias::where('id', $_node->alias_id)
                        ->delete();
                    FilesReference::where('model_type', $_node->getMorphClass())
                        ->where('model_id', $_node->id)
                        ->delete();
                    $_node->delete();
                });
            }
        }
    }