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
        Schema::table('pemesanan', function (Blueprint $table) {
            if (!Schema::hasColumn('pemesanan', 'id_pesanan')) {
                $table->string('id_pesanan', 20)->nullable()->after('alamat');
            }
        });

        // Populate existing orders with TTM-4letters
        $letters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $orders = DB::table('pemesanan')->get();
        foreach ($orders as $order) {
            $code = 'TTM-' . substr(str_shuffle($letters), 0, 4);
            DB::table('pemesanan')->where('id', $order->id)->update(['id_pesanan' => $code]);
        }

        Schema::table('pemesanan', function (Blueprint $table) {
            if (Schema::hasColumn('pemesanan', 'no_po')) {
                $table->dropColumn('no_po');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pemesanan', function (Blueprint $table) {
            if (!Schema::hasColumn('pemesanan', 'no_po')) {
                $table->string('no_po', 50)->nullable()->after('alamat');
            }
            if (Schema::hasColumn('pemesanan', 'id_pesanan')) {
                $table->dropColumn('id_pesanan');
            }
        });
    }
};
