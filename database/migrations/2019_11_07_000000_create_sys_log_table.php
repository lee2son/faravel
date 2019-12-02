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
        Schema::create(env('LOG_TABLE'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('env')->index()->comment('当前运行环境');
            $table->boolean('is_running_unit_tests')->index()->comment('是否运行在测试用例中');
            $table->boolean('is_running_in_console')->index()->comment('是否运行在控制台中');
            $table->string('ips')->nullable()->index()->comment('客户端IP地址列表');
            $table->string('request_id')->nullable()->index()->comment('X-Request-Id');
            $table->string('trace_id')->nullable()->index()->comment('X-Trace-Id');
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
        Schema::dropIfExists(env('LOG_TABLE'));
    }

    /**
     * Get the migration connection name.
     *
     * @return string|null
     */
    public function getConnection()
    {
        return env('LOG_CONNECTION');
    }
}