<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Groupe;
use Illuminate\Auth\Access\HandlesAuthorization;

class GroupePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('view_any_groupe');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Groupe $groupe): bool
    {
        return $user->can('view_groupe');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('create_groupe');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Groupe $groupe): bool
    {
        return $user->can('update_groupe');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Groupe $groupe): bool
    {
        return $user->can('delete_groupe');
    }

    /**
     * Determine whether the user can bulk delete.
     */
    public function deleteAny(User $user): bool
    {
        return $user->can('delete_any_groupe');
    }

    /**
     * Determine whether the user can permanently delete.
     */
    public function forceDelete(User $user, Groupe $groupe): bool
    {
        return $user->can('force_delete_groupe');
    }

    /**
     * Determine whether the user can permanently bulk delete.
     */
    public function forceDeleteAny(User $user): bool
    {
        return $user->can('force_delete_any_groupe');
    }

    /**
     * Determine whether the user can restore.
     */
    public function restore(User $user, Groupe $groupe): bool
    {
        return $user->can('restore_groupe');
    }

    /**
     * Determine whether the user can bulk restore.
     */
    public function restoreAny(User $user): bool
    {
        return $user->can('restore_any_groupe');
    }

    /**
     * Determine whether the user can replicate.
     */
    public function replicate(User $user, Groupe $groupe): bool
    {
        return $user->can('replicate_groupe');
    }

    /**
     * Determine whether the user can reorder.
     */
    public function reorder(User $user): bool
    {
        return $user->can('reorder_groupe');
    }
}
