<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Invitation — {{ $restaurant->name }}</title>
    @vite(['resources/css/bistro.css'])
</head>
<body class="min-h-screen bg-stone-100 px-4 py-12 antialiased">
    <div class="mx-auto max-w-md rounded-2xl bg-white p-8 shadow">
        <h1 class="text-xl font-semibold text-stone-900">Créer votre accès</h1>
        <p class="mt-2 text-sm text-stone-600">
            Établissement : <strong>{{ $restaurant->name }}</strong> — profil
            @switch($invitation->role)
                @case('owner') gérant @break
                @case('reservation') gestionnaire réservations @break
                @case('editor') rédacteur @break
                @case('server') serveur (consultation réservations) @break
                @default {{ $invitation->role }}
            @endswitch
        </p>
        <form method="post" action="{{ route('invitation.store', ['token' => $invitation->token]) }}" class="mt-6 space-y-4">
            @csrf
            <div>
                <label class="block text-sm font-medium text-stone-700">E-mail</label>
                <input type="email" value="{{ $invitation->email }}" disabled class="mt-1 w-full rounded-lg border border-stone-200 bg-stone-50 px-3 py-2 text-sm text-stone-500">
            </div>
            <div>
                <label for="name" class="block text-sm font-medium text-stone-700">Nom affiché</label>
                <input name="name" id="name" required value="{{ old('name') }}" class="mt-1 w-full rounded-lg border border-stone-200 px-3 py-2 text-sm">
                @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="family_name" class="block text-sm font-medium text-stone-700">Nom de famille (connexion)</label>
                <input name="family_name" id="family_name" required value="{{ old('family_name') }}" autocomplete="family-name" class="mt-1 w-full rounded-lg border border-stone-200 px-3 py-2 text-sm" maxlength="120">
                <p class="mt-1 text-xs text-stone-500">Saisi tel quel à l’écran de connexion (insensible à la casse).</p>
                @error('family_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="code" class="block text-sm font-medium text-stone-700">Mot de passe (6 chiffres)</label>
                <input type="password" name="code" id="code" required inputmode="numeric" pattern="[0-9]*" maxlength="6" autocomplete="new-password" class="mt-1 w-full rounded-lg border border-stone-200 px-3 py-2 text-sm">
                <p class="mt-1 text-xs text-stone-500">Ce code et le nom de famille ci-dessus servent à vous connecter sur la page d’administration.</p>
                @error('code')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
            <div>
                <label for="code_confirmation" class="block text-sm font-medium text-stone-700">Confirmer le code</label>
                <input type="password" name="code_confirmation" id="code_confirmation" required inputmode="numeric" pattern="[0-9]*" maxlength="6" class="mt-1 w-full rounded-lg border border-stone-200 px-3 py-2 text-sm">
            </div>
            <button type="submit" class="w-full rounded-lg bg-amber-600 px-4 py-2 text-sm font-medium text-white hover:bg-amber-700">Valider</button>
        </form>
        <p class="mt-4 text-center text-xs text-stone-500">
            Invitation valide jusqu’au {{ $invitation->expires_at->translatedFormat('d/m/Y H:i') }}.
        </p>
    </div>
</body>
</html>
