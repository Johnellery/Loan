<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Mountain bike',
        ];
    }


    public function roadBike(): self
    {
        return $this->state([
            'name' => 'Road bike',
        ]);
    }

    public function foldingBike(): self
    {
        return $this->state([
            'name' => 'Folding bike',
        ]);
    }

    public function touringBike(): self
    {
        return $this->state([
            'name' => 'Touring bike',
        ]);
    }

    public function bmx(): self
    {
        return $this->state([
            'name' => 'BMX',
        ]);
    }

    public function cruiser(): self
    {
        return $this->state([
            'name' => 'Cruiser',
        ]);
    }

    public function hybridBike(): self
    {
        return $this->state([
            'name' => 'Hybrid bike',
        ]);
    }

    public function cyclocrossBike(): self
    {
        return $this->state([
            'name' => 'Cyclocross bike',
        ]);
    }
}
