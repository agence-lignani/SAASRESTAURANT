<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Site non publié</title>
    <style>
        body { min-height: 100vh; margin: 0; display: grid; place-items: center; background: #f8f4ef; color: #2a241f; font-family: system-ui, sans-serif; line-height: 1.65; }
        main { width: min(36rem, calc(100% - 2rem)); border: 1px solid #e7dbcf; border-radius: 1.5rem; background: #fdfaf6; padding: clamp(2rem, 6vw, 4rem); box-shadow: 0 18px 50px rgb(67 45 28 / 0.08); }
        h1 { margin: 0; font-family: Georgia, serif; font-size: clamp(2.3rem, 7vw, 4rem); line-height: .96; letter-spacing: -.04em; }
        p { margin: 1rem 0 0; color: #5d5045; }
        a { color: #8B4513; font-weight: 700; text-underline-offset: .25rem; }
    </style>
</head>
<body>
    <main>
        <h1>En ligne prochainement</h1>
        @if($restaurant)
            <p><strong>{{ $restaurant->name }}</strong></p>
        @endif
        <p>Ce site n’est pas encore public. L’établissement doit activer la publication depuis l’administration.</p>
        <p><a href="{{ url('/admin') }}">Connexion administration</a></p>
    </main>
</body>
</html>
