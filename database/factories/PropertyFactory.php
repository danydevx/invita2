<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Listings\Models\Listing;
use Modules\Properties\Models\Property;
use Modules\Properties\Models\PropertyType;

class PropertyFactory extends Factory
{
    protected $model = Property::class;

    public function definition(): array
    {
        $operationTypes = ['sale', 'rent', 'transfer'];
        $currencies = ['MXN', 'USD'];
        $periods = ['single', 'monthly', 'weekly', 'daily'];
        $statuses = ['draft', 'published', 'paused', 'rented', 'sold', 'transferred', 'archived'];

        $operation = $this->faker->randomElement($operationTypes);
        $price = $this->faker->numberBetween(100000, 5000000);

        return [
            'listing_id' => Listing::factory(),
            'property_type_id' => PropertyType::factory(),
            'title' => $this->faker->sentence(4),
            'slug' => $this->faker->unique()->slug(3),
            'description' => $this->faker->paragraphs(3, true),
            'operation_type' => $operation,
            'price' => $price,
            'currency' => $this->faker->randomElement($currencies),
            'price_period' => $operation === 'rent' ? $this->faker->randomElement(['monthly', 'weekly', 'daily']) : 'single',
            'main_image' => null,
            'status' => $this->faker->randomElement($statuses),
            'is_featured' => $this->faker->boolean(20),
            'is_public' => $this->faker->boolean(70),
            'published_at' => $this->faker->optional()->dateTimeBetween('-1 year', 'now'),
        ];
    }

    public function house(): static
    {
        return $this->state(fn (array $attributes) => [
            'property_type_id' => fn () => PropertyType::where('key', 'house')->firstOrCreate([
                'key' => 'house',
                'name' => 'Casa',
                'slug' => 'casas',
            ])->id,
        ]);
    }

    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
            'is_public' => true,
            'published_at' => now(),
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }

    public function forSale(): static
    {
        return $this->state(fn (array $attributes) => [
            'operation_type' => 'sale',
            'price_period' => 'single',
        ]);
    }

    public function forRent(): static
    {
        return $this->state(fn (array $attributes) => [
            'operation_type' => 'rent',
            'price_period' => 'monthly',
        ]);
    }
}
