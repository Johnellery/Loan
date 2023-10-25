<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Applicant;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Applicant>
 */
class ApplicantFactory extends Factory
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
            'bike_id' => $this->faker->randomElement(['1', '2', '3', '4','5', '6', '7', '8', '9', '10', '11', '12', '13', '14']),
            'term' => '4',
            'installment' =>  $this->faker->randomElement(['1','4']),
            'start' => 'August 30, 2023',
            'end' => 'November 30, 2023',
            'status' => 'pending',
            'ci_status' => 'approved',
            'created_at' => '2023-08-19 13:06:48',
        ];
    }
    public function Applicant(): self
    {
        return $this->state([
            'user_id' => '11',
            'branch_id' => '2',
            'picture' => 'picture',
            'valid_id_list' => 'driver_license',
            'valid_id' => 'valid_id.jpg',
            'barangay_clearance' => 'barangay.png',
            'first' => 'Kimmy',
            'middle' => 'Cruz',
            'last' => 'De Guzman',
            'age' => '21',
            'gender' => 'Female',
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
            'bike_id' => '16',
            'term' => '4',
            'installment' => '4',
            'down_payment' => '2500',
            'start' => 'August 19, 2023',
            'end' => 'November 19, 2023',
            'status' => 'pending',
            'ci_status' => 'approved',
            'created_at' => '2023-08-14 13:06:48',
            // 'total_interest' => '1820',
            // 'plus' => '10920',
            // 'payment' => '546',
            // 'remaining_balance' => '8420',
            // 'bike_price' => '9100',
        ]);
    }
    public function Applicant2(): self
    {
        return $this->state([
            'user_id' => '12',
            'branch_id' => '2',
            'picture' => 'picture',
            'valid_id_list' => 'driver_license',
            'valid_id' => 'valid_id.jpg',
            'barangay_clearance' => 'barangay.png',
            'first' => 'Rea',
            'middle' => 'Cruz',
            'last' => 'Benito',
            'age' => '21',
            'gender' => 'Female',
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
            'term' => '5',
            'installment' => '4',
            'start' => 'August 14, 2023',
            'end' => 'December 14, 2023',
            'status' => 'pending',
            'ci_status' => 'approved',
        ]);
    }
    public function Applicant3(): self
    {
        return $this->state([
            'user_id' => '13',
            'branch_id' => '2',
            'picture' => 'picture',
            'valid_id_list' => 'driver_license',
            'valid_id' => 'valid_id.jpg',
            'barangay_clearance' => 'barangay.png',
            'first' => 'Eric',
            'middle' => 'Mutuc',
            'last' => 'Zaragosa',
            'age' => '21',
            'gender' => 'Female',
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
            'bike_id' => '5',
            'term' => '4',
            'installment' => '1',
            'start' => 'August 4, 2023',
            'end' => 'November 4, 2023',
            'status' => 'pending',
            'ci_status' => 'approved',
        ]);
    }
    public function Applicant4(): self
    {
        return $this->state([
            'user_id' => '14',
            'branch_id' => '2',
            'picture' => 'picture',
            'valid_id_list' => 'driver_license',
            'valid_id' => 'valid_id.jpg',
            'barangay_clearance' => 'barangay.png',
            'first' => 'Francis',
            'middle' => 'Reyes',
            'last' => 'De Galicia',
            'age' => '21',
            'gender' => 'Female',
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
            'bike_id' => '7',
            'term' => '4',
            'installment' => '1',
            'start' => 'August 15, 2023',
            'end' => 'November 15, 2023',
            'status' => 'pending',
            'ci_status' => 'approved',
        ]);
    }
    public function Applicant5(): self
    {
        return $this->state([
            'user_id' => '15',
            'branch_id' => '2',
            'picture' => 'picture',
            'valid_id_list' => 'driver_license',
            'valid_id' => 'valid_id.jpg',
            'barangay_clearance' => 'barangay.png',
            'first' => 'Avy',
            'middle' => 'Cruz',
            'last' => 'Tomas',
            'age' => '21',
            'gender' => 'Female',
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
            'bike_id' => '4',
            'term' => '4',
            'installment' => '4',
            'start' => 'August 3, 2023',
            'end' => 'November 3, 2023',
            'status' => 'pending',
            'ci_status' => 'approved',
        ]);
    }
    public function Applicant6(): self
    {
        return $this->state([
            'user_id' => '16',
            'branch_id' => '2',
            'picture' => 'picture',
            'valid_id_list' => 'driver_license',
            'valid_id' => 'valid_id.jpg',
            'barangay_clearance' => 'barangay.png',
            'first' => 'Andre',
            'middle' => 'De Guzman',
            'last' => 'Macalino',
            'age' => '21',
            'gender' => 'Female',
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
            'bike_id' => '4',
            'term' => '4',
            'installment' => '1',
            'start' => 'August 17, 2023',
            'end' => 'November 17, 2023',
            'status' => 'pending',
            'ci_status' => 'approved',
        ]);
    }
    public function Applicant7(): self
    {
        return $this->state([
            'user_id' => '17',
            'branch_id' => '2',
            'picture' => 'picture',
            'valid_id_list' => 'driver_license',
            'valid_id' => 'valid_id.jpg',
            'barangay_clearance' => 'barangay.png',
            'first' => 'Randy',
            'middle' => 'De Cruz',
            'last' => 'Murphy',
            'age' => '21',
            'gender' => 'Female',
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
            'bike_id' => '7',
            'term' => '4',
            'installment' => '1',
            'start' => 'August 20, 2023',
            'end' => 'November 20, 2023',
            'status' => 'pending',
            'ci_status' => 'approved',
        ]);
    }
    public function Applicant8(): self
    {
        return $this->state([
            'user_id' => '18',
            'branch_id' => '2',
            'picture' => 'picture',
            'valid_id_list' => 'driver_license',
            'valid_id' => 'valid_id.jpg',
            'barangay_clearance' => 'barangay.png',
            'first' => 'Dasia',
            'middle' => 'Mills',
            'last' => 'Huels',
            'age' => '21',
            'gender' => 'Female',
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
            'bike_id' => '5',
            'term' => '4',
            'installment' => '4',
            'start' => 'August 17, 2023',
            'end' => 'November 17, 2023',
            'status' => 'pending',
            'ci_status' => 'approved',
        ]);
    }
    public function Applicant9(): self
    {
        return $this->state([
            'user_id' => '19',
            'branch_id' => '2',
            'picture' => 'picture',
            'valid_id_list' => 'driver_license',
            'valid_id' => 'valid_id.jpg',
            'barangay_clearance' => 'barangay.png',
            'first' => 'Andrea',
            'middle' => 'Cruz',
            'last' => 'De Guzman',
            'age' => '21',
            'gender' => 'Female',
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
            'bike_id' => '9',
            'term' => '4',
            'installment' => '4',
            'start' => 'August 3, 2023',
            'end' => 'November 3, 2023',
            'status' => 'pending',
            'ci_status' => 'approved',
        ]);
    }
    public function Applicant10(): self
    {
        return $this->state([
            'user_id' => '20',
            'branch_id' => '2',
            'picture' => 'picture',
            'valid_id_list' => 'driver_license',
            'valid_id' => 'valid_id.jpg',
            'barangay_clearance' => 'barangay.png',
            'first' => 'Jett',
            'middle' => 'Gol',
            'last' => 'Roger',
            'age' => '21',
            'gender' => 'Female',
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
            'bike_id' => '11',
            'term' => '4',
            'installment' => '1',
            'start' => 'August 5, 2023',
            'end' => 'November 5, 2023',
            'status' => 'pending',
            'ci_status' => 'approved',
        ]);
    }
    public function Applicant11(): self
    {
        return $this->state([
            'user_id' => '21',
            'branch_id' => '2',
            'picture' => 'picture',
            'valid_id_list' => 'driver_license',
            'valid_id' => 'valid_id.jpg',
            'barangay_clearance' => 'barangay.png',
            'first' => 'Rhea',
            'middle' => 'De leon',
            'last' => 'Janbi',
            'age' => '21',
            'gender' => 'Female',
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
            'bike_id' => '4',
            'term' => '4',
            'installment' => '4',
            'start' => 'August 17, 2023',
            'end' => 'November 17, 2023',
            'status' => 'pending',
            'ci_status' => 'pending',
        ]);
    }
    public function Applicant12(): self
    {
        return $this->state([
            'user_id' => '22',
            'branch_id' => '2',
            'picture' => 'picture',
            'valid_id_list' => 'driver_license',
            'valid_id' => 'valid_id.jpg',
            'barangay_clearance' => 'barangay.png',
            'first' => 'Llona',
            'middle' => 'Cruz',
            'last' => 'De Guzman',
            'age' => '21',
            'gender' => 'Male',
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
            'bike_id' => '9',
            'term' => '4',
            'installment' => '1',
            'start' => 'August 30, 2023',
            'end' => 'November 30, 2023',
            'status' => 'pending',
            'ci_status' => 'approved',
        ]);
    }
    public function Applicant13(): self
    {
        return $this->state([
            'user_id' => '23',
            'branch_id' => '2',
            'picture' => 'picture',
            'valid_id_list' => 'driver_license',
            'valid_id' => 'valid_id.jpg',
            'barangay_clearance' => 'barangay.png',
            'first' => 'Raven',
            'middle' => 'Coronel',
            'last' => 'Cruz',
            'age' => '21',
            'gender' => 'Female',
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
            'bike_id' => '5',
            'term' => '4',
            'installment' => '4',
            'start' => 'August 17, 2023',
            'end' => 'November 17, 2023',
            'status' => 'pending',
            'ci_status' => 'rejected',
        ]);
    }
    public function Applicant14(): self
    {
        return $this->state([
            'user_id' => '24',
            'branch_id' => '2',
            'picture' => 'picture',
            'valid_id_list' => 'driver_license',
            'valid_id' => 'valid_id.jpg',
            'barangay_clearance' => 'barangay.png',
            'first' => $this->faker->name(),
            'middle' =>  $this->faker->lastname,
            'last' => $this->faker->lastname,
            'age' => '21',
            'gender' => 'Female',
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
            'bike_id' => '12',
            'term' => '4',
            'installment' => '4',
            'start' => 'August 19, 2023',
            'end' => 'November 19, 2023',
            'status' => 'pending',
            'ci_status' => 'pending',
        ]);
    }


}