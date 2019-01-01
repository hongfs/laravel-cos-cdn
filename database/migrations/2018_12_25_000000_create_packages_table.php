<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackagesTable extends Migration
{
    /**
     * 运行数据库迁移
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('packages')) {
            Schema::create('packages', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name', 255)->comment('名称');
                $table->string('alias', 255)->comment('别名');
                $table->string('description', 1000)->nullable()->comment('描述');
                $table->string('github', 255)->nullable()->comment('github');
                $table->string('homepage', 255)->nullable()->comment('官网');
                $table->string('minversion', 255)->nullable()->comment('最低版本');
                $table->string('etag', 255)->nullable()->comment('Etag');
                $table->tinyInteger('star')->default(0)->comment('星标');
                $table->tinyInteger('visible')->default(1)->comment('可见性');
                $table->timestamps();
                $table->softDeletes();
            });
        }
    }

    /**
     * 回滚数据库迁移
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('packages');
    }
}
