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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('shipping_city_id')->nullable()->after('shipping_method_id');
            $table->string('shipping_city')->nullable()->after('shipping_city_id');
            $table->string('shipping_province')->nullable()->after('shipping_city');
            $table->integer('shipping_weight')->nullable()->after('shipping_cost');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['shipping_city_id', 'shipping_city', 'shipping_province', 'shipping_weight']);
        });
    }
};
