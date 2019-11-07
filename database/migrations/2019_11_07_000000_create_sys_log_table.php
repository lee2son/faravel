<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSysLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('faravel.log_to_db.table'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('env')->index()->comment('当前运行环境');
            $table->boolean('is_running_unit_tests')->index()->comment('是否允许在测试用例中');
            $table->boolean('is_running_in_console')->index()->comment('是否允许在控制台中');
            $table->string('ips')->nullable()->index()->comment('客户端IP地址列表');
            $table->string('request_id')->nullable()->index()->comment('$_SERVER["REQUEST_ID"]');
            $table->longText('message');
            $table->integer('level')->index();
            $table->string('level_name')->index();
            $table->string('channel')->index();
            $table->longText('extra');
            $table->longText('context');
            $table->integer('date')->index();
            $table->bigInteger('ts');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('faravel.log_to_db.table'));
    }

    /**
     * Get the migration connection name.
     *
     * @return string|null
     */
    public function getConnection()
    {
        return config('faravel.log_to_db.connection');
    }
}