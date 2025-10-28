<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Folder;
use Illuminate\Http\Request;

class FolderController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:folders,id',
        ]);

        $folder = Folder::create([
            'name' => $validated['name'],
            'parent_id' => $validated['parent_id'] ?? null,
            'order' => Folder::where('parent_id', $validated['parent_id'] ?? null)->max('order') + 1,
        ]);

        return response()->json([
            'success' => true,
            'folder' => $folder->load('children', 'media'),
            'message' => 'Dossier créé avec succès!',
        ]);
    }

    public function update(Request $request, Folder $folder)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $folder->update($validated);

        return response()->json([
            'success' => true,
            'folder' => $folder,
            'message' => 'Dossier renommé avec succès!',
        ]);
    }

    public function destroy(Folder $folder)
    {
        // Déplacer les médias et sous-dossiers vers le parent
        $folder->media()->update(['folder_id' => $folder->parent_id]);
        $folder->children()->update(['parent_id' => $folder->parent_id]);

        $folder->delete();

        return response()->json([
            'success' => true,
            'message' => 'Dossier supprimé avec succès!',
        ]);
    }
}
