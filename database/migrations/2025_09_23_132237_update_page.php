<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->string('project_type')->nullable()->after('is_selected_work');
            $table->string('director')->nullable()->after('project_type');
            $table->string('productors')->nullable()->after('director');
            $table->string('production_company')->nullable()->after('productors');
            $table->string('distributor')->nullable()->after('production_company');
            $table->string('award')->nullable()->after('distributor');
            $table->text('misc')->nullable()->after('award');
        });
    }

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
                'misc'
            ]);
        });
    }
};
