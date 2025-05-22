<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>{{ $page->title }}</title>
    @vite('resources/css/app.css')
</head>
<body>
    @include('components.navbar')
    <h1>{{ $page->title }}</h1>

    <div>
        {!! nl2br(e($page->content)) !!}
        @include('components.grid', ['media' => $page->project?->media])

    </div>

    
</body>
</html>
