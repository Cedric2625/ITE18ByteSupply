<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Order
 * 
 * @package App\Models
 * 
 * @property int $id
 * @property \Carbon\Carbon $order_date
 * @property float $total_amount
 * @property string $shipping_status
 * @property string|null $tracking_number
 * @property \Carbon\Carbon|null $estimated_delivery
 * @property string $order_reference_number
 * @property int $buyer_id
 * @property int $admin_id
 * @property string $payment_method
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property-read \App\Models\Buyer $buyer
 * @property-read \App\Models\Admin $admin
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\SelectedComponent[] $selectedComponents
 */
class Order extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'order_date',
        'total_amount',
        'shipping_status',
        'tracking_number',
        'estimated_delivery',
        'order_reference_number',
        'buyer_id',
        'admin_id',
        'payment_method',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'order_date' => 'datetime',
        'total_amount' => 'decimal:2',
        'estimated_delivery' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the buyer that owns the order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function buyer(): BelongsTo
    {
        return $this->belongsTo(Buyer::class);
    }

    /**
     * Get the admin that processed the order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    /**
     * Get the selected components for the order.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function selectedComponents(): HasMany
    {
        return $this->hasMany(SelectedComponent::class);
    }
}
