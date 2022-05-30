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
        Schema::create('coupon_cart_mappings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('coupon_code', 50);
            $table->decimal('coupon_amount', 8, 2)->default(0.00);
            // $table->string('device_token',255);
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('cart_id')->nullable();
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
        Schema::dropIfExists('coupon_cart_mappings');
    }
};
