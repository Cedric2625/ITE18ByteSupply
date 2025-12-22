<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Str;
use Faker\Factory as FakerFactory;

/**
 * Class Buyer
 * 
 * @package App\Models
 * 
 * @property int $id
 * @property string $buyer_name
 * @property string $buyer_number
 * @property string $email
 * @property string $password_hash
 * @property string $address
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property \Carbon\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Order[] $orders
 */
class Buyer extends Authenticatable
{
    use HasApiTokens, HasFactory, SoftDeletes, Notifiable;

		protected static function booted(): void
		{
			static::creating(function (Buyer $buyer): void {
				if (empty($buyer->buyer_number)) {
					$buyer->buyer_number = static::generateUniqueBuyerNumber();
				}

				if (empty($buyer->address)) {
					$faker = FakerFactory::create();
					$buyer->address = $faker->address();
				}
			});
		}

		private static function generateUniqueBuyerNumber(): string
		{
			$faker = FakerFactory::create();

			// Generate a numeric-only string with exactly 9 digits.
			for ($i = 0; $i < 5; $i++) {
				$candidate = $faker->numerify('#########'); // 9 digits

				if (!static::where('buyer_number', $candidate)->exists()) {
					return $candidate;
				}
			}

			// Fallback: guaranteed 9-digit number
			return (string) random_int(100000000, 999999999);
		}

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'buyer_name',
        'buyer_number',
        'email',
        'password_hash',
        'address',
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
     * Attributes that should be hidden for arrays/JSON.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password_hash',
    ];

    /**
     * Get the orders associated with the buyer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Return the password value for authentication.
     */
    public function getAuthPassword(): string
    {
        return (string) $this->password_hash;
    }
}
