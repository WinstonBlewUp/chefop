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
            'color' => 'nullable|string|max:7',
        ]);

        $folder = Folder::create([
            'name' => $validated['name'],
            'parent_id' => $validated['parent_id'] ?? null,
            'color' => $validated['color'] ?? '#3B82F6',
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
            'color' => 'nullable|string|max:7',
        ]);

        $folder->update($validated);

        return response()->json([
            'success' => true,
            'folder' => $folder,
            'message' => 'Dossier mis à jour avec succès!',
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

    public function reorder(Request $request)
    {
        $validated = $request->validate([
            'folders' => 'required|array',
            'folders.*.id' => 'required|exists:folders,id',
            'folders.*.order' => 'required|integer',
        ]);

        foreach ($validated['folders'] as $folderData) {
            Folder::where('id', $folderData['id'])->update(['order' => $folderData['order']]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Ordre des dossiers mis à jour!',
        ]);
    }
}
