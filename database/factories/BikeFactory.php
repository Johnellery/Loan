<?php
namespace Database\Factories;

use App\Models\Bike;
use Illuminate\Database\Eloquent\Factories\Factory;

class BikeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'image' => 'default.jpg', // Default image or any other default value
            'name' => 'Default Bike',
            'price' => '1000',
            'brand' => '',
            'rate' => '10',
            'description' => 'Default bike description...',
            'category_id' => '1',
            'branch_id' => '2',
            'user_id' => '3',
            'down' => '1000',
            'status' => 'approved',
        ];
    }

    public function touring1_0(): self
    {
        return $this->state([
            'image' => 'touring.jpg',
            'name' => 'Trinx Touring 1.0',
            'brand' => 'Trinx',
            'price' => '5600',
            'rate' => '10',
            'description' => 'A versatile road bike, great for long rides. Lightweight aluminum frame, responsive brakes, and smooth gear shifting. Ideal for touring and commuting.',
            'category_id' => '4',
            'branch_id' => '2',
            'user_id' => '3',
            'down' => '1000',
            'status' => 'approved',
        ]);
    }

    public function trinx700c(): self
    {
        return $this->state([
            'image' => '700c.jpg',
            'name' => 'Trinx 700c',
            'brand' => 'Trinx',
            'price' => '9500',
            'rate' => '20',
            'description' => 'A road bike with 700c wheels, providing a smooth and efficient ride. Designed for speed and agility, suitable for various terrains and distances.',
            'category_id' => '2',
            'branch_id' => '2',
            'user_id' => '3',
            'down' => '2000',
            'status' => 'approved',
        ]);
    }

    public function foxterPowell1_2(): self
    {
        return $this->state([
            'image' => 'powell.jpg',
            'name' => 'Foxter POWELL 1.2',
            'brand' => 'Foxter',
            'price' => '19500',
            'rate' => '20',
            'description' => ' A mountain bike built for versatility. Durable frame, front suspension, and responsive brakes. Perfect for off-road adventures and trails.            ',
            'category_id' => '1',
            'branch_id' => '2',
            'user_id' => '2',
            'down' => '2000',
            'is_available' => 'unavailable',
            'status' => 'approved',
        ]);
    }

    public function trinxM136(): self
    {
        return $this->state([
            'image' => 'm136.jpg',
            'name' => 'Trinx M136',
            'brand' => 'Trinx',
            'price' => '14500',
            'rate' => '20',
            'description' => 'An entry-level mountain bike, suitable for casual off-road riding. Sturdy frame, basic suspension, and reliable performance for beginners.',
            'category_id' => '1',
            'branch_id' => '2',
            'user_id' => '3',
            'down' => '2000',
            'status' => 'approved',
        ]);
    }

    public function trinxM6000(): self
    {
        return $this->state([
            'image' => 'm6000.jpeg',
            'name' => 'Trinx M6000',
            'brand' => 'Trinx',
            'price' => '11500',
            'rate' => '20',
            'description' => 'A capable mountain bike with quality components. Lightweight alloy frame, responsive disc brakes, and precise gear shifting. Ideal for trail riding and off-road adventures.',
            'category_id' => '1',
            'branch_id' => '2',
            'user_id' => '3',
            'down' => '2000',
            'status' => 'approved',
        ]);
    }

    public function tfFoxterFT30127_5(): self
    {
        return $this->state([
            'image' => 'ft.jpg',
            'name' => 'Foxter FT301 27.5',
            'brand' => 'Foxter',
            'price' => '6450',
            'rate' => '10',
            'description' => 'A versatile mountain bike with 27.5-inch wheels. Robust frame, front suspension, and reliable brakes. Suited for various terrains, great for recreational and trail riding.',
            'category_id' => '1',
            'branch_id' => '2',
            'user_id' => '3',
            'down' => '1000',
            'status' => 'approved',
        ]);
    }

    public function foxterAero(): self
    {
        return $this->state([
            'image' => 'aero.jpg',
            'name' => 'Foxter Aero',
            'brand' => 'Foxter',
            'price' => '6820',
            'rate' => '10',
            'description' => 'A high-performance road bike built for speed and aerodynamics. Lightweight carbon frame, aerodynamic design, and quality components. Ideal for racing and fast-paced rides.',
            'category_id' => '2',
            'branch_id' => '2',
            'user_id' => '3',
            'down' => '1000',
            'status' => 'rejected',
        ]);
    }

    public function trinxClimber1_1(): self
    {
        return $this->state([
            'image' => 'climber.jpeg',
            'name' => 'Trinx Climber 1.1',
            'brand' => 'Trinx',
            'price' => '16000',
            'rate' => '20',
            'description' => 'A rugged mountain bike for challenging trails. Robust frame, front suspension, and powerful disc brakes. Suited for adventurous off-road riding.',
            'category_id' => '2',
            'branch_id' => '2',
            'user_id' => '3',
            'down' => '2000',
            'is_available' => 'unavailable',
            'status' => 'approved',
        ]);
    }
    public function Specialized(): self
    {
        return $this->state([
            'image' => 'tarmac.jpg',
            'name' => 'Specialized Tarmac',
            'brand' => 'Specialized',
            'price' => '6000',
            'rate' => '10',
            'description' => 'A premium road bike designed for speed and precision. Lightweight carbon frame, advanced components, and aerodynamic features. Ideal for racing and enthusiastic road cyclists.',
            'category_id' => '2',
            'branch_id' => '2',
            'user_id' => '3',
            'down' => '1000',
            'is_available' => 'available',
            'status' => 'pending',
        ]);
    }
    public function Specialized1(): self
    {
        return $this->state([
            'image' => 'roubaix.jpg',
            'name' => 'Specialized Roubaix',
            'brand' => 'Specialized',
            'price' => '7000',
            'rate' => '10',
            'description' => 'A performance road bike built for comfort and endurance. Innovative suspension technology, lightweight frame, and smooth handling. Perfect for long-distance rides and rough roads.',
            'category_id' => '2',
            'branch_id' => '2',
            'user_id' => '3',
            'down' => '1000',
            'is_available' => 'available',
            'status' => 'pending',
        ]);
    }
    public function Specialized2(): self
    {
        return $this->state([
            'image' => 'allez.jpeg',
            'name' => 'Specialized Allez',
            'brand' => 'Specialized',
            'price' => '9600',
            'rate' => '20',
            'description' => 'A versatile entry-level road bike. Sturdy aluminum frame, reliable components, and a comfortable riding position. Great for beginners and casual road cycling.',
            'category_id' => '2',
            'branch_id' => '2',
            'user_id' => '3',
            'down' => '2000',
            'is_available' => 'available',
            'status' => 'approved',
        ]);
    }
    public function Trek(): self
    {
        return $this->state([
            'image' => 'madone.jpg',
            'name' => 'Trek Madone',
            'brand' => 'Trek',
            'price' => '9900',
            'rate' => '20',
            'description' => 'A high-end road bike known for its aerodynamic design and cutting-edge technology. Lightweight carbon frame, precise components, and exceptional speed. Ideal for serious cyclists and competitive racing.',
            'category_id' => '2',
            'branch_id' => '2',
            'down' => '2000',
            'user_id' => '3',
            'is_available' => 'available',
            'status' => 'approved',
        ]);
    }
    public function Trek1(): self
    {
        return $this->state([
            'image' => 'emonda.jpg',
            'name' => 'Trek Emonda',
            'brand' => 'Trek',
            'price' => '11800',
            'rate' => '20',
            'description' => 'A premium road bike known for its lightweight construction and impressive climbing ability. Features a carbon frame, responsive components, and a focus on performance. Ideal for those who value climbing and agility.',
            'category_id' => '2',
            'branch_id' => '2',
            'user_id' => '3',
            'down' => '2000',
            'is_available' => 'available',
            'status' => 'approved',
        ]);
    }
    public function Trek2(): self
    {
        return $this->state([
            'image' => 'domane.webp',
            'name' => 'Trek Domane',
            'brand' => 'Trek',
            'price' => '12500',
            'rate' => '20',
            'description' => 'A performance road bike designed for comfort and endurance. Notable for its IsoSpeed technology, offering a smooth ride over rough roads. Lightweight frame, stable handling, and suitable for long-distance cycling.',
            'category_id' => '2',
            'branch_id' => '2',
            'user_id' => '3',
            'down' => '2000',
            'is_available' => 'available',
            'status' => 'approved',
        ]);
    }
    public function Giant(): self
    {
        return $this->state([
            'image' => 'tcr.jpg',
            'name' => 'Giant TCR',
            'brand' => 'Giant',
            'price' => '11550',
            'rate' => '20',
            'description' => 'A popular road bike celebrated for its balanced performance. Features a lightweight frame, precise components, and an emphasis on speed and agility. Suitable for a wide range of road cycling activities.',
            'category_id' => '2',
            'branch_id' => '2',
            'user_id' => '3',
            'down' => '2000',
            'is_available' => 'available',
            'status' => 'approved',
        ]);
    }
    public function Giant1(): self
    {
        return $this->state([
            'image' => 'defy.jpg',
            'name' => 'Giant Defy',
            'brand' => 'Giant',
            'price' => '9100',
            'rate' => '20',
            'description' => 'A versatile road bike designed for endurance and comfort. Notable for its endurance geometry, offering a smoother ride over long distances. Features a lightweight frame, responsive components, and is ideal for riders looking for a more comfortable road cycling experience.',
            'category_id' => '2',
            'branch_id' => '2',
            'user_id' => '3',
            'down' => '2000',
            'is_available' => 'available',
            'status' => 'approved',
        ]);
    }
    public function Giant2(): self
    {
        return $this->state([
            'image' => 'propel.jpg',
            'name' => 'Giant Propel',
            'brand' => 'Giant',
            'price' => '9300',
            'rate' => '20',
            'description' => 'A high-performance aero road bike designed for speed and efficiency. Features a wind-tunnel-tested design, a lightweight carbon frame, responsive components, and is ideal for riders looking to maximize their speed on the road.',
            'category_id' => '2',
            'branch_id' => '2',
            'user_id' => '3',
            'down' => '2000',
            'is_available' => 'available',
            'status' => 'approved',
        ]);
    }
    public function Cannondale(): self
    {
        return $this->state([
            'image' => 'caad.jpg',
            'name' => 'Cannondale CAAD',
            'brand' => 'Cannondale',
            'price' => '9300',
            'rate' => '20',
            'description' => 'A series of road bikes by Cannondale known for their high-quality aluminum frames. They offer a balance of performance and affordability, making them popular among cyclists seeking a responsive and durable ride.',
            'category_id' => '2',
            'branch_id' => '2',
            'user_id' => '3',
            'down' => '2000',
            'is_available' => 'available',
            'status' => 'approved',
        ]);
    }
    public function Cannondale1(): self
    {
        return $this->state([
            'image' => 'supersix.jpg',
            'name' => 'Cannondale SuperSix',
            'brand' => 'Cannondale',
            'price' => '9300',
            'rate' => '20',
            'description' => 'A high-end road bike series known for its lightweight carbon frames, aerodynamic design, and exceptional performance. Its a top choice for serious road cyclists and racers, providing a perfect blend of speed, responsiveness, and agility.',
            'category_id' => '2',
            'branch_id' => '2',
            'user_id' => '3',
            'down' => '2000',
            'is_available' => 'available',
            'status' => 'approved',
        ]);
    }
    public function Cannondale2(): self
    {
        return $this->state([
            'image' => 'synapse.jpg',
            'name' => 'Cannondale Synapse',
            'brand' => 'Cannondale',
            'price' => '9300',
            'rate' => '20',
            'description' => 'A road bike series designed for endurance and comfort, making it ideal for long-distance rides and rough road conditions. It features a more relaxed geometry and incorporates vibration-dampening technologies for a smoother and more comfortable riding experience. ',
            'category_id' => '2',
            'branch_id' => '2',
            'user_id' => '3',
            'down' => '2000',
            'is_available' => 'available',
            'status' => 'approved',
        ]);
    }
    public function Pinarello(): self
    {
        return $this->state([
            'image' => 'gan.jpg',
            'name' => 'Pinarello Gan',
            'brand' => 'Pinarello',
            'price' => '9300',
            'rate' => '20',
            'description' => 'A road bike series by Pinarello, offering an accessible entry into the brands renowned designs. These bikes feature a high-quality carbon frame and performance-oriented components. While not as high-end as some Pinarello models, the Gan series still provides a smooth, responsive, and enjoyable ride for cyclists looking for a taste of Italian craftsmanship.',
            'category_id' => '2',
            'branch_id' => '2',
            'user_id' => '3',
            'down' => '2000',
            'is_available' => 'available',
            'status' => 'approved',
        ]);
    }
    public function Pinarello1(): self
    {
        return $this->state([
            'image' => 'prince.jpg',
            'name' => 'Pinarello Prince',
            'brand' => 'Pinarello',
            'price' => '9300',
            'rate' => '20',
            'description' => 'A premium road bike series by Pinarello, known for its combination of performance and comfort. The Prince series features a high-quality carbon frame, top-tier components, and advanced aerodynamics.',
            'category_id' => '2',
            'branch_id' => '2',
            'user_id' => '3',
            'down' => '2000',
            'is_available' => 'available',
            'status' => 'approved',
        ]);
    }
    public function Pinarello2(): self
    {
        return $this->state([
            'image' => 'dogma.jpg',
            'name' => 'Pinarello Dogma',
            'brand' => 'Pinarello',
            'price' => '9300',
            'rate' => '20',
            'description' => 'A flagship road bike series by Pinarello, revered for its cutting-edge technology, aerodynamics, and exceptional performance. These bikes feature top-tier carbon frames, advanced components, and have a strong presence in the professional cycling world.',
            'category_id' => '2',
            'branch_id' => '2',
            'user_id' => '3',
            'down' => '2000',
            'is_available' => 'available',
            'status' => 'approved',
        ]);
    }
//OTHER USER
public function SpecializedSta(): self
{
    return $this->state([
        'image' => 'tarmac.jpg',
        'name' => 'Specialized Tarmac',
        'brand' => 'Specialized',
        'price' => '6000',
        'rate' => '10',
        'description' => '...',
        'category_id' => '2',
        'branch_id' => '1',
        'user_id' => '2',
        'down' => '1000',
        'is_available' => 'available',
        'status' => 'approved',
    ]);
}
public function Specialized1Sta(): self
{
    return $this->state([
        'image' => 'roubaix.jpg',
        'name' => 'Specialized Roubaix',
        'brand' => 'Specialized',
        'price' => '7000',
        'rate' => '10',
        'description' => '...',
        'category_id' => '2',
        'branch_id' => '1',
        'user_id' => '2',
        'down' => '1000',
        'is_available' => 'available',
        'status' => 'approved',
    ]);
}
public function Specialized2Sta(): self
{
    return $this->state([
        'image' => 'allez.jpeg',
        'name' => 'Specialized Allez',
        'brand' => 'Specialized',
        'price' => '9600',
        'rate' => '20',
        'description' => '...',
        'category_id' => '2',
        'branch_id' => '1',
        'user_id' => '2',
        'down' => '2000',
        'is_available' => 'available',
        'status' => 'approved',
    ]);
}
public function TrekSta(): self
{
    return $this->state([
        'image' => 'madone.jpg',
        'name' => 'Trek Madone',
        'brand' => 'Trek',
        'price' => '9900',
        'rate' => '20',
        'description' => '...',
        'category_id' => '2',
        'branch_id' => '1',
        'user_id' => '2',
        'down' => '2000',
        'is_available' => 'available',
        'status' => 'approved',
    ]);
}
public function Trek1Sta(): self
{
    return $this->state([
        'image' => 'emonda.jpg',
        'name' => 'Trek Emonda',
        'brand' => 'Trek',
        'price' => '11800',
        'rate' => '20',
        'description' => '...',
        'category_id' => '2',
        'branch_id' => '1',
        'user_id' => '2',
        'down' => '2000',
        'is_available' => 'available',
        'status' => 'approved',
    ]);
}
public function Trek2Sta(): self
{
    return $this->state([
        'image' => 'domane.webp',
        'name' => 'Trek Domane',
        'brand' => 'Trek',
        'price' => '12500',
        'rate' => '20',
        'description' => '...',
        'category_id' => '2',
        'branch_id' => '1',
        'user_id' => '2',
        'down' => '2000',
        'is_available' => 'available',
        'status' => 'approved',
    ]);
}
public function GiantSta(): self
{
    return $this->state([
        'image' => 'tcr.jpg',
        'name' => 'Giant TCR',
        'brand' => 'Giant',
        'price' => '11550',
        'rate' => '20',
        'description' => '...',
        'category_id' => '2',
        'branch_id' => '1',
        'user_id' => '2',
        'down' => '2000',
        'is_available' => 'available',
        'status' => 'approved',
    ]);
}
public function Giant1Sta(): self
{
    return $this->state([
        'image' => 'defy.jpg',
        'name' => 'Giant Defy',
        'brand' => 'Giant',
        'price' => '9100',
        'rate' => '20',
        'description' => '...',
        'category_id' => '2',
        'branch_id' => '1',
        'user_id' => '2',
        'down' => '2000',
        'is_available' => 'available',
        'status' => 'pending',
    ]);
}
public function Giant2Sta(): self
{
    return $this->state([
        'image' => 'propel.jpg',
        'name' => 'Giant Prope',
        'brand' => 'Giant',
        'price' => '9300',
        'rate' => '20',
        'description' => '...',
        'category_id' => '2',
        'branch_id' => '1',
        'user_id' => '2',
        'down' => '2000',
        'is_available' => 'available',
        'status' => 'pending',
    ]);
}
public function CannondaleSta(): self
{
    return $this->state([
        'image' => 'caad.jpg',
        'name' => 'Cannondale CAAD',
        'brand' => 'Cannondale',
        'price' => '9300',
        'rate' => '20',
        'description' => '...',
        'category_id' => '2',
        'branch_id' => '1',
        'user_id' => '2',
        'down' => '2000',
        'is_available' => 'available',
        'status' => 'pending',
    ]);
}
public function Cannondale1Sta(): self
{
    return $this->state([
        'image' => 'supersix.jpg',
        'name' => 'Cannondale SuperSix',
        'brand' => 'Cannondale',
        'price' => '9300',
        'rate' => '20',
        'description' => '...',
        'category_id' => '2',
        'branch_id' => '1',
        'user_id' => '2',
        'down' => '2000',
        'is_available' => 'available',
        'status' => 'pending',
    ]);
}
public function Cannondale2Sta(): self
{
    return $this->state([
        'image' => 'synapse.jpg',
        'name' => 'Cannondale Synapsee',
        'brand' => 'Cannondale',
        'price' => '9300',
        'rate' => '20',
        'description' => '...',
        'category_id' => '2',
        'branch_id' => '1',
        'user_id' => '2',
        'down' => '2000',
        'is_available' => 'available',
        'status' => 'pending',
    ]);
}
public function PinarelloSta(): self
{
    return $this->state([
        'image' => 'gan.jpg',
        'name' => 'Pinarello Gan',
        'brand' => 'Pinarello',
        'price' => '9300',
        'rate' => '20',
        'description' => '...',
        'category_id' => '2',
        'branch_id' => '1',
        'user_id' => '2',
        'down' => '2000',
        'is_available' => 'available',
        'status' => 'pending',
    ]);
}
public function Pinarello1Sta(): self
{
    return $this->state([
        'image' => 'prince.jpg',
        'name' => 'Pinarello Prince',
        'brand' => 'Pinarello',
        'price' => '9300',
        'rate' => '20',
        'description' => '...',
        'category_id' => '2',
        'branch_id' => '1',
        'user_id' => '2',
        'down' => '2000',
        'is_available' => 'available',
        'status' => 'pending',
    ]);
}
public function Pinarello2Sta(): self
{
    return $this->state([
        'image' => 'dogma.jpg',
        'name' => 'Pinarello Dogma',
        'brand' => 'Pinarello',
        'price' => '9300',
        'rate' => '20',
        'description' => '...',
        'category_id' => '2',
        'branch_id' => '1',
        'user_id' => '2',
        'down' => '2000',
        'is_available' => 'available',
        'status' => 'pending',
    ]);
}
}

