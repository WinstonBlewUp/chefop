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

// Créer des grilles pour différentes tailles d'écran
$mediaByRow = [
    'desktop' => [],
    'tablet' => [],
    'mobile' => []
];

if ($page->project) {
    foreach (['desktop' => $desktopRatio, 'tablet' => $tabletRatio, 'mobile' => $mobileRatio] as $breakpoint => $maxRatio) {
        $currentRow = [];
        $currentWidth = 0;

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

            if ($currentWidth >= $maxRatio) {
                $mediaByRow[$breakpoint][] = $currentRow;
                $currentRow = [];
                $currentWidth = 0;
            }
        }

        if (!empty($currentRow)) {
            $mediaByRow[$breakpoint][] = $currentRow;
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
                $media = $project->media->first();
            @endphp
            @if ($media)
                <a href="{{ route('pages.show', ['slug' => $project->slug]) }}" class="block relative h-60 max-w-[40%] min-w-[15%]">
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
    <div class="text-center text-gray-400 py-12">Aucun média à afficher.</div>
@endif
