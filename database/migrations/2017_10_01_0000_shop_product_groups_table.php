<?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;
    use Spatie\Permission\Models\Permission;
    use Spatie\Permission\Models\Role;

    class ShopProductGroupsTable extends Migration
    {
        protected $table;

        public function up()
        {
            Schema::create('shop_product_groups', function (Blueprint $table) {
                $table->increments('id');
                $table->integer('product_id')
                    ->unsigned()
                    ->nullable();
                $table->integer('related_id')
                    ->unsigned()
                    ->nullable();
                $table->integer('percent')
                    ->default(0);
                $table->foreign('product_id')
                    ->references('id')
                    ->on('shop_products')
                    ->onDelete('cascade');
                $table->foreign('related_id')
                    ->references('id')
                    ->on('shop_products')
                    ->onDelete('cascade');
            });
        }

        public function down()
        {
            if(Schema::hasTable('shop_product_groups')) {
                Schema::table('shop_product_groups', function (Blueprint $table) {
                    $table->dropForeign('shop_product_groups_product_id_foreign');
                    $table->dropForeign('shop_product_groups_related_id_foreign');
                    $table->drop();
                });
            }
        }
    }
