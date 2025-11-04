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
        Schema::table('media', function (Blueprint $table) {
            // Rendre file_path nullable pour supporter les vidéos externes
            $table->string('file_path')->nullable()->change();

            // Ajouter les champs pour les vidéos externes
            $table->boolean('is_external')->default(false)->after('type');
            $table->text('external_url')->nullable()->after('is_external');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('media', function (Blueprint $table) {
            $table->string('file_path')->nullable(false)->change();
            $table->dropColumn(['is_external', 'external_url']);
        });
    }
};
