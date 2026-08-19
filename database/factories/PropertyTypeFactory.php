<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Properties\Models\PropertyType;

class PropertyTypeFactory extends Factory
{
    protected $model = PropertyType::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->word(),
            'key' => $this->faker->unique()->slug(2, '_'),
            'slug' => $this->faker->slug(2),
            'description' => $this->faker->optional()->sentence(),
            'icon' => 'bi bi-building',
            'is_active' => true,
            'is_public' => true,
            'sort_order' => $this->faker->numberBetween(1, 100),
            'settings' => null,
        ];
    }

    public function house(): static
    {
        return $this->state(fn (array $attributes) => [
            'key' => 'house',
            'name' => 'Casa',
            'slug' => 'casas',
            'icon' => 'bi bi-house',
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
