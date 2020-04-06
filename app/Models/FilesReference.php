<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Model;

    class FilesReference extends Model
    {
        protected $table = 'files_reference';
        protected $guarded = [];
        protected $entity;
        protected $type;
        public $timestamps = FALSE;

        public function __construct($entity = NULL, $type = 'medias')
        {
            $this->entity = $entity;
            $this->type = $type;
        }

        public function set()
        {
            if($this->entity && ($_files_reference = request()->input($this->type))) {
                $_old = self::where('model_type', $this->entity->getMorphClass())
                    ->where('model_id', $this->entity->id)
                    ->pluck('relation_fid');
                if(is_array($_files_reference)) {
                    foreach($_files_reference as $_file) {
                        $files_media[] = $this->updateOrCreate([
                            'model_type'   => $this->entity->getMorphClass(),
                            'model_id'     => $this->entity->id,
                            'type'         => $this->type,
                            'relation_fid' => $_file['id'],
                        ], [
                            'model_type'   => $this->entity->getMorphClass(),
                            'model_id'     => $this->entity->id,
                            'type'         => $this->type,
                            'relation_fid' => $_file['id'],
                        ]);
                        File::where('id', $_file['id'])
                            ->update([
                                'title'       => $_file['title'],
                                'alt'         => $_file['alt'],
                                'description' => $_file['description'],
                            ]);
                        $_old = $_old->filter(function ($_value) use ($_file) {
                            return $_value != $_file['id'];
                        });
                    }
                }
                $_files_reference_delete = $_old->all();
                if(count($_files_reference_delete)) {
                    self::where('model_type', $this->entity->getMorphClass())
                        ->where('model_id', $this->entity->id)
                        ->where('type', $this->type)
                        ->whereIn('relation_fid', $_files_reference_delete)
                        ->delete();
                }
            } elseif($this->entity) {
                self::where('model_type', $this->entity->getMorphClass())
                    ->where('model_id', $this->entity->id)
                    ->where('type', $this->type)
                    ->delete();
            }

            return NULL;
        }
    }
