<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Selected Work</title>
    @vite('resources/css/app.css')
</head>
<body>
    @include('components.navbar')
    
    <div class="bg-white w-full lg:pr-20 py-5">
        <div class="max-w-7xl mx-auto px-2 sm:px-4 lg:px-8">
            <div class="flex gap-4 lg:gap-8">
                {{-- Navigation latérale des projets - Cachée sur mobile --}}
                <div class="hidden lg:block w-40 flex-shrink-0 {{ $selectedWorkProjects->count() > 1 ? 'opacity-100' : 'opacity-0 pointer-events-none' }}">
                    <div class="sticky top-8">
                        <h3 class="font-semibold text-gray-900 text-sm uppercase tracking-wide mb-4">
                            Selected Work
                        </h3>
                        <div class="space-y-2">
                            @foreach($selectedWorkProjects as $project)
                                <a href="{{ route('pages.show', $project->slug) }}"
                                   class="block py-2 px-2 text-sm transition-colors hover:bg-gray-100 text-gray-700">
                                    {{ $project->title }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Contenu principal responsive --}}
                <div class="flex-1 pr-0 lg:pr-12">
                    <h1 class="text-3xl font-bold text-center mb-8">Selected Work</h1>

                    {{-- Grille des projets Selected Work --}}
                    @if($selectedWorkProjects->isNotEmpty())
                        <div class="flex flex-wrap justify-center gap-2 sm:gap-4">
                            @foreach ($selectedWorkProjects as $project)
                                @php
                                    $media = $project->media->first();
                                @endphp
                                @if ($media)
                                    {{-- Responsive sizing: pleine largeur sur mobile, puis adaptatif --}}
                                    <a href="{{ route('pages.show', $project->slug) }}" class="block relative h-48 sm:h-60 w-full sm:max-w-[45%] lg:max-w-[40%] lg:min-w-[15%]">
                                        @if (Str::startsWith($media->type, 'image/'))
                                            <img
                                                src="{{ asset('storage/' . $media->file_path) }}"
                                                alt="media"
                                                class="h-full w-auto object-cover shadow mx-auto rounded-none"
                                            />
                                        @elseif (Str::startsWith($media->type, 'video/'))
                                            <video
                                                src="{{ asset('storage/' . $media->file_path) }}"
                                                class="h-full w-auto object-cover shadow mx-auto rounded-none"
                                                muted autoplay loop
                                            ></video>
                                        @endif
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <div class="text-center text-gray-400 py-12">Aucun projet sélectionné à afficher.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</body>
</html>
