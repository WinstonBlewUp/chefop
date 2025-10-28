<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Vérifie si la colonne n'existe pas déjà
            if (!Schema::hasColumn('projects', 'is_locked')) {
                $table->boolean('is_locked')->default(false)->after('is_selected_work');
            }
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            if (Schema::hasColumn('projects', 'is_locked')) {
                $table->dropColumn('is_locked');
            }
        });
    }
};
