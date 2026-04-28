<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserInvitation;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AcceptInvitationController extends Controller
{
    public function show(string $token): View|RedirectResponse
    {
        $invitation = UserInvitation::query()->where('token', $token)->firstOrFail();

        if (! $invitation->isPending()) {
            return redirect()->to(url('/admin/login'))
                ->with('error', 'Cette invitation n’est plus valide.');
        }

        return view('auth.accept-invitation', [
            'invitation' => $invitation,
            'restaurant' => $invitation->restaurant,
        ]);
    }

    public function store(Request $request, string $token): RedirectResponse
    {
        $invitation = UserInvitation::query()->where('token', $token)->firstOrFail();

        if (! $invitation->isPending()) {
            return redirect()->to(url('/admin/login'))
                ->with('error', 'Cette invitation n’est plus valide.');
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'family_name' => ['required', 'string', 'max:120'],
            'code' => ['required', 'string', 'regex:/^\d{6}$/', 'confirmed'],
        ]);

        if (User::query()->where('email', $invitation->email)->exists()) {
            return back()->withErrors([
                'email' => 'Un compte existe déjà avec cette adresse e-mail.',
            ]);
        }

        $user = User::query()->create([
            'name' => $validated['name'],
            'family_name' => $validated['family_name'],
            'email' => $invitation->email,
            'password' => $validated['code'],
            'email_verified_at' => now(),
        ]);

        $user->restaurants()->attach($invitation->restaurant_id, ['role' => $invitation->role]);

        $invitation->update(['accepted_at' => now()]);

        return redirect()
            ->to(url('/admin/login'))
            ->with('status', 'Votre compte est prêt. Connectez-vous avec le nom de famille saisi ci-dessus et le code à 6 chiffres défini ci-dessus. Votre profil sera : '.self::roleLabel($invitation->role).'.');
    }

    private static function roleLabel(string $role): string
    {
        return match ($role) {
            'owner' => 'Gérant',
            'reservation' => 'Gestionnaire salle',
            'editor' => 'Rédacteur',
            'server' => 'Serveur',
            default => $role,
        };
    }
}
