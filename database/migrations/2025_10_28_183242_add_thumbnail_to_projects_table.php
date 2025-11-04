<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->foreignId('thumbnail_media_id')
                ->nullable()
                ->constrained('media')
                ->nullOnDelete()
                ->after('name');

            $table->string('thumbnail_path')->nullable()->after('thumbnail_media_id');
        });
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropConstrainedForeignId('thumbnail_media_id');
            $table->dropColumn('thumbnail_path');
        });
    }
};
