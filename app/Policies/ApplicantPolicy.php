<?php

namespace App\Policies;

use App\Models\Applicant;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ApplicantPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        $name = $user->role->name;
        return $name === 'Admin' || $name === 'Staff' || $name === 'Collector'  || $name === 'Customer';
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Applicant $applicant): bool
    {
        $name = $user->role->name;
        return $name === 'Admin' || $name === 'Staff' || $name === 'Collector'  || $name === 'Customer';
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        $name = $user->role->name;
        return $name === 'Admin' || $name === 'Staff' || $name === 'Collector'  || $name === 'Customer';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Applicant $applicant): bool
    {
        $name = $user->role->name;
        return $name === 'Admin' || $name === 'Staff' || $name === 'Collector' || $name === 'Customer';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Applicant $applicant): bool
    {
        $name = $user->role->name;
        return $name === 'Admin' || $name === 'Customer';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Applicant $applicant): bool
    {
        $name = $user->role->name;
        return $name === 'Admin' || $name === 'Customer';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Applicant $applicant): bool
    {
        $name = $user->role->name;
        return $name === 'Admin' || $name === 'Customer';
    }
}
