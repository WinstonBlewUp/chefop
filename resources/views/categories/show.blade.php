<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>{{ strtoupper($category->name) }}</title>
    @vite('resources/css/app.css')
</head>
<body>
    @include('components.navbar')
    
    <div class="container mx-auto py-8">
        <h1 class="text-3xl font-bold text-center mb-8">{{ strtoupper($category->name) }}</h1>

        @if($projects->count() > 0)
            {{-- Grille des projets de la catégorie --}}
            <div class="flex flex-wrap justify-center gap-4 px-6">
                @foreach ($projects as $project)
                    @php
                        $media = $project->media->first();
                    @endphp
                    @if ($media)
                        <a href="{{ route('pages.show', $project->slug) }}" class="block relative h-60 max-w-[40%] min-w-[15%]">
                            @if (Str::startsWith($media->type, 'image/'))
                                <img
                                    src="{{ asset('storage/' . $media->file_path) }}"
                                    alt="{{ $project->title }}"
                                    class="h-full w-auto object-cover rounded-md shadow mx-auto"
                                />
                            @elseif (Str::startsWith($media->type, 'video/'))
                                <video
                                    src="{{ asset('storage/' . $media->file_path) }}"
                                    class="h-full w-auto object-cover rounded-md shadow mx-auto"
                                    muted autoplay loop
                                ></video>
                            @endif
                        </a>
                    @endif
                @endforeach
            </div>
        @else
            <div class="text-center text-gray-400 py-12">
                <p>Aucun projet dans cette catégorie pour le moment.</p>
            </div>
        @endif
    </div>
</body>
</html>