<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contact</title>
    @vite('resources/css/app.css')
</head>

<body class="bg-white text-center font-[Inter]">
    @include('components.navbar')

    <div class="py-20 px-4 max-w-4xl mx-auto">
        <!-- Titre RESEAUX -->
        <h1 class="text-[32px] font-bold leading-[39px] mb-4">RESEAUX</h1>

        <!-- Texte -->
        <p class="text-[14px] font-bold leading-[19px] mb-1">TEXTE</p>
        <p class="text-[14px] font-normal leading-[19px] max-w-[188px] mx-auto mb-6">
            Texte plus long<br>
            Catégorie : Info intéressante
        </p>

        <!-- Emplacements des pseudos -->
        <div class="flex justify-center gap-16 text-[14px] leading-[19px] font-normal text-gray-800">
            <p>pseudo</p>
            <p>pseudo</p>
            <p>pseudo</p>
        </div>
    </div>
</body>
</html>
