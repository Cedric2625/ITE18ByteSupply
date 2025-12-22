<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Supplier
 * 
 * @package App\Models
 * 
 * @property int $id
 * @property string $supplier_name
 * @property string $contact_person
 * @property string $phone_number
 * @property string $email
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\HardwareComponent[] $hardwareComponents
 */
class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'supplier_name',
        'contact_person',
        'phone_number',
        'email',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Get the hardware components for the supplier.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function hardwareComponents(): HasMany
    {
        return $this->hasMany(HardwareComponent::class);
    }
}
