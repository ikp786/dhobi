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
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('order_number');
            $table->string('razorpay_id')->nullable();
            $table->integer('is_order')->default(0);
            $table->decimal('order_amount', 8, 2);
            $table->decimal('total_product_amount', 8, 2);
            $table->decimal('add_on_service_amount', 8, 2)->default(0.00);
            $table->decimal('deliver_charge', 8, 2);
            $table->string('coupon_code', 50);
            $table->decimal('coupon_amount', 8, 2)->default(0.00);
            // $table->string('offer_product_name')->nullable();
            // $table->time('offer_product_qty')->nullable();
            $table->unsignedBigInteger('user_id');
            // $table->string('device_token',255);
            $table->unsignedBigInteger('driver_id')->nullable();
            $table->unsignedBigInteger('address_id')->nullable();
            $table->string('mobile', 13);
            $table->text('address');
            $table->text('remark');
            $table->string('pincode');
            $table->string('email')->nullable();
            $table->string('txn_id')->nullable();
            $table->enum('payment_method', ['Cod', 'Online'])->nullable()->comment('cod=> cash on delivery, online => payment gateway');
            $table->string('payment_status')->default('Pending');
            $table->enum('order_delivery_status', ['Pending','Canceled','Pickup', 'Deliver'])->default('Pending');
            $table->enum('driver_payment_type', ['Cash', 'Online'])->nullable();
            $table->date('delivery_date')->nullable();
            $table->date('pickup_date')->nullable();
            $table->unsignedBigInteger('pickup_time_slot_id');
            $table->unsignedBigInteger('delivery_time_slot_id');
            $table->string('pickup_time');
            $table->string('delivery_time');
            $table->foreign('driver_id')->references('id')->on('users');
            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('orders');
    }
};
