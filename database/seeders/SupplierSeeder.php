<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = [
            [
                'Name' => 'Apple Inc.',
                'Contact' => '+1-800-275-2273',
                'Address' => 'One Apple Park Way, Cupertino, CA 95014, USA'
            ],
            [
                'Name' => 'Samsung Electronics',
                'Contact' => '+82-2-2255-0114',
                'Address' => '129 Samsung-ro, Yeongtong-gu, Suwon-si, Gyeonggi-do, South Korea'
            ],
            [
                'Name' => 'Google LLC',
                'Contact' => '+1-650-253-0000',
                'Address' => '1600 Amphitheatre Parkway, Mountain View, CA 94043, USA'
            ],
            [
                'Name' => 'OnePlus Technology',
                'Contact' => '+86-755-2882-1808',
                'Address' => 'Tairan Building, Chegongmiao, Futian District, Shenzhen, China'
            ],
            [
                'Name' => 'Xiaomi Corporation',
                'Contact' => '+86-400-100-5678',
                'Address' => 'Xiaomi Campus, 33 Xi Erqi Middle Road, Haidian District, Beijing, China'
            ]
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
}