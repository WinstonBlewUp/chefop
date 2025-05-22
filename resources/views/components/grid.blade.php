 <?php

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
