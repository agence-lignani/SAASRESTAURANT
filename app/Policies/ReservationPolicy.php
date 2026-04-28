<?php

namespace App\Policies;

use App\Models\Reservation;
use App\Models\User;
use App\Support\Filament\FilamentAccess;

class ReservationPolicy
{
    public function viewAny(User $user): bool
    {
        return FilamentAccess::canViewReservations();
    }

    public function view(User $user, Reservation $reservation): bool
    {
        return FilamentAccess::canViewReservations();
    }

    public function create(User $user): bool
    {
        return FilamentAccess::canManageBookings();
    }

    public function update(User $user, Reservation $reservation): bool
    {
        return FilamentAccess::canManageBookings();
    }

    public function delete(User $user, Reservation $reservation): bool
    {
        return FilamentAccess::canManageBookings();
    }

    /**
     * Profil serveur : reporter l’horaire (statut « retard »).
     */
    public function delayReservation(User $user, Reservation $reservation): bool
    {
        if (! FilamentAccess::isServer()) {
            return false;
        }

        return in_array($reservation->status, [
            Reservation::STATUS_PENDING,
            Reservation::STATUS_CONFIRMED,
            Reservation::STATUS_DELAYED,
        ], true);
    }

    /**
     * Profil serveur : annuler la réservation (même effet métier qu’en salle).
     */
    public function cancelReservation(User $user, Reservation $reservation): bool
    {
        if (! FilamentAccess::isServer()) {
            return false;
        }

        return in_array($reservation->status, [
            Reservation::STATUS_PENDING,
            Reservation::STATUS_CONFIRMED,
            Reservation::STATUS_DELAYED,
        ], true);
    }

    /**
     * Profil serveur : confirmer la présence du client (table honorée).
     */
    public function confirmPresence(User $user, Reservation $reservation): bool
    {
        if (! FilamentAccess::isServer()) {
            return false;
        }

        return in_array($reservation->status, [
            Reservation::STATUS_PENDING,
            Reservation::STATUS_CONFIRMED,
            Reservation::STATUS_DELAYED,
        ], true);
    }
}
