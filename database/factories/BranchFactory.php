<?php

namespace Database\Factories;

use App\Models\Branch;
use Illuminate\Database\Eloquent\Factories\Factory;

class BranchFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => 'Sta. Rita Branch',
            'unit' => '045',
            'barangay' => 'Balucuc',
            'city' => 'Pulilan',
            'province' => 'Bulacan',
        ];
    }
    public function Pulilan(): self
    {
        return $this->state([
            'name' => 'Pulilan Branch',
            'unit' => '045',
            'barangay' => 'Balucuc',
            'city' => 'Pulilan',
            'province' => 'Bulacan',
        ]);
    }
    public function Bocue(): self
    {
        return $this->state([
            'name' => 'Bocue Branch',
            'unit' => '0035 Centro',
            'barangay' => 'Balucuc',
            'city' => 'Apalit',
            'province' => 'Pampanga',
        ]);
    }
    public function Sanildefonso(): self
    {
        return $this->state([
            'name' => 'San Ildefonso Branch',
            'unit' => '045',
            'barangay' => 'Balucuc',
            'city' => 'Pulilan',
            'province' => 'Bulacan',
        ]);
    }
    public function Balagtas(): self
    {
        return $this->state([
            'name' => 'Balagtas Branch',
            'unit' => '045',
            'barangay' => 'Balucuc',
            'city' => 'Pulilan',
            'province' => 'Bulacan',
        ]);
    }
    public function Calumpit(): self
    {
        return $this->state([
            'name' => 'Calumpit Branch',
            'unit' => '045',
            'barangay' => 'Balucuc',
            'city' => 'Pulilan',
            'province' => 'Bulacan',
        ]);
    }
    public function Bulacan(): self
    {
        return $this->state([
            'name' => 'Bulacan, Bulacan Branch',
            'unit' => '045',
            'barangay' => 'Balucuc',
            'city' => 'Pulilan',
            'province' => 'Bulacan',
        ]);
    }
    public function Jose(): self
    {
        return $this->state([
            'name' => 'San Jose Branch',
            'unit' => '045',
            'barangay' => 'Balucuc',
            'city' => 'Pulilan',
            'province' => 'Bulacan',
        ]);
    }
}
