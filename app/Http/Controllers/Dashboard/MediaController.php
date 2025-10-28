<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Media;
use App\Models\Folder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class MediaController extends Controller
{
    public function index(Request $request)
    {
        $folderId = $request->query('folder');

        // Charger le dossier actuel
        $currentFolder = $folderId ? Folder::find($folderId) : null;

        // Charger les sous-dossiers du dossier actuel avec leurs médias
        $folders = Folder::where('parent_id', $folderId)
            ->withCount('media')
            ->with('media')
            ->orderBy('order')
            ->get();

        // Charger uniquement les médias sans dossier (non organisés)
        $media = Media::whereNull('folder_id')->latest()->get();

        return view('dashboard.media.index', compact('media', 'folders', 'currentFolder'));
    }

    public function upload(Request $request)
    {
        \Log::info('Upload request received', [
            'has_file' => $request->hasFile('file'),
            'has_files' => $request->hasFile('files'),
            'file_size' => $request->hasFile('file') ? $request->file('file')->getSize() : null,
            'file_type' => $request->hasFile('file') ? $request->file('file')->getMimeType() : null,
            'all_files' => array_keys($request->allFiles()),
        ]);

        try {
            // Support multi-fichiers
            if ($request->hasFile('files')) {
                return $this->uploadMultiple($request);
            }

            // Si pas de fichier 'file' mais qu'il y a d'autres fichiers, essayons de les traiter
            if (!$request->hasFile('file')) {
                $allFiles = $request->allFiles();
                if (!empty($allFiles)) {
                    // Prendre le premier fichier disponible, quel que soit son nom
                    $fileKey = array_keys($allFiles)[0];
                    $request->merge([$fileKey => $request->file($fileKey)]);
                    // Créer une nouvelle requête avec le fichier sous le nom 'file'
                    $files = $request->allFiles();
                    $firstFile = reset($files);
                    if (is_array($firstFile)) {
                        $firstFile = reset($firstFile);
                    }
                    $request->files->set('file', $firstFile);
                }
            }

            $request->validate([
                'file' => 'required|file|mimes:jpeg,png,jpg,gif,webp,bmp,tiff,svg,mp4,mov,avi,mkv,wmv,flv,webm,m4v,3gp,ogv,m2v,mts,m2ts,ts,vob,f4v,asf|max:122880', // max 120 Mo
            ], [
                'file.required' => 'Veuillez sélectionner un fichier.',
                'file.file' => 'Le fichier sélectionné n\'est pas valide.',
                'file.mimes' => 'Format de fichier non supporté. Formats acceptés : Images (JPEG, PNG, GIF, WEBP, BMP, TIFF, SVG) et Vidéos (MP4, MOV, AVI, MKV, WMV, FLV, WEBM, M4V, 3GP, OGV, etc.).',
                'file.max' => 'Le fichier est trop volumineux. Taille maximale autorisée : 120 Mo.',
            ]);

            $file = $request->file('file');
            $folderId = $request->input('folder_id');
            $media = $this->processFile($file, $folderId);

            return response()->json([
                'success' => true,
                'message' => 'Fichier uploadé avec succès !',
                'media' => [
                    'id' => $media->id,
                    'file_path' => $media->file_path,
                    'type' => $media->type,
                    'url' => $media->url
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error in upload', [
                'errors' => $e->validator->errors()->all(),
                'first_error' => $e->validator->errors()->first('file')
            ]);
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first('file')
            ], 422);
        } catch (\Exception $e) {
            \Log::error('Exception in upload', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'upload : ' . $e->getMessage()
            ], 500);
        }
    }

    private function uploadMultiple(Request $request)
    {
        \Log::info('uploadMultiple called', [
            'files_data' => $request->file('files'),
            'files_count' => $request->file('files') ? count($request->file('files')) : 0,
            'is_array' => is_array($request->file('files')),
        ]);

        try {
            $request->validate([
                'files' => 'required|array|max:10',
                'files.*' => 'file|mimes:jpeg,png,jpg,gif,webp,bmp,tiff,svg,mp4,mov,avi,mkv,wmv,flv,webm,m4v,3gp,ogv,m2v,mts,m2ts,ts,vob,f4v,asf|max:122880',
            ], [
                'files.required' => 'Veuillez sélectionner au moins un fichier.',
                'files.array' => 'Format de données invalide.',
                'files.max' => 'Vous ne pouvez pas uploader plus de 10 fichiers à la fois.',
                'files.*.file' => 'Un des fichiers sélectionnés n\'est pas valide.',
                'files.*.mimes' => 'Un ou plusieurs fichiers ont un format non supporté.',
                'files.*.max' => 'Un ou plusieurs fichiers sont trop volumineux (max 120 Mo).',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Validation error in uploadMultiple', [
                'errors' => $e->validator->errors()->all(),
            ]);
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first(),
                'errors' => $e->validator->errors()->all()
            ], 422);
        }

        $results = [];
        $successCount = 0;
        $errors = [];
        $folderId = $request->input('folder_id');

        foreach ($request->file('files') as $file) {
            try {
                $media = $this->processFile($file, $folderId);
                $results[] = [
                    'id' => $media->id,
                    'file_path' => $media->file_path,
                    'type' => $media->type,
                    'url' => $media->url
                ];
                $successCount++;
            } catch (\Exception $e) {
                $errors[] = $file->getClientOriginalName() . ': ' . $e->getMessage();
            }
        }

        if ($successCount > 0 && empty($errors)) {
            return response()->json([
                'success' => true,
                'message' => "$successCount fichier(s) uploadé(s) avec succès !",
                'media' => $results
            ]);
        } elseif ($successCount > 0 && !empty($errors)) {
            return response()->json([
                'success' => true,
                'message' => "$successCount fichier(s) uploadé(s), " . count($errors) . " erreur(s).",
                'media' => $results,
                'errors' => $errors
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Aucun fichier n\'a pu être uploadé.',
                'errors' => $errors
            ], 422);
        }
    }

    private function processFile($file, $folderId = null)
    {
        // Vérifications supplémentaires
        if (!$file->isValid()) {
            throw new \Exception('Fichier corrompu ou invalide.');
        }

        // Générer un nom unique pour éviter les conflits
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        $fileName = Str::slug($originalName) . '_' . time() . '_' . Str::random(8) . '.' . $extension;

        $path = $file->storeAs('media', $fileName, 'public');

        return Media::create([
            'file_path' => $path,
            'type' => $file->getMimeType(),
            'folder_id' => $folderId,
        ]);
    }

    public function move(Request $request, Media $media)
    {
        $validated = $request->validate([
            'folder_id' => 'nullable|exists:folders,id',
        ]);

        $media->update(['folder_id' => $validated['folder_id']]);

        return response()->json([
            'success' => true,
            'message' => 'Média déplacé avec succès!',
        ]);
    }
}
