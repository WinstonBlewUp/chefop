<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - Mon App Laravel</title>

    {{-- Exemple de styles simples en ligne ou lien vers CSS si besoin --}}
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 2rem;
            background-color: #f9f9f9;
            color: #333;
        }

        h1 {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }

        p {
            font-size: 1.2rem;
            line-height: 1.6;
        }

        .btn {
            display: inline-block;
            margin-top: 1.5rem;
            padding: 0.75rem 1.5rem;
            background-color: #6366f1;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }

        .btn:hover {
            background-color: #4f46e5;
        }
    </style>
</head>
<body>
    <h1>Bienvenue sur la page d’accueil</h1>
    <p>Ceci est une vue Blade toute simple. Laravel est correctement configuré 🎉</p>
    <a href="{{ route('dashboard') }}" class="btn">Accéder au dashboard</a>
</body>
</html>
