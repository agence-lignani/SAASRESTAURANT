<?php

namespace App\Support\Filament;

/**
 * Profil PRONOTE choisi à la connexion (session) — utilisé par les policies Filament (J5 / F1).
 */
final class FilamentAccess
{
    public static function role(): string
    {
        return (string) session('filament_profile_role', 'owner');
    }

    public static function isOwner(): bool
    {
        return self::role() === 'owner';
    }

    public static function isReservation(): bool
    {
        return self::role() === 'reservation';
    }

    public static function isEditor(): bool
    {
        return self::role() === 'editor';
    }

    public static function isServer(): bool
    {
        return self::role() === 'server';
    }

    /**
     * Consultation des réservations (liste, fiche, calendrier) — inclut le profil serveur.
     */
    public static function canViewReservations(): bool
    {
        return self::isOwner() || self::isReservation() || self::isServer();
    }

    /**
     * Création / modification / suppression réservations et paramètres (services, créneaux, etc.).
     */
    public static function canManageBookings(): bool
    {
        return self::isOwner() || self::isReservation();
    }

    public static function canEditSiteAndMenu(): bool
    {
        return self::isOwner() || self::isEditor();
    }

    public static function canInviteTeam(): bool
    {
        return self::isOwner();
    }
}
