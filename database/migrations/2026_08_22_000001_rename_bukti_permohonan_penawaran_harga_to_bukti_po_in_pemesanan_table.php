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
        Schema::table('pemesanan', function (Blueprint $table) {
            if (Schema::hasColumn('pemesanan', 'bukti_permohonan_penawaran_harga') && !Schema::hasColumn('pemesanan', 'bukti_po')) {
                $table->renameColumn('bukti_permohonan_penawaran_harga', 'bukti_po');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pemesanan', function (Blueprint $table) {
            if (Schema::hasColumn('pemesanan', 'bukti_po') && !Schema::hasColumn('pemesanan', 'bukti_permohonan_penawaran_harga')) {
                $table->renameColumn('bukti_po', 'bukti_permohonan_penawaran_harga');
            }
        });
    }
};
