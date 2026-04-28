<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Site non publié</title>
    <style>
        body { font-family: system-ui, sans-serif; max-width: 36rem; margin: 3rem auto; padding: 0 1rem; line-height: 1.5; color: #1a1a1a; }
        a { color: #8B4513; }
    </style>
</head>
<body>
    <h1>En ligne prochainement</h1>
    @if($restaurant)
        <p><strong>{{ $restaurant->name }}</strong></p>
    @endif
    <p>Ce site n’est pas encore public. L’établissement doit activer la publication depuis l’administration.</p>
    <p><a href="{{ url('/admin') }}">Connexion administration</a></p>
</body>
</html>
