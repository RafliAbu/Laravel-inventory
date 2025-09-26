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
        Schema::table('orders', function (Blueprint $table) {
            // 1. Menambahkan kolom baru untuk foreign key
            // Pastikan posisinya rapi, misal setelah 'user_id'
            $table->unsignedBigInteger('product_id')->nullable()->after('user_id');

            // 2. Mendefinisikan foreign key constraint
            // Ini menghubungkan 'product_id' di tabel ini ke 'id' di tabel 'products'
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            // Hapus constraint foreign key terlebih dahulu
            $table->dropForeign(['product_id']);
            
            // Hapus kolomnya
            $table->dropColumn('product_id');
        });
    }
};