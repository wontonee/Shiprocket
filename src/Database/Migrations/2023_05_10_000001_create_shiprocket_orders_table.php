<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, let's determine the data type of the id column in orders table
        $orderIdType = DB::getSchemaBuilder()->getColumnType('orders', 'id');
        
        Schema::create('shiprocket_orders', function (Blueprint $table) use ($orderIdType) {
            $table->id();
            // Match the order_id column type to the id column in orders
            if ($orderIdType == 'bigint') {
                // Need to make it unsigned if the orders.id is unsigned
                $table->unsignedBigInteger('order_id');
            } elseif ($orderIdType == 'int') {
                $table->unsignedInteger('order_id');
            } else {
                $table->unsignedBigInteger('order_id');
            }
            $table->string('shiprocket_order_id')->nullable();
            $table->string('shiprocket_shipment_id')->nullable();
            $table->string('status')->nullable();
            $table->string('tracking_number')->nullable();
            $table->string('courier_name')->nullable();
            $table->string('awb_code')->nullable();
            $table->integer('awb_status')->nullable();
            $table->string('pickup_status')->nullable();
            $table->dateTime('pickup_scheduled_date')->nullable();
            $table->string('pickup_note')->nullable();
            $table->timestamps();
            $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shiprocket_orders');
    }
};