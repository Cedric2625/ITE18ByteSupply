<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Class SelectedComponent
 * 
 * @package App\Models
 * 
 * @property int $order_id
 * @property int $component_id
 * @property int $quantity
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \App\Models\Order $order
 * @property-read \App\Models\HardwareComponent $hardwareComponent
 */
class SelectedComponent extends Model
{
    use HasFactory;

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The primary key for the model.
     *
     * @var array
     */
    protected $primaryKey = ['order_id', 'component_id'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'order_id',
        'component_id',
        'quantity',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the order that owns the selected component.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the hardware component that owns the selected component.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function hardwareComponent(): BelongsTo
    {
        return $this->belongsTo(HardwareComponent::class, 'component_id');
    }
}
