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
        // Check if stills category exists
        $stillsCategory = DB::table('categories')->where('slug', 'stills')->first();

        if ($stillsCategory) {
            // Check if stills project already exists
            $existingStillsProject = DB::table('projects')->where('slug', 'stills')->first();

            if ($existingStillsProject) {
                // Update existing project to be locked
                DB::table('projects')->where('id', $existingStillsProject->id)->update([
                    'is_locked' => true,
                    'updated_at' => now(),
                ]);
                $stillsProjectId = $existingStillsProject->id;
            } else {
                // Create stills project
                $stillsProjectId = DB::table('projects')->insertGetId([
                    'title' => 'Stills',
                    'slug' => 'stills',
                    'description' => 'Collection de photos stills',
                    'is_locked' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            // Create corresponding page for the project if it doesn't exist
            $existingPage = DB::table('pages')->where('slug', 'stills')->first();
            if (!$existingPage) {
                DB::table('pages')->insert([
                    'title' => 'Stills',
                    'slug' => 'stills',
                    'content' => '<p>Collection de photos stills</p>',
                    'project_id' => $stillsProjectId,
                    'published' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            } else {
                // Update existing page to link to the project
                DB::table('pages')->where('id', $existingPage->id)->update([
                    'project_id' => $stillsProjectId,
                    'updated_at' => now(),
                ]);
            }

            // Move projects from stills category to have no category (they'll be handled as individual media in stills project)
            $stillsProjects = DB::table('projects')->where('category_id', $stillsCategory->id)->get();

            // Move media from old stills projects to the new stills project
            foreach ($stillsProjects as $project) {
                $mediaIds = DB::table('media_project')->where('project_id', $project->id)->pluck('media_id');

                // Insert media relationships for new stills project
                foreach ($mediaIds as $mediaId) {
                    DB::table('media_project')->insert([
                        'media_id' => $mediaId,
                        'project_id' => $stillsProjectId,
                    ]);
                }

                // Remove old media relationships
                DB::table('media_project')->where('project_id', $project->id)->delete();
            }

            // Delete old stills projects
            DB::table('projects')->where('category_id', $stillsCategory->id)->delete();

            // Delete stills category
            DB::table('categories')->where('id', $stillsCategory->id)->delete();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Recreate stills category
        $stillsCategoryId = DB::table('categories')->insertGetId([
            'name' => 'Stills',
            'slug' => 'stills',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Find stills project
        $stillsProject = DB::table('projects')->where('slug', 'stills')->where('is_locked', true)->first();

        if ($stillsProject) {
            // Delete the page associated with stills project
            DB::table('pages')->where('project_id', $stillsProject->id)->delete();

            // Delete the stills project
            DB::table('projects')->where('id', $stillsProject->id)->delete();
        }
    }
};
