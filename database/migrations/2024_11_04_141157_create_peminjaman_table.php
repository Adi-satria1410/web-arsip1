<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('peminjaman', function (Blueprint $table) {
            $table->id();
            $table->json('jenis_dokumen');
            $table->string('jenis_no_hak_di_208')->nullable()->change();
            $table->string('peminjam');
            $table->string('keperluan', 255);
            $table->string('nomor_dokumen');
            $table->date('tanggal_peminjaman');
            $table->date('tanggal_pengembalian')->nullable();
            $table->timestamp('tanggal_dikembalikan')->nullable();
            $table->string('status')->default('Dipinjam'); // Dipinjam, Dikembalikan
            $table->string('regency_id')->nullable(); // Kolom kecamatan
            $table->string('district_id')->nullable();
            $table->string('village_id')->nullable();
            $table->string('denda')->nullable();
            $table->boolean('status_pengembalian')->default(false);
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peminjaman');
    }
};
