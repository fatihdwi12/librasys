<?php

namespace Database\Seeders;

use App\Models\Member;
use Illuminate\Database\Seeder;

class MemberSeeder extends Seeder
{
    public function run(): void
    {
        $members = [
            ['MBR-001', 'Ahmad Fauzi', 'ahmad@example.com', '081234567801', 'Medan', 'active'],
            ['MBR-002', 'Siti Rahma', 'siti@example.com', '081234567802', 'Medan', 'active'],
            ['MBR-003', 'Budi Santoso', 'budi@example.com', '081234567803', 'Binjai', 'active'],
            ['MBR-004', 'Dewi Lestari', 'dewi@example.com', '081234567804', 'Deli Serdang', 'inactive'],
        ];

        foreach ($members as [$code, $name, $email, $phone, $address, $status]) {
            Member::create([
                'member_code' => $code,
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'address' => $address,
                'status' => $status,
            ]);
        }
    }
}