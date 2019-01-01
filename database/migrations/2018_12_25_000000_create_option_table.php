<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOptionTable extends Migration
{
    /**
     * 运行数据库迁移
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('option')) {
            Schema::create('option', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name', 100)->unique();
                $table->longText('value')->nullable();
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
        Schema::dropIfExists('option');
    }
}
