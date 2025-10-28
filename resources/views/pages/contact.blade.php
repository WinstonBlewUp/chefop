<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $page->title ?? 'Contact' }}</title>
    @vite('resources/css/app.css')
</head>
<body class="bg-white text-center font-[Inter]">

    {{-- Navbar --}}
    @include('components.navbar')

    <div class="py-20 px-4 max-w-4xl mx-auto">
        

        {{-- Contenu issu du dashboard --}}
        <div class="prose prose-sm max-w-none text-left">
            {!! $page->content !!}
        </div>
    </div>

</body>
</html>
