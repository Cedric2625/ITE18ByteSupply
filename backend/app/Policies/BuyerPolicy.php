<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Buyer;

class BuyerPolicy
{
    /**
     * Determine whether the user can view any buyers.
     */
    public function viewAny(mixed $user): bool
    {
        return $user instanceof Admin;
    }

    /**
     * Determine whether the user can view the buyer.
     */
    public function view(mixed $user, Buyer $buyer): bool
    {
        if ($user instanceof Admin) {
            return true;
        }
        if ($user instanceof Buyer) {
            return $user->id === $buyer->id;
        }
        return false;
    }

    /**
     * Only admins can create buyers via API.
     */
    public function create(mixed $user): bool
    {
        return $user instanceof Admin;
    }

    /**
     * Only admins can update buyers.
     */
    public function update(mixed $user, Buyer $buyer): bool
    {
        return $user instanceof Admin;
    }

    /**
     * Only admins can delete buyers.
     */
    public function delete(mixed $user, Buyer $buyer): bool
    {
        return $user instanceof Admin;
    }
}


