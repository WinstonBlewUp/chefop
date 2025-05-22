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
        return view('dashboard.media.index');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:jpeg,png,jpg,gif,mp4,mov,avi|max:51200', // max 50 Mo
        ]);

        $file = $request->file('file');
        $path = $file->store('media', 'public');

        Media::create([
            'file_path' => $path,
            'type' => $file->getMimeType(),
        ]);

        return response()->json(['success' => true]);
    }
}
