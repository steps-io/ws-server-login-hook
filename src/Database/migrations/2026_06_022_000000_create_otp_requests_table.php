<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ws_login_hook_otp_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('otp')->nullable();
            $table->string('profile_name')->nullable();
            $table->string('mobile')->nullable();
            $table->text('serial_number')->nullable();
       
            $table->string('locale')->default('ar');
            $table->enum('status', ['pending', 'verified'])->default('pending');
            $table->string('model_type')->nullable();
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
        Schema::dropIfExists('ws_login_hook_otp_requests');
    }
};
