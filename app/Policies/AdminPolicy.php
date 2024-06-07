<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class AdminPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Par exemple, vérifier si l'utilisateur est autorisé à voir la liste des administrateurs
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Admin $admin): bool
    {
        // Par exemple, vérifier si l'utilisateur est autorisé à voir un administrateur spécifique
        return $user->isAdmin() || $user->id === $admin->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Par exemple, vérifier si l'utilisateur est autorisé à créer un nouvel administrateur
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Admin $admin): bool
    {
        // Par exemple, vérifier si l'utilisateur est autorisé à mettre à jour un administrateur spécifique
        return $user->isAdmin() || $user->id === $admin->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Admin $admin): bool
    {
        // Par exemple, vérifier si l'utilisateur est autorisé à supprimer un administrateur spécifique
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Admin $admin): bool
    {
        // Si vous avez une logique pour restaurer les administrateurs, vous pouvez l'implémenter ici
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Admin $admin): bool
    {
        // Par exemple, vérifier si l'utilisateur est autorisé à supprimer définitivement un administrateur spécifique
        return $user->isAdmin();
    }
}
