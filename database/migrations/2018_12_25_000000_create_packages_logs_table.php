<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackagesLogsTable extends Migration
{
    /**
     * 运行数据库迁移
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('packages_logs')) {
            Schema::create('packages_logs', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('pid')->comment('package id');
                $table->string('version', 255)->comment('版本');
                $table->unsignedSmallInteger('total_number')->default(0)->comment('总数量');
                $table->unsignedSmallInteger('success_number')->default(0)->comment('成功数量');
                $table->unsignedSmallInteger('fail_number')->default(0)->comment('失败数量');
                $table->text('fail_files')->nullable()->comment('失败文件');
                $table->float('running_time', 9, 3)->comment('运行时间(秒)');
                $table->tinyInteger('status')->default(1)->comment('状态');
                $table->dateTime('created_at');
                $table->dateTime('updated_at');
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
        Schema::dropIfExists('packages_logs');
    }
}
