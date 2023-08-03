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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->decimal('latitude_masuk', 10, 8);
            $table->decimal('latitude_pulang', 10, 8)->nullable();
            $table->decimal('longitude_masuk', 11, 8);
            $table->decimal('longitude_pulang', 11, 8)->nullable();
            $table->float('distance_masuk');
            $table->float('distance_pulang')->nullable();
            $table->timestamp('jam_masuk');
            $table->timestamp('jam_pulang')->nullable();
            $table->integer('lembur')->nullable();
            $table->enum('status', ['Hadir', 'Terlambat', 'Izin', 'Alpha', 'Libur', 'Belum ditentukan'])->default('Belum ditentukan');
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
        Schema::dropIfExists('attendances');
    }
};
