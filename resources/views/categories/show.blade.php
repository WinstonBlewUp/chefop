<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ strtoupper($category->name) }}</title>
    @vite('resources/css/app.css')
</head>
<body>
    @include('components.navbar')
    
    <div class="container mx-auto py-8">
        <!-- <h1 class="text-3xl font-bold text-center mb-8">{{ strtoupper($category->name) }}</h1> -->

        @if($projects->count() > 0)
            {{-- Grille des projets de la catégorie --}}
            <div class="flex flex-wrap justify-center gap-4 px-6">
                @foreach ($projects as $project)
                    @php
                        // Utiliser la thumbnail si elle existe, sinon sélectionner le média avec le meilleur ratio
                        if ($project->thumbnail) {
                            $media = $project->thumbnail;
                        } else {
                            $idealRatio = 1.5; // Ratio idéal pour un bon remplissage
                            $bestMedia = null;
                            $bestScore = PHP_FLOAT_MAX;

                            foreach ($project->media as $mediaItem) {
                                $ratio = 1.77; // ratio par défaut
                                if (Str::startsWith($mediaItem->type, 'image/')) {
                                    $imagePath = storage_path("app/public/" . $mediaItem->file_path);
                                    if (file_exists($imagePath)) {
                                        [$width, $height] = getimagesize($imagePath);
                                        $ratio = $width / $height;
                                    }
                                }

                                // Calculer la différence avec le ratio idéal
                                $score = abs($ratio - $idealRatio);
                                if ($score < $bestScore) {
                                    $bestScore = $score;
                                    $bestMedia = $mediaItem;
                                }
                            }

                            $media = $bestMedia ?: $project->media->first();
                        }
                    @endphp
                    <a href="{{ route('pages.show', $project->slug) }}" class="block relative h-60 max-w-[40%] min-w-[15%] group overflow-hidden">
                        @if ($media)
                            @if (Str::startsWith($media->type, 'image/'))
                                <img
                                    src="{{ asset('storage/' . $media->file_path) }}"
                                    alt="{{ $project->title }}"
                                    class="h-full w-auto object-cover rounded-none shadow mx-auto"
                                />
                            @elseif ($media->is_external)
                                <iframe
                                    src="{{ $media->getEmbedUrl() }}"
                                    class="h-full w-auto rounded-none shadow mx-auto pointer-events-none"
                                    frameborder="0"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen
                                ></iframe>
                            @elseif (Str::startsWith($media->type, 'video/'))
                                <video
                                    src="{{ asset('storage/' . $media->file_path) }}"
                                    class="h-full w-auto object-cover rounded-none shadow mx-auto"
                                    muted autoplay loop
                                ></video>
                            @endif
                        @else
                            {{-- Placeholder gris si aucun média --}}
                            <div class="h-full w-48 bg-gray-300 rounded-none shadow mx-auto flex items-center justify-center">
                                <svg class="w-16 h-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                        @endif
                        {{-- Overlay discret en bas à gauche avec slide in --}}
                        <div class="absolute bottom-3 left-3 bg-white bg-opacity-90 backdrop-blur-sm rounded-sm px-2 py-1 transform translate-y-2 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-300">
                            <span class="text-gray-900 font-medium text-sm">
                                {{ $project->title }}
                            </span>
                        </div>
                    </a>
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