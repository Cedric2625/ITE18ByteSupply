<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Ensure a known admin account exists
        DB::table('admins')->updateOrInsert(
            ['username' => 'admin'],
            [
                'password_hash' => bcrypt('password'),
                'role' => 'system_admin',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

		/** ---------------- ADMINS ---------------- */
		$admins = [];
		for ($i = 0; $i < 9; $i++) {
            $admins[] = [
                'username' => fake()->unique()->userName(),
                'password_hash' => bcrypt('password'),
                'role' => fake()->randomElement(['system_admin', 'inventory_admin', 'order_admin']),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('admins')->insert($admins);
        $adminIds = DB::table('admins')->pluck('id')->all();

        /** ---------------- BUYERS ---------------- */
        // Ensure a known buyer account exists
        DB::table('buyers')->updateOrInsert(
            ['email' => 'customer@example.com'],
            [
                'buyer_name'   => 'Sample Customer',
                'password_hash'=> bcrypt('password'),
                'buyer_number' => fake()->unique()->numerify('20#######'),
                'address'      => fake()->address(),
                'created_at'   => now(),
                'updated_at'   => now(),
            ]
        );

        $buyers = [];
        for ($i = 0; $i < 50; $i++) {
            $buyers[] = [
                'buyer_name'   => fake()->name(),
                'buyer_number' => fake()->unique()->numerify('20#######'),
                'email'        => fake()->unique()->safeEmail(),
                'password_hash'=> bcrypt('password'),
                'address'      => fake()->address(),
                'created_at'   => now(),
                'updated_at'   => now(),
            ];
        }
        DB::table('buyers')->insert($buyers);
        $buyerIds = DB::table('buyers')->pluck('id')->all();

        /** ---------------- CATEGORIES ---------------- */
        $categories = [];
        for ($i = 0; $i < 100; $i++) {
            $categories[] = [
                'category_name' => ucfirst(fake()->unique()->word()),
                'created_at'    => now(),
                'updated_at'    => now(),
            ];
        }
        DB::table('categories')->insert($categories);

        /** ---------------- SUPPLIERS ---------------- */
        $suppliers = [];
        for ($i = 0; $i < 100; $i++) {
            $suppliers[] = [
                'supplier_name'  => fake()->company(),
                'contact_person' => fake()->name(),
                'phone_number'   => fake()->phoneNumber(),
                'email'          => fake()->unique()->companyEmail(),
                'created_at'     => now(),
                'updated_at'     => now(),
            ];
        }
        DB::table('suppliers')->insert($suppliers);

        /** ---------------- HARDWARE COMPONENTS ---------------- */
        $components = [];
        for ($i = 0; $i < 100; $i++) {
            $components[] = [
                'component_reference_number' => strtoupper(fake()->bothify('REF-###??')),
                'brand'                      => fake()->company(),
                'model'                      => strtoupper(fake()->bothify('MDL-###??')),
                'specifications'             => fake()->sentence(8),
                'retail_price'               => fake()->randomFloat(2, 1000, 20000),
                'seller_price'               => fake()->randomFloat(2, 500, 15000),
                'component_name'             => fake()->word() . ' ' . fake()->randomElement(['Keyboard','Mouse','CPU','GPU','Router']),
                'stock_quantity'             => 100,
                'date_created'               => fake()->date(),
                'date_order'                 => fake()->date(),
                'date_arrive'                => fake()->date(),
                'category_id'                => DB::table('categories')->inRandomOrder()->value('id') ?? 1,
                'supplier_id'                => DB::table('suppliers')->inRandomOrder()->value('id') ?? 1,
                'created_at'                 => now(),
                'updated_at'                 => now(),
            ];
        }
        DB::table('hardware_components')->insert($components);
        $componentIds = DB::table('hardware_components')->pluck('id')->all();

        //** ---------------- ORDERS ---------------- */
        $orderIds = [];
        for ($i = 0; $i < 100; $i++) {
            $orderDate = fake()->dateTimeBetween('-1 year');
            $id = DB::table('orders')->insertGetId([
                'order_date'         => $orderDate->format('Y-m-d H:i:s'),
                'total_amount'       => fake()->randomFloat(2, 500, 50000),
                'buyer_id'           => Arr::random($buyerIds),
                'admin_id'           => Arr::random($adminIds),
                'estimated_delivery' => $orderDate->modify('+7 days')->format('Y-m-d H:i:s'),
                'tracking_number'    => fake()->bothify('TRK#####'),
                'shipping_status'    => fake()->randomElement(['pending','processing','shipped','delivered','cancelled']),
                'payment_method'     => fake()->randomElement(['cash','credit_card','bank_transfer','online_payment']),
                'order_reference_number' => strtoupper(fake()->bothify('ORD-########')),
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);
            $orderIds[] = $id;
        }

        /** ---------------- SELECTED COMPONENTS ---------------- */
        $selectedComponents = [];
        $usedPairs = []; // track order_id + component_id combos

        for ($i = 0; $i < 100; $i++) {
            do {
                $orderId = Arr::random($orderIds);
                $componentId = Arr::random($componentIds);
                $pair = $orderId . '-' . $componentId;
            } while (in_array($pair, $usedPairs)); // keep retrying if duplicate

            $usedPairs[] = $pair;

            $selectedComponents[] = [
                'order_id'     => $orderId,
                'component_id' => $componentId,
                'quantity'     => fake()->numberBetween(1, 5),
            ];
        }

        DB::table('selected_components')->insert($selectedComponents);

        /** ---------------- REVIEWS ---------------- */
        $reviews = [];
        for ($i = 0; $i < 200; $i++) {
            $reviews[] = [
                'buyer_id'     => Arr::random($buyerIds),
                'component_id' => Arr::random($componentIds),
                'rating'       => fake()->numberBetween(1, 5),
                'comment'      => fake()->boolean(70) ? fake()->sentence(10) : null,
                'created_at'   => now(),
                'updated_at'   => now(),
            ];
        }
        DB::table('reviews')->insert($reviews);
    }
}