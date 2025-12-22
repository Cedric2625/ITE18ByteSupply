<?php

namespace App\Policies;

use App\Models\Admin;
use App\Models\Buyer;
use App\Models\Order;

class OrderPolicy
{
    /**
     * Admins can view any orders; buyers can view their own.
     */
    public function view(mixed $user, Order $order): bool
    {
        if ($user instanceof Admin) {
            return true;
        }
        if ($user instanceof Buyer) {
            return $order->buyer_id === $user->id;
        }
        return false;
    }

    /**
     * Admins manage orders; buyers cannot create/update/delete here.
     */
    public function create(mixed $user): bool
    {
        return $user instanceof Admin;
    }

    public function update(mixed $user, Order $order): bool
    {
        return $user instanceof Admin;
    }

    public function delete(mixed $user, Order $order): bool
    {
        return $user instanceof Admin;
    }
}


