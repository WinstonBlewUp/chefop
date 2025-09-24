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
        Schema::table('projects', function (Blueprint $table) {
            // Vérifier si les colonnes existent déjà avant de les ajouter
            if (!Schema::hasColumn('projects', 'project_type')) {
                $table->string('project_type')->nullable();
            }
            if (!Schema::hasColumn('projects', 'director')) {
                $table->string('director')->nullable();
            }
            if (!Schema::hasColumn('projects', 'productors')) {
                $table->string('productors')->nullable();
            }
            if (!Schema::hasColumn('projects', 'production_company')) {
                $table->string('production_company')->nullable();
            }
            if (!Schema::hasColumn('projects', 'distributor')) {
                $table->string('distributor')->nullable();
            }
            if (!Schema::hasColumn('projects', 'award')) {
                $table->string('award')->nullable();
            }
            if (!Schema::hasColumn('projects', 'misc')) {
                $table->string('misc')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
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
};
