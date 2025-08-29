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
            $request->validate([
                'file' => 'required|file|mimes:jpeg,png,jpg,gif,webp,mp4,mov,avi,mkv,wmv,flv|max:102400', // max 100 Mo
            ], [
                'file.required' => 'Veuillez sélectionner un fichier.',
                'file.file' => 'Le fichier sélectionné n\'est pas valide.',
                'file.mimes' => 'Format de fichier non supporté. Formats acceptés : JPEG, PNG, JPG, GIF, WEBP, MP4, MOV, AVI, MKV, WMV, FLV.',
                'file.max' => 'Le fichier est trop volumineux. Taille maximale autorisée : 100 Mo.',
            ]);

            $file = $request->file('file');
            
            // Vérifications supplémentaires
            if (!$file->isValid()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Fichier corrompu ou invalide.'
                ], 422);
            }

            // Générer un nom unique pour éviter les conflits
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            $fileName = Str::slug($originalName) . '_' . time() . '.' . $extension;
            
            $path = $file->storeAs('media', $fileName, 'public');

            $media = Media::create([
                'file_path' => $path,
                'type' => $file->getMimeType(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Fichier uploadé avec succès !',
                'media' => $media
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
}
