<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Media;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class MediaController extends Controller
{
    public function index()
    {
        $media = Media::latest()->get();
        return view('dashboard.media.index', compact('media'));
    }

    public function upload(Request $request)
    {
        try {
            // Support multi-fichiers
            if ($request->hasFile('files')) {
                return $this->uploadMultiple($request);
            }
            
            $request->validate([
                'file' => 'required|file|mimes:jpeg,png,jpg,gif,webp,bmp,tiff,svg,mp4,mov,avi,mkv,wmv,flv,webm,m4v,3gp,ogv,m2v,mts,m2ts,ts,vob,f4v,asf|max:102400', // max 100 Mo
            ], [
                'file.required' => 'Veuillez sélectionner un fichier.',
                'file.file' => 'Le fichier sélectionné n\'est pas valide.',
                'file.mimes' => 'Format de fichier non supporté. Formats acceptés : Images (JPEG, PNG, GIF, WEBP, BMP, TIFF, SVG) et Vidéos (MP4, MOV, AVI, MKV, WMV, FLV, WEBM, M4V, 3GP, OGV, etc.).',
                'file.max' => 'Le fichier est trop volumineux. Taille maximale autorisée : 100 Mo.',
            ]);

            $file = $request->file('file');
            $media = $this->processFile($file);
            
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
            return response()->json([
                'success' => false,
                'message' => $e->validator->errors()->first('file')
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'upload : ' . $e->getMessage()
            ], 500);
        }
    }

    private function uploadMultiple(Request $request)
    {
        $request->validate([
            'files' => 'required|array|max:10',
            'files.*' => 'file|mimes:jpeg,png,jpg,gif,webp,bmp,tiff,svg,mp4,mov,avi,mkv,wmv,flv,webm,m4v,3gp,ogv,m2v,mts,m2ts,ts,vob,f4v,asf|max:102400',
        ], [
            'files.required' => 'Veuillez sélectionner au moins un fichier.',
            'files.array' => 'Format de données invalide.',
            'files.max' => 'Vous ne pouvez pas uploader plus de 10 fichiers à la fois.',
            'files.*.file' => 'Un des fichiers sélectionnés n\'est pas valide.',
            'files.*.mimes' => 'Un ou plusieurs fichiers ont un format non supporté.',
            'files.*.max' => 'Un ou plusieurs fichiers sont trop volumineux (max 100 Mo).',
        ]);

        $results = [];
        $successCount = 0;
        $errors = [];

        foreach ($request->file('files') as $file) {
            try {
                $media = $this->processFile($file);
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

    private function processFile($file)
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
        ]);
    }
}
