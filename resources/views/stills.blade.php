<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Stills</title>
    @vite('resources/css/app.css')
</head>
<body>
    @include('components.navbar')

    <div class="bg-white w-100 pr-20 py-5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex gap-8">
                {{-- Navigation latérale (cachée car c'est un projet unique) --}}
                <div class="w-40 flex-shrink-0 opacity-0 pointer-events-none">
                    <div class="sticky top-8">
                        <h3 class="font-semibold text-gray-900 text-sm uppercase tracking-wide mb-4">
                            Stills
                        </h3>
                    </div>
                </div>

                {{-- Contenu principal étendu pour correspondre à navbar --}}
                <div class="flex-1 pr-12">
                    <h1 class="text-3xl font-bold text-center mb-8">Stills</h1>

                    @if($stillsProject && $stillsProject->description)
                        <div class="text-center text-gray-600 mb-8">
                            {!! nl2br(e($stillsProject->description)) !!}
                        </div>
                    @endif

                    {{-- Grille des médias --}}
                    @if($stillsProject)
                        @php
                            // Créer un objet page fictif pour le composant grid
                            $page = (object) ['project' => $stillsProject];
                        @endphp
                        @include('components.grid', ['page' => $page])
                    @else
                        <div class="text-center text-gray-400 py-12">Aucun média stills à afficher.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</body>
</html>