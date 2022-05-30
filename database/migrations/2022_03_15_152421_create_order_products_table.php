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
        Schema::create('order_products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('order_id');
            $table->unsignedBigInteger('product_id');
            $table->unsignedBigInteger('sub_category_id');
            $table->unsignedBigInteger('category_id');
            $table->string('product_name');
            $table->text('add_on_services')->nullable();
            $table->string('product_quantity')->nullable()->default(0);
            $table->decimal('total_amount', 8, 2);
            $table->decimal('add_on_service_amount', 8, 2)->default(0.00);
            $table->foreign('order_id')->references('id')->on('orders');
            $table->foreign('product_id')->references('id')->on('products');
            $table->foreign('category_id')->references('id')->on('categories');
            // $table->foreign('sub_category_id')->references('id')->on('sub_categories');
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
        Schema::dropIfExists('order_products');
    }
};
