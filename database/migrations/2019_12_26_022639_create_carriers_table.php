<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCarriersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carriers', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('twilio_account_sid')->nullable();
            $table->string('twilio_auth_token')->nullable();
            $table->string('thinq_account_id')->nullable();
            $table->string('thinq_api_username')->nullable();
            $table->string('thinq_api_token')->nullable();
            $table->integer('priority')->default(10 );
            $table->string('api'); //twilio|thinq
            $table->boolean('enabled')->default(false);
            $table->boolean('beta')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('carriers');
    }
}
