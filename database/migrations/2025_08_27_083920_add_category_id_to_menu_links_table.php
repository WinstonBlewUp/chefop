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
        Schema::table('menu_links', function (Blueprint $table) {
            $table->unsignedBigInteger('category_id')->nullable()->after('page_id');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
            
            // Modifier la contrainte pour permettre page_id OU category_id (pas les deux)
            $table->index(['page_id', 'category_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('menu_links', function (Blueprint $table) {
            $table->dropForeign(['category_id']);
            $table->dropIndex(['page_id', 'category_id']);
            $table->dropColumn('category_id');
        });
    }
};
