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
        Schema::create('configurations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('location_id')->constrained()->cascadeOnDelete();
            $table->integer('accepted_distance');
            $table->time('presensi_masuk_dari');
            $table->time('presensi_masuk_sampai');
            $table->time('presensi_pulang_dari');
            $table->time('presensi_pulang_sampai');
            $table->time('toleransi_keterlambatan');
            $table->time('maksimal_terlambat');
            $table->char('hari_libur', 10);
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
        Schema::dropIfExists('configurations');
    }
};
