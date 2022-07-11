<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEnterpriseHostsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('enterprise_hosts', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('senderID');
            $table->string('securityCode');
            $table->string('type');
            $table->boolean('enabled')->default(false);
            $table->string('url');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enterprise_hosts');
    }
}
