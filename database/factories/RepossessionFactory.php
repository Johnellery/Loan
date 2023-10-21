<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Applicant>
 */
class RepossessionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => '10',
            'branch_id' => '2',
            'picture' => 'picture',
            'valid_id_list' => 'driver_license',
            'valid_id' => 'valid_id.jpg',
            'barangay_clearance' => 'barangay.png',
            'first' => $this->faker->name(),
            'middle' =>  $this->faker->lastname,
            'last' => $this->faker->lastname,
            'age' =>  $this->faker->randomElement(['23', '25', '34', '43','52', '61', '73', '32', '37', '29', '30', '26', '22', '55']),
            'gender' => $this->faker->randomElement(['Male', 'Female']),
            'civil' => 'single',
            'religion' => 'Catholic',
            'occupation' => 'Manager',
            'contact_applicant' => '09466837683',
            'spouse' => '',
            'contact_spouse' => '',
            'occupation_spouse' => '',
            'unit' => '0035',
            'barangay' => 'Inaon',
            'city' => 'Pulilan',
            'province' => 'Bulacan',
            'bike_id' => '3',
            'term' => '4',
            'installment' =>  '4',
            'start' => 'August 17, 2023',
            'end' => 'November 17, 2023',
            'status' => 'approved',
            'ci_status' => 'approved',
            'bike_price' => '19500',
            'down_payment' => '2000',
            'total_interest' => '3900',
            'payment' => '1070',
            'plus'  => '23400',
            'remaining_balance' => '0',
        ];
    }
    public function Applicantrep(): self
    {
        return $this->state([
            'user_id' => '10',
            'branch_id' => '2',
            'picture' => 'picture',
            'valid_id_list' => 'driver_license',
            'valid_id' => 'valid_id.jpg',
            'barangay_clearance' => 'barangay.png',
            'first' => $this->faker->name(),
            'middle' =>  $this->faker->lastname,
            'last' => $this->faker->lastname,
            'age' =>  $this->faker->randomElement(['23', '25', '34', '43','52', '61', '73', '32', '37', '29', '30', '26', '22', '55']),
            'gender' => $this->faker->randomElement(['Male', 'Female']),
            'civil' => 'single',
            'religion' => 'Catholic',
            'occupation' => 'Manager',
            'contact_applicant' => '09466837683',
            'spouse' => '',
            'contact_spouse' => '',
            'occupation_spouse' => '',
            'unit' => '0035',
            'barangay' => 'Inaon',
            'city' => 'Pulilan',
            'province' => 'Bulacan',
            'bike_id' => '3',
            'term' => '4',
            'installment' =>  '4',
            'start' => 'August 17, 2023',
            'end' => 'November 17, 2023',
            'status' => 'approved',
            'ci_status' => 'approved',
            'bike_price' => '19500',
            'down_payment' => '2000',
            'total_interest' => '3900',
            'payment' => '1070',
            'plus'  => '23400',
            'remaining_balance' => '0',
        ]);
    }
}
