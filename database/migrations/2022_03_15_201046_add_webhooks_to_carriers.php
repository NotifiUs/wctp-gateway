<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('carriers', function (Blueprint $table) {
            $table->string('webhook_host')->nullable()->after('thinq_api_token');
            $table->string('webhook_endpoint')->nullable()->after('webhook_host');
            $table->string('webhook_username')->nullable()->after('webhook_endpoint');
            $table->string('webhook_password')->nullable()->after('webhook_username');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('carriers', function (Blueprint $table) {
            $table->dropColumn(['webhook_host','webhook_endpoint', 'webhook_username', 'webhook_password']);
        });
    }
};
