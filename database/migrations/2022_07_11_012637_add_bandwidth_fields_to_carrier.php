<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('carriers', function (Blueprint $table) {
            $table->string('bandwidth_api_username')->nullable()->after('webhook_password');
            $table->text('bandwidth_api_password')->nullable()->after('bandwidth_api_username');
            $table->text('bandwidth_api_account_id')->nullable()->after('bandwidth_api_password');
            $table->text('bandwidth_api_application_id')->nullable()->after('bandwidth_api_account_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carriers', function (Blueprint $table) {
            $table->dropColumn(['bandwidth_api_username','bandwidth_api_password', 'bandwidth_api_account_id', 'bandwidth_api_application_id']);
        });
    }
};
