<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // Tautan akun mobile (users) ke profil customer.
            // wa_id dibuat nullable agar user yang daftar via mobile
            // (belum punya JID WhatsApp) tetap bisa punya customer profile.
            $table->foreignId('user_id')->nullable()->unique()->after('id')
                ->constrained()->nullOnDelete();
            $table->string('wa_id', 50)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('user_id');
            // Kembalikan NOT NULL hanya bila tidak ada baris ber-wa_id null.
            // $table->string('wa_id', 50)->nullable(false)->change();
        });
    }
};
