<?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;
    use Spatie\Permission\Models\Permission;
    use Spatie\Permission\Models\Role;

    class ShopProductViewedTable extends Migration
    {
        protected $table;

        public function up()
        {
            Schema::create('shop_product_viewed', function (Blueprint $table) {
                $table->integer('product_id')
                    ->unsigned();
                $table->integer('user_id')
                    ->unsigned();
                $table->timestamp('added_on');
                $table->foreign('product_id')
                    ->references('id')
                    ->on('shop_products')
                    ->onDelete('cascade');
                $table->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');
            });
        }

        public function down()
        {
            if (Schema::hasTable('shop_product_viewed')) {
                Schema::table('shop_product_viewed', function (Blueprint $table) {
                    $table->dropForeign('shop_product_viewed_product_id_foreign');
                    $table->dropForeign('shop_product_viewed_user_id_foreign');
                    $table->drop();
                });
            }
        }
    }
