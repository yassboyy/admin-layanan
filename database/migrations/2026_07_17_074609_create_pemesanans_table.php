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
        Schema::create('pemesanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nama_pelanggan');
            $table->string('no_hp', 20);
            $table->string('email');
            $table->text('alamat');
            $table->string('id_pesanan', 20)->nullable();
            $table->text('catatan')->nullable();
            $table->bigInteger('diskon')->default(0);
            $table->bigInteger('total_harga');
            $table->integer('status_tipe');
            $table->string('bukti_po')->nullable();
            $table->string('bukti_surat_perjanjian_kerja')->nullable();
            $table->string('bukti_dp')->nullable();
            $table->string('bukti_lunas')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemesanan');
    }
};
