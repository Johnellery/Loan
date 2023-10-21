<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Rate>
 */
class RateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'low' => '7800',
            'rate' => '10',
            'high' => '7801',
            'rate1' => '20',
            'status' => 'deactived',
        ];
    }
    public function rate1(): self
    {
        return $this->state([
            'low' => '7000',
            'rate' => '10',
            'high' => '7001',
            'rate1' => '20',
            'status' => 'active',
        ]);
    }
    public function rate2(): self
    {
        return $this->state([
            'low' => '5000',
            'rate' => '10',
            'high' => '5001',
            'rate1' => '20',
            'status' => 'deactivated',
        ]);
    }
    public function rate3(): self
    {
        return $this->state([
            'low' => '7000',
            'rate' => '13',
            'high' => '7001',
            'rate1' => '23',
            'status' => 'deactivated',
        ]);
    }
    public function rate4(): self
    {
        return $this->state([
            'low' => '7500',
            'rate' => '13',
            'high' => '7501',
            'rate1' => '25',
            'status' => 'deactivated',
        ]);
    }
    public function rate5(): self
    {
        return $this->state([
            'low' => '7000',
            'rate' => '18',
            'high' => '7001',
            'rate1' => '30',
            'status' => 'deactivated',
        ]);
    }
    public function rate6(): self
    {
        return $this->state([
            'low' => '6000',
            'rate' => '11',
            'high' => '6001',
            'rate1' => '17',
            'status' => 'deactivated',
        ]);
    }
    public function rate7(): self
    {
        return $this->state([
            'low' => '7000',
            'rate' => '8',
            'high' => '7001',
            'rate1' => '15',
            'status' => 'deactivated',
        ]);
    }
    public function rate8(): self
    {
        return $this->state([
            'low' => '10000',
            'rate' => '10',
            'high' => '10001',
            'rate1' => '20',
            'status' => 'deactivated',
        ]);
    }
    public function rate9(): self
    {
        return $this->state([
            'low' => '8000',
            'rate' => '10',
            'high' => '8001',
            'rate1' => '20',
            'status' => 'deactivated',
        ]);
    }
    public function rate10(): self
    {
        return $this->state([
            'low' => '7000',
            'rate' => '15',
            'high' => '7001',
            'rate1' => '30',
            'status' => 'deactivated',
        ]);
    }
    public function rate11(): self
    {
        return $this->state([
            'low' => '7200',
            'rate' => '7',
            'high' => '7201',
            'rate1' => '17',
            'status' => 'deactivated',
        ]);
    }

}
