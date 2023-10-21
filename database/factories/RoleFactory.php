<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Role>
 */
class RoleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Admin',
            'description' => 'Admin User',
        ];
    }

    /**
     * Define a state for Staff Role.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function staff(): self
    {
        return $this->state([
            'name' => 'Staff',
            'description' => 'Staff User',
        ]);
    }
    public function collector(): self
    {
        return $this->state([
            'name' => 'Collector',
            'description' => 'Collector User',
        ]);
    }
    public function customer(): self
    {
        return $this->state([
            'name' => 'Customer',
            'description' => 'Customer User',
        ]);
    }
}
