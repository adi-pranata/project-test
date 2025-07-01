<?php

namespace App\Policies;

use App\Models\ComplaintDocument;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class ComplaintDocumentPolicy
{
     use HandlesAuthorization;
    public function download(User $user, ComplaintDocument $document)
    {
        return $user->id === $document->complaint->user_id || $user->isAdmin();
    }
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ComplaintDocument $complaintDocument): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ComplaintDocument $complaintDocument): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ComplaintDocument $complaintDocument): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, ComplaintDocument $complaintDocument): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, ComplaintDocument $complaintDocument): bool
    {
        return false;
    }
}
