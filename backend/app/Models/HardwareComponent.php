<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $component_reference_number
 * @property string $component_name
 * @property string $brand
 * @property string $model
 * @property string $specifications
 * @property float $retail_price
 * @property float $seller_price
 * @property int $stock_quantity
 * @property int $category_id
 * @property int $supplier_id
 * @property \Carbon\Carbon|null $date_created
 * @property \Carbon\Carbon|null $date_order
 * @property \Carbon\Carbon|null $date_arrive
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property-read \App\Models\Category $category
 * @property-read \App\Models\Supplier $supplier
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\SelectedComponent[] $selectedComponents
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Review[] $reviews
 */
class HardwareComponent extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'component_reference_number',
        'component_name',
        'brand',
        'model',
        'specifications',
        'retail_price',
        'seller_price',
        'stock_quantity',
        'category_id',
        'supplier_id',
        'date_created',
        'date_order',
        'date_arrive',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'retail_price' => 'decimal:2',
        'seller_price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'date_created' => 'datetime',
        'date_order' => 'datetime',
        'date_arrive' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the category that owns the hardware component.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the supplier that owns the hardware component.
     */
    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the selected components for the hardware component.
     */
    public function selectedComponents(): HasMany
    {
        return $this->hasMany(SelectedComponent::class, 'component_id');
    }

    /**
     * Reviews for this component.
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'component_id');
    }
}