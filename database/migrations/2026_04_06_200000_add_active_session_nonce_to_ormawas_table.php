<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('ormawas', function (Blueprint $table) {
            $table->string('active_session_nonce', 120)->nullable()->after('password')->index();
        });
    }

    public function down(): void
    {
        Schema::table('ormawas', function (Blueprint $table) {
            $table->dropIndex(['active_session_nonce']);
            $table->dropColumn('active_session_nonce');
        });
    }
};
