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
        Schema::create('incoming_goods', function (Blueprint $table) {
            $table->id();
            $table->date('tanggal');
            $table->foreignId('supplier_id')->constrained('suppliers')->onDelete('cascade');
            $table->string('no_surat_jalan');
            $table->string('rkm_po');
            $table->string('po');
            $table->string('project');
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
        Schema::dropIfExists('incoming_goods');
    }
};