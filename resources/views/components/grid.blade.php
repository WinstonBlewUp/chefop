 <?php
/* 
 $mediaByRow = [];
 $currentRow = [];
 $currentWidth = 0;
 $maxRowRatio = 5;

 if ($page->project) {
     foreach ($page->project->media as $media) {
         $ratio = 1.77;
         if (Str::startsWith($media->type, 'image/')) {
             $imagePath = storage_path("app/public/" . $media->file_path);
             if (file_exists($imagePath)) {
                 [$width, $height] = getimagesize($imagePath);
                 $ratio = $width / $height;
             }
         }

         $currentRow[] = ['media' => $media, 'ratio' => $ratio];
         $currentWidth += $ratio;

         if ($currentWidth >= $maxRowRatio) {
             $mediaByRow[] = $currentRow;
             $currentRow = [];
             $currentWidth = 0;
         }
     }

     if (!empty($currentRow)) {
         $mediaByRow[] = $currentRow;
     }
 }
?>
@if ($page->project && $page->project->media->isNotEmpty())
 <div class="space-y-4 px-6">
     @foreach ($mediaByRow as $row)
         <div class="flex justify-center gap-2">
             @php
                 $totalRatio = array_sum(array_column($row, 'ratio'));
             @endphp
             @foreach ($row as $item)
                 @php
                     $flexGrow = $item['ratio'] / $totalRatio;
                 @endphp
                 <div class="relative" style="flex: {{ $flexGrow }};">
                     @if (Str::startsWith($item['media']->type, 'image/'))
                         <img
                             src="{{ asset('storage/' . $item['media']->file_path) }}"
                             alt="media"
                             class="h-60 w-full object-cover -md shadow"
                         />
                     @elseif (Str::startsWith($item['media']->type, 'video/'))
                         <video
                             src="{{ asset('storage/' . $item['media']->file_path) }}"
                             class="h-60 w-full object-cover -md shadow"
                             controls
                         ></video>
                     @endif
                 </div>
             @endforeach
         </div>
     @endforeach
 </div>
@else
 <div class="text-center text-gray-400 py-12">Aucun média à afficher.</div>
@endif
 */


use Illuminate\Support\Str;

// Configuration responsive pour les ratios
$desktopRatio = 5;    // Desktop: ~5 images par ligne
$tabletRatio = 3.5;   // Tablet: ~3-4 images par ligne
$mobileRatio = 2.2;   // Mobile: ~2-3 images par ligne

// Limites strictes par breakpoint
$maxMediaPerRow = [
    'desktop' => 5,   // Maximum 5 médias par rangée
    'tablet' => 4,    // Maximum 4 médias par rangée
    'mobile' => 3     // Maximum 3 médias par rangée
];

// Créer des grilles pour différentes tailles d'écran
$mediaByRow = [
    'desktop' => [],
    'tablet' => [],
    'mobile' => []
];

if ($page->project) {
    foreach (['desktop' => $desktopRatio, 'tablet' => $tabletRatio, 'mobile' => $mobileRatio] as $breakpoint => $maxRatio) {
        $maxCount = $maxMediaPerRow[$breakpoint];

        // Préparation des médias avec leurs ratios
        $mediaWithRatios = [];
        foreach ($page->project->media as $media) {
            $ratio = 1.77;
            if (Str::startsWith($media->type, 'image/')) {
                $imagePath = storage_path("app/public/" . $media->file_path);
                if (file_exists($imagePath)) {
                    [$width, $height] = getimagesize($imagePath);
                    $ratio = $width / $height;
                }
            }
            $mediaWithRatios[] = ['media' => $media, 'ratio' => $ratio];
        }

        // Tri par ratio décroissant (photos les plus larges en premier)
        usort($mediaWithRatios, function($a, $b) {
            return $b['ratio'] <=> $a['ratio'];
        });

        $currentRow = [];
        $currentWidth = 0;
        $totalMedias = count($mediaWithRatios);
        $processedCount = 0;

        foreach ($mediaWithRatios as $item) {
            $currentRow[] = $item;
            $currentWidth += $item['ratio'];
            $processedCount++;

            $remainingMedias = $totalMedias - $processedCount;
            $currentRowCount = count($currentRow);

            // Conditions de fermeture de rangée
            $shouldCloseRow = false;

            // 1. Maximum atteint (ratio ou nombre)
            if ($currentWidth >= $maxRatio || $currentRowCount >= $maxCount) {
                $shouldCloseRow = true;
            }

            // 2. Éviter un item seul sur la dernière ligne
            if ($remainingMedias == 1 && $currentRowCount >= 2) {
                $shouldCloseRow = true;
            }

            // 3. Si on a 2+ items et que ça risque de laisser 1 seul item
            if ($remainingMedias == 2 && $currentRowCount >= 3) {
                $shouldCloseRow = true;
            }

            if ($shouldCloseRow) {
                $mediaByRow[$breakpoint][] = $currentRow;
                $currentRow = [];
                $currentWidth = 0;
            }
        }

        // Traitement de la dernière rangée
        if (!empty($currentRow)) {
            $mediaByRow[$breakpoint][] = $currentRow;
        }

        // Post-traitement : rééquilibrage automatique des rangées
        $rows = &$mediaByRow[$breakpoint];
        $totalRows = count($rows);

        if ($totalRows >= 2) {
            // Première passe : redistribuer pour éviter les déséquilibres majeurs
            for ($i = 0; $i < $totalRows - 1; $i++) {
                $currentRowRef = &$rows[$i];
                $nextRowRef = &$rows[$i + 1];

                $currentCount = count($currentRowRef);
                $nextCount = count($nextRowRef);

                // Si rangée suivante a 2 items ou moins et rangée actuelle a 4+ items
                if ($nextCount <= 2 && $currentCount >= 4) {
                    // Si on peut déplacer un item de la rangée actuelle vers la suivante
                    if ($currentCount > 2 && $nextCount < $maxCount) {
                        // Prendre le dernier item de la rangée actuelle (plus petit ratio)
                        $itemToMove = array_pop($currentRowRef);
                        // L'ajouter au début de la rangée suivante
                        array_unshift($nextRowRef, $itemToMove);

                        // Vérifier si on peut encore en déplacer un
                        if (count($currentRowRef) >= 4 && count($nextRowRef) < $maxCount) {
                            $itemToMove2 = array_pop($currentRowRef);
                            array_unshift($nextRowRef, $itemToMove2);
                        }
                    }
                }
            }

            // Deuxième passe : traiter spécifiquement les rangées avec exactement 2 médias
            for ($i = 0; $i < count($rows); $i++) {
                if (count($rows[$i]) == 2) {
                    $rowWith2 = &$rows[$i];
                    $ratioRow2 = array_sum(array_column($rowWith2, 'ratio'));

                    // Vérifier si le ratio permet de fusionner avec une autre rangée
                    if ($ratioRow2 <= ($maxRatio * 0.6)) { // Si ratio faible, essayer de fusionner

                        // Chercher une rangée précédente ou suivante avec de la place
                        $moved = false;

                        // Essayer avec la rangée précédente
                        if ($i > 0 && count($rows[$i-1]) < $maxCount) {
                            $prevRowRatio = array_sum(array_column($rows[$i-1], 'ratio'));
                            if ($prevRowRatio + $ratioRow2 <= $maxRatio * 1.2) { // Tolérance de 20%
                                // Déplacer tous les items de la rangée avec 2 vers la précédente
                                foreach ($rowWith2 as $item) {
                                    $rows[$i-1][] = $item;
                                }
                                array_splice($rows, $i, 1); // Supprimer la rangée vide
                                $moved = true;
                            }
                        }

                        // Sinon essayer avec la rangée suivante
                        if (!$moved && $i < count($rows) - 1 && count($rows[$i+1]) < $maxCount) {
                            $nextRowRatio = array_sum(array_column($rows[$i+1], 'ratio'));
                            if ($nextRowRatio + $ratioRow2 <= $maxRatio * 1.2) {
                                // Déplacer tous les items vers la rangée suivante
                                foreach (array_reverse($rowWith2) as $item) {
                                    array_unshift($rows[$i+1], $item);
                                }
                                array_splice($rows, $i, 1); // Supprimer la rangée vide
                                $moved = true;
                            }
                        }

                        if ($moved) {
                            $i--; // Réévaluer l'index après suppression
                        }
                    }
                }
            }

            // Traitement spécial pour la dernière rangée si elle n'a qu'un item
            $lastRowIndex = count($rows) - 1;
            if ($lastRowIndex > 0 && count($rows[$lastRowIndex]) == 1) {
                $previousRowIndex = $lastRowIndex - 1;

                // Si la rangée précédente a de la place
                if (count($rows[$previousRowIndex]) < $maxCount) {
                    $singleItem = $rows[$lastRowIndex][0];
                    $rows[$previousRowIndex][] = $singleItem;
                    // Supprimer la rangée qui ne contenait qu'un item
                    array_pop($rows);
                }
            }
        }
    }
}
?>

@if ($page->project && $page->project->media->isNotEmpty())
    {{-- Grille responsive masonry --}}

    {{-- Mobile: 2-3 images par ligne, hauteur réduite --}}
    <div class="block sm:hidden space-y-2 px-2">
        @foreach ($mediaByRow['mobile'] as $row)
            <div class="flex justify-center gap-1">
                @php
                    $totalRatio = array_sum(array_column($row, 'ratio'));
                @endphp
                @foreach ($row as $item)
                    @php
                        $flexGrow = $item['ratio'] / $totalRatio;
                    @endphp
                    <div class="relative shadow-sm" style="flex: {{ $flexGrow }};">
                        @if (Str::startsWith($item['media']->type, 'image/'))
                            <img
                                src="{{ asset('storage/' . $item['media']->file_path) }}"
                                alt="media"
                                class="h-32 w-full object-cover rounded-none"
                            />
                        @elseif (Str::startsWith($item['media']->type, 'video/'))
                            <video
                                src="{{ asset('storage/' . $item['media']->file_path) }}"
                                class="h-32 w-full object-cover rounded-none"
                                controls
                            ></video>
                        @endif
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>

    {{-- Tablet: 3-4 images par ligne, hauteur moyenne --}}
    <div class="hidden sm:block lg:hidden space-y-2 px-4">
        @foreach ($mediaByRow['tablet'] as $row)
            <div class="flex justify-center gap-1.5">
                @php
                    $totalRatio = array_sum(array_column($row, 'ratio'));
                @endphp
                @foreach ($row as $item)
                    @php
                        $flexGrow = $item['ratio'] / $totalRatio;
                    @endphp
                    <div class="relative shadow" style="flex: {{ $flexGrow }};">
                        @if (Str::startsWith($item['media']->type, 'image/'))
                            <img
                                src="{{ asset('storage/' . $item['media']->file_path) }}"
                                alt="media"
                                class="h-40 w-full object-cover rounded-none"
                            />
                        @elseif (Str::startsWith($item['media']->type, 'video/'))
                            <video
                                src="{{ asset('storage/' . $item['media']->file_path) }}"
                                class="h-40 w-full object-cover rounded-none"
                                controls
                            ></video>
                        @endif
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>

    {{-- Desktop: 4-5 images par ligne, hauteur complète (comme avant) --}}
    <div class="hidden lg:block space-y-3">
        @foreach ($mediaByRow['desktop'] as $row)
            <div class="flex justify-center gap-2">
                @php
                    $totalRatio = array_sum(array_column($row, 'ratio'));
                @endphp
                @foreach ($row as $item)
                    @php
                        $flexGrow = $item['ratio'] / $totalRatio;
                    @endphp
                    <div class="relative shadow" style="flex: {{ $flexGrow }};">
                        @if (Str::startsWith($item['media']->type, 'image/'))
                            <img
                                src="{{ asset('storage/' . $item['media']->file_path) }}"
                                alt="media"
                                class="h-48 w-full object-cover rounded-none"
                            />
                        @elseif (Str::startsWith($item['media']->type, 'video/'))
                            <video
                                src="{{ asset('storage/' . $item['media']->file_path) }}"
                                class="h-48 w-full object-cover rounded-none"
                                controls
                            ></video>
                        @endif
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>

@elseif ($page->category && $page->category->projects->isNotEmpty())
    {{-- Grille via collection : première image de chaque projet, cliquable --}}
    <div class="flex flex-wrap justify-center gap-4">
        @foreach ($page->category->projects as $project)
            @php
                // Sélectionner le média avec le meilleur ratio pour remplir l'espace
                $idealRatio = 1.5; // Ratio idéal pour un bon remplissage
                $bestMedia = null;
                $bestScore = PHP_FLOAT_MAX;

                foreach ($project->media as $media) {
                    $ratio = 1.77; // ratio par défaut
                    if (Str::startsWith($media->type, 'image/')) {
                        $imagePath = storage_path("app/public/" . $media->file_path);
                        if (file_exists($imagePath)) {
                            [$width, $height] = getimagesize($imagePath);
                            $ratio = $width / $height;
                        }
                    }

                    // Calculer la différence avec le ratio idéal
                    $score = abs($ratio - $idealRatio);
                    if ($score < $bestScore) {
                        $bestScore = $score;
                        $bestMedia = $media;
                    }
                }

                $media = $bestMedia ?: $project->media->first();
            @endphp
            @if ($media)
                <a href="{{ route('pages.show', ['slug' => $project->slug]) }}" class="block relative h-60 max-w-[40%] min-w-[15%] group overflow-hidden">
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
                    {{-- Overlay discret en bas à gauche avec slide in --}}
                    <div class="absolute bottom-3 left-3 bg-white bg-opacity-90 backdrop-blur-sm rounded-sm px-2 py-1 transform translate-y-2 opacity-0 group-hover:translate-y-0 group-hover:opacity-100 transition-all duration-300">
                        <span class="text-gray-900 font-medium text-sm">
                            {{ $project->title }}
                        </span>
                    </div>
                </a>
            @endif
        @endforeach
    </div>

@else
    <div class="text-center text-gray-400 py-12">Aucun média à afficher.</div>
@endif
