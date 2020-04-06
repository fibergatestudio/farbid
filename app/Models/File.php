<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;
    use Illuminate\Support\Facades\DB;
    use Illuminate\Support\Facades\Storage;

    class File extends Model
    {
        const IMAGE_MIMETYPE = [
            'image/jpeg',
            'image/png',
            'image/gif'
        ];
        protected $table = 'file_managed';
        protected $guarded = [];
        private $fid;

        public function __construct($fid = NULL)
        {
            $this->fid = $fid;
        }

        public static function duplicate($id = NULL)
        {
            if(!is_null($id)) {
                $_entity = self::find($id);
                $_save = $_entity->toArray();
                unset($_save['id']);
                unset($_save['created_at']);
                unset($_save['updated_at']);
                $_duplicate = self::updateOrCreate([
                    'id' => NULL
                ], $_save);

                return $_duplicate->id;
            }

            return NULL;
        }

        public static function checked_files()
        {
            $_files = DB::table('file_managed as f')
                ->whereExists(function ($_query) {
                    $_query->select(DB::raw(1))
                        ->from('pages as p')
                        ->whereRaw('p.background_fid = f.id');
                })
                ->whereExists(function ($_query) {
                    $_query->select(DB::raw(1))
                        ->from('nodes as n')
                        ->whereRaw('n.background_fid = f.id');
                }, 'or')
                ->whereExists(function ($_query) {
                    $_query->select(DB::raw(1))
                        ->from('nodes as n1')
                        ->whereRaw('n1.preview_fid = f.id');
                }, 'or')
                ->whereExists(function ($_query) {
                    $_query->select(DB::raw(1))
                        ->from('advantages as a')
                        ->whereRaw('a.background_fid = f.id');
                }, 'or')
                ->whereExists(function ($_query) {
                    $_query->select(DB::raw(1))
                        ->from('advantage_items as ai')
                        ->whereRaw('ai.icon_fid = f.id');
                }, 'or')
                ->whereExists(function ($_query) {
                    $_query->select(DB::raw(1))
                        ->from('banners as ba')
                        ->whereRaw('ba.banner_fid = f.id');
                }, 'or')
                ->whereExists(function ($_query) {
                    $_query->select(DB::raw(1))
                        ->from('blocks as bl')
                        ->whereRaw('bl.background_fid = f.id');
                }, 'or')
                ->whereExists(function ($_query) {
                    $_query->select(DB::raw(1))
                        ->from('files_reference as fr')
                        ->whereRaw('fr.relation_fid = f.id');
                }, 'or')
                ->whereExists(function ($_query) {
                    $_query->select(DB::raw(1))
                        ->from('profiles as pr')
                        ->whereRaw('pr.avatar_fid = f.id');
                }, 'or')
                ->whereExists(function ($_query) {
                    $_query->select(DB::raw(1))
                        ->from('services as se')
                        ->whereRaw('se.background_fid = f.id');
                }, 'or')
                ->whereExists(function ($_query) {
                    $_query->select(DB::raw(1))
                        ->from('shop_categories as sc')
                        ->whereRaw('sc.background_fid = f.id');
                }, 'or')
                ->whereExists(function ($_query) {
                    $_query->select(DB::raw(1))
                        ->from('shop_categories as sc1')
                        ->whereRaw('sc1.icon_fid = f.id');
                }, 'or')
                ->whereExists(function ($_query) {
                    $_query->select(DB::raw(1))
                        ->from('shop_products as sp')
                        ->whereRaw('sp.background_fid = f.id');
                }, 'or')
                ->whereExists(function ($_query) {
                    $_query->select(DB::raw(1))
                        ->from('shop_products as sp1')
                        ->whereRaw('sp1.preview_fid = f.id');
                }, 'or')
                ->whereExists(function ($_query) {
                    $_query->select(DB::raw(1))
                        ->from('slider_items as sl')
                        ->whereRaw('sl.background_fid = f.id');
                }, 'or')
                ->pluck('f.id');
            $_config_seo = config('os_seo');
            foreach($_config_seo['logotype'] as $_file) {
                if($_file) $_files->push($_file);
            }
            if($_config_seo['favicon']) $_files->push($_config_seo['favicon']);

            if($_files->isNotEmpty()) {
                $_delete_files = File::whereNotIn('id', $_files)
//                    ->limit(10)
                    ->get();
                if($_delete_files->isNotEmpty()){
                    $_delete_files->map(function($_file){
                        if(Storage::disk('uploads')->has($_file->filename)){
                            Storage::disk('uploads')->delete($_file->filename);
                        }
                        if(Storage::disk('front')->has("images/{$_file->filename}")){
                            Storage::disk('front')->delete("images/{$_file->filename}");
                        }
                        $_file->delete();
                    });
                }
            }

            dd($_delete_files, 1);
        }
    }
