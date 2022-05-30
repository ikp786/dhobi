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
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name',50)->nullable();
            $table->string('email',100)->unique()->nullable();
            $table->string('mobile', 15)->nullable();
            $table->string('unique_id',50)->nullable();
            $table->enum('online_status',['Online','Offline'])->default('Offline');
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->integer('role');
            $table->integer('otp')->nullable();
            $table->text('address')->nullable();
            $table->date('dob')->nullable();
            $table->string('profile_pic',250)->nullable();
            $table->string('device_token',355)->nullable();
            $table->rememberToken();
            $table->softDeletes();
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
        Schema::dropIfExists('users');
    }
};
