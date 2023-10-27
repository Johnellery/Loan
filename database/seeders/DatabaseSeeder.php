<?php

namespace Database\Seeders;

use App\Models\Applicant;
use App\Models\Bike;
use App\Models\Branch;
use App\Models\Rate;
use App\Models\Role;
use App\Models\User;
use App\Models\Billing;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Faker\Factory as FakerFactory;

class DatabaseSeeder extends Seeder
{
    public function run()
    {

        //ROLE
        $adminRole = Role::factory()->create();
        $roleTypes = ['staff', 'collector', 'customer'];

        foreach ($roleTypes as $roleType) {
            Role::factory()->$roleType()->create();
        }

        //ROLE

        foreach ([ 'billing'] as $billing) {
            Billing::factory()->$billing()->create();
        }
        foreach (range(1, 130) as $billingNumber) {
            $billing = 'billing' . $billingNumber;
            Billing::factory()->$billing()->create();
        }

        // Billing::factory()->count(50)->create();
        //BRANCH
        Branch::factory()->create();
        foreach (['Pulilan', 'Bocue', 'Sanildefonso', 'Balagtas', 'Calumpit', 'Bulacan', 'Jose'] as $branchState) {
            Branch::factory()->$branchState()->create();
        }
        //BRANCH

        //CATEGORY
        Category::factory()->create();
        foreach ([ 'roadBike', 'foldingBike', 'touringBike', 'bmx', 'cruiser', 'hybridBike', 'cyclocrossBike'] as $categoryState) {
            Category::factory()->$categoryState()->create();
        }
        //CATEGORY
        foreach (['rate1', 'rate2', 'rate3', 'rate4', 'rate5', 'rate6', 'rate7', 'rate8', 'rate9', 'rate10','rate11', ] as $rate) {
            Rate::factory()->$rate()->create();
        }
        //USER
        $adminUser = User::factory()->create([
            'first' => 'Erick',
            'middle' => 'De Guzman',
            'last' => 'Cruz',
            'phone' => '09466837683',
            'name' => 'Admin',
            'email' => 'admin@gmail.com',
            'role_id' => 1,
            'branch_id' => 2,
            'status' => 'active',
        ]);
        $staffUser = User::factory()->create([
            'first' => 'Frank',
            'middle' => 'De Galicia',
            'last' => 'Cortez',
            'phone' => '09764345632',
            'name' => 'StaRitaStaff',
            'email' => 'StaRitastaff@gmail.com',
            'role_id' => 2,
            'branch_id' => '1',
            'status' => 'active',
        ]);
        $staffUser1 = User::factory()->create([
            'first' => 'Jenny',
            'middle' => 'Marquez',
            'last' => 'Espanola',
            'phone' => '09164465467',
            'name' => 'PulilanStaff',
            'email' => 'Pulilanstaff@gmail.com',
            'role_id' => 2,
            'branch_id' => 2,
            'status' => 'active',
        ]);
        $collectorUser1 = User::factory()->create([
            'first' => 'Jericho',
            'middle' => 'Fermantez',
            'last' => 'Batad',
            'phone' => '09543564234',
            'name' => 'PulilanCollector',
            'email' => 'Pulilancollector@gmail.com',
            'role_id' => 3,
            'branch_id' => 2,
            'status' => 'active',
        ]);
        $collectorUser = User::factory()->create([
            'first' => 'apalit',
            'last' => 'collector',
            'phone' => '09543564234',
            'name' => 'StaRitaCollector',
            'email' => 'StaRitacollector@gmail.com',
            'role_id' => 3,
            'branch_id' => 1,
            'status' => 'active',
        ]);
        User::factory()->count(80)->create();
        //USER

        //BIKE
    $bikeStates = [ 'touring1_0', 'trinx700c', 'foxterPowell1_2', 'trinxM136', 'trinxM6000', 'tfFoxterFT30127_5', 'foxterAero', 'trinxClimber1_1', 'Specialized2', 'Specialized1', 'Specialized'
                ,'Trek', 'Trek1', 'Trek2', 'Giant', 'Giant1', 'Giant2', 'Cannondale', 'Cannondale1', 'Cannondale2', 'Pinarello', 'Pinarello1', 'Pinarello2',

                'Specialized2Sta', 'Specialized1Sta', 'SpecializedSta'
                ,'TrekSta', 'Trek1Sta', 'Trek2Sta', 'GiantSta', 'Giant1Sta', 'Giant2Sta', 'CannondaleSta', 'Cannondale1Sta', 'Cannondale2Sta', 'PinarelloSta', 'Pinarello1Sta', 'Pinarello2Sta',];
        foreach ($bikeStates as $bikeState) {
            Bike::factory()->$bikeState()->create();
}
        //BIKE

        //APPLICANT

    $applicantStates = [ 'Applicant','Applicant2','Applicant3','Applicant4','Applicant5','Applicant6','Applicant7','Applicant8','Applicant9','Applicant10',
    'Applicant11','Applicant12','Applicant13','Applicant14',

    'Applicant15','Applicant16','Applicant17','Applicant18','Applicant19','Applicant20','Applicant21','Applicant22','Applicant23','Applicant24',
    'Applicant25','Applicant26','Applicant27','Applicant28',
    'Applicant29','Applicant30','Applicant31','Applicant32','Applicant33','Applicant34','Applicant35','Applicant36','Applicant37','Applicant38',
    'Applicant39','Applicant40','Applicant41',
    'Applicant42','Applicant43','Applicant44','Applicant45','Applicant46','Applicant47','Applicant48','Applicant49','Applicant50','Applicant51',
    'Applicant52','Applicant53','Applicant54','Applicant55','Applicant56','Applicant57',];
        foreach ($applicantStates as $applicantState) {
            Applicant::factory()->$applicantState()->create();
                }
    //             Applicant::factory()->count(70)->create();
    //             $faker = FakerFactory::create();
    //             Applicant::factory()->count(50)->create([
    //                 'user_id' => '10',
    //                 'branch_id' => '2',
    //                 'picture' => 'picture',
    //                 'valid_id_list' => 'driver_license',
    //                 'valid_id' => 'valid_id.jpg',
    //                 'barangay_clearance' => 'barangay.png',
    //                 'first' => $faker->name(),
    //                 'middle' => $faker->lastname,
    //                 'last' => $faker->lastname,
    //                 'age' => $faker->randomElement(['23', '25', '34', '43','52', '61', '73', '32', '37', '29', '30', '26', '22', '55']),
    //                 'gender' => $faker->randomElement(['Male', 'Female']),
    //                 'civil' => 'single',
    //                 'religion' => 'Catholic',
    //                 'occupation' => 'Manager',
    //                 'contact_applicant' => '09466837683',
    //                 'spouse' => '',
    //                 'contact_spouse' => '',
    //                 'occupation_spouse' => '',
    //                 'unit' => '0035',
    //                 'barangay' => 'Inaon',
    //                 'city' => 'Pulilan',
    //                 'province' => 'Bulacan',
    //                 'bike_id' => '3',
    //                 'term' => '4',
    //                 'installment' => '4',
    //                 'start' => 'August 17, 2023',
    //                 'end' => 'November 17, 2023',
    //                 'status' => 'approved',
    //                 'ci_status' => 'approved',
    //                 'bike_price' => '19500',
    //                 'down_payment' => '2000',
    //                 'total_interest' => '3900',
    //                 'payment' => '1070',
    //                 'plus' => '23400',
    //                 'remaining_balance' => '0',
    //             ]);
        //APPLICANT
            }
}

