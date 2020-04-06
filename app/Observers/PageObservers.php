<?php

    namespace App\Observers;

    use App\Models\File;
    use App\Models\FilesReference;
    use App\Models\Node;
    use App\Models\Page;
    use App\Models\UrlAlias;

    class PageObservers
    {
        public function created(Page $item)
        {
        }

        public function saved(Page $item)
        {
            if(request()->has('url')) {
                $_url_alias = new UrlAlias($item);
                $_url_alias->set();
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
            if(request()->has('files') && request()->input('files')) {
                $_medias = new FilesReference($item, 'files');
                $_medias->set();
            }
            $item->forgetCache();
        }

        public function deleting(Page $item)
        {
            if($item->alias_id) {
                UrlAlias::where('id', $item->alias_id)
                    ->delete();
            }
            FilesReference::where('model_type', $item->getMorphClass())
                ->where('model_id', $item->id)
                ->delete();
            $_relation_items = Page::where('relation', $item->id)
                ->get();
            if($_relation_items->isNotEmpty()) {
                $_relation_items->each(function ($_page) {
                    UrlAlias::where('id', $_page->alias_id)
                        ->delete();
                    FilesReference::where('model_type', $_page->getMorphClass())
                        ->where('model_id', $_page->id)
                        ->delete();
                    $_page->delete();
                });
            }
            if($item->type == 'page_list_nodes') {
                $_nodes = Node::where('entity_id', $item->id)
                    ->get();
                if($_nodes->isNotEmpty()) {
                    $_nodes->each(function ($_node) {
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
    }