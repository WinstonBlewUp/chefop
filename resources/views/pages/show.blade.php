<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>{{ $page->title }}</title>
</head>
<body>
    <h1>{{ $page->title }}</h1>

    <div>
        {!! nl2br(e($page->content)) !!}
    </div>
</body>
</html>
