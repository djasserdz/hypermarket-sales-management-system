<?php

namespace App\Policies;

use App\Models\User;
use App\Models\CashRegister;
use Illuminate\Auth\Access\Response;

class ManagerPolicy
{
    /**
     * Determine whether the user can view any cash registers.
     */
    public function viewAny(User $user): bool
    {
        return true; // All authenticated users can view any
    }

    /**
     * Determine whether the user can view a specific cash register.
     */
    public function view(User $user, CashRegister $cashRegister): bool
    {
        return $user->id === $cashRegister->supermarket?->manager_id;
    }

    /**
     * Determine whether the user can create a cash register.
     */
    public function create(User $user): bool
    {
        return true; // Managers can create cash registers (optional)
    }

    /**
     * Determine whether the user can update the cash register.
     */
    public function update(User $user, CashRegister $cashRegister): bool
    {
        return $user->id === $cashRegister->supermarket?->manager_id;
    }

    /**
     * Determine whether the user can delete the cash register.
     */
    public function delete(User $user, CashRegister $cashRegister): bool
    {
        return $user->id === $cashRegister->supermarket?->manager_id;
    }

    /**
     * Determine whether the user can restore the cash register.
     */
    public function restore(User $user, CashRegister $cashRegister): bool
    {
        return $user->id === $cashRegister->supermarket?->manager_id;
    }

    /**
     * Determine whether the user can permanently delete the cash register.
     */
    public function forceDelete(User $user, CashRegister $cashRegister): bool
    {
        return false; // Generally unsafe, keep disabled
    }
}
