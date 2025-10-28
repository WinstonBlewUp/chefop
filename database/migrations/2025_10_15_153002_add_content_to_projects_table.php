<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ✅ Corriger les timestamps invalides avant l'ALTER TABLE
        DB::table('projects')
            ->where('created_at', '0000-00-00 00:00:00')
            ->update(['created_at' => now()]);

        DB::table('projects')
            ->where('updated_at', '0000-00-00 00:00:00')
            ->update(['updated_at' => now()]);

        Schema::table('projects', function (Blueprint $table) {
            // Ajouter le champ content pour l'éditeur riche
            $table->longText('content')->nullable()->after('description');

            // Supprimer les anciens champs annexes
            $table->dropColumn([
                'project_type',
                'director',
                'productors',
                'production_company',
                'distributor',
                'award',
                'misc',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // Supprimer le champ content
            $table->dropColumn('content');

            // Restaurer les anciens champs
            $table->string('project_type')->nullable();
            $table->string('director')->nullable();
            $table->string('productors')->nullable();
            $table->string('production_company')->nullable();
            $table->string('distributor')->nullable();
            $table->string('award')->nullable();
            $table->string('misc')->nullable();
        });
    }
};
