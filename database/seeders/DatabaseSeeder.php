<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Service;
use App\Models\Customer;
use App\Models\Promo;
use App\Models\Setting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        // 1. Generate Laundry Categories
        $catKiloan = Category::create(['name' => 'Cuci Kiloan', 'slug' => 'cuci-kiloan']);
        $catSatuan = Category::create(['name' => 'Cuci Satuan (Pcs)', 'slug' => 'cuci-satuan']);
        $catDryClean = Category::create(['name' => 'Dry Clean Premium', 'slug' => 'dry-clean']);
        $catSetrika = Category::create(['name' => 'Setrika Saja', 'slug' => 'setrika-saja']);
        $catSpecial = Category::create(['name' => 'Sepatu & Perlengkapan', 'slug' => 'sepatu-perlengkapan']);

        // 2. Generate Laundry Services
        // Kiloan (unit: kg)
        Service::create([
            'category_id' => $catKiloan->id,
            'name' => 'Cuci Setrika Reguler (2 Hari)',
            'unit' => 'kg',
            'price_per_unit' => 7000,
            'estimated_duration_hours' => 48,
            'is_active' => true,
        ]);
        Service::create([
            'category_id' => $catKiloan->id,
            'name' => 'Cuci Setrika Kilat (1 Hari)',
            'unit' => 'kg',
            'price_per_unit' => 10000,
            'estimated_duration_hours' => 24,
            'is_active' => true,
        ]);
        Service::create([
            'category_id' => $catKiloan->id,
            'name' => 'Cuci Setrika Express (6 Jam)',
            'unit' => 'kg',
            'price_per_unit' => 15000,
            'estimated_duration_hours' => 6,
            'is_active' => true,
        ]);

        // Satuan (unit: pcs)
        Service::create([
            'category_id' => $catSatuan->id,
            'name' => 'Cuci Bedcover Besar',
            'unit' => 'pcs',
            'price_per_unit' => 35000,
            'estimated_duration_hours' => 72,
            'is_active' => true,
        ]);
        Service::create([
            'category_id' => $catSatuan->id,
            'name' => 'Cuci Selimut / Sprei',
            'unit' => 'pcs',
            'price_per_unit' => 15000,
            'estimated_duration_hours' => 48,
            'is_active' => true,
        ]);
        Service::create([
            'category_id' => $catSatuan->id,
            'name' => 'Cuci Kemeja / Celana Satuan',
            'unit' => 'pcs',
            'price_per_unit' => 6000,
            'estimated_duration_hours' => 48,
            'is_active' => true,
        ]);

        // Dry Clean (unit: pcs)
        Service::create([
            'category_id' => $catDryClean->id,
            'name' => 'Dry Clean Jas Lengkap (Set)',
            'unit' => 'pcs',
            'price_per_unit' => 50000,
            'estimated_duration_hours' => 72,
            'is_active' => true,
        ]);
        Service::create([
            'category_id' => $catDryClean->id,
            'name' => 'Dry Clean Gaun Pengantin / Pesta',
            'unit' => 'pcs',
            'price_per_unit' => 120000,
            'estimated_duration_hours' => 96,
            'is_active' => true,
        ]);

        // Setrika Saja (unit: kg)
        Service::create([
            'category_id' => $catSetrika->id,
            'name' => 'Setrika Reguler (2 Hari)',
            'unit' => 'kg',
            'price_per_unit' => 4000,
            'estimated_duration_hours' => 48,
            'is_active' => true,
        ]);
        Service::create([
            'category_id' => $catSetrika->id,
            'name' => 'Setrika Kilat (1 Hari)',
            'unit' => 'kg',
            'price_per_unit' => 6000,
            'estimated_duration_hours' => 24,
            'is_active' => true,
        ]);

        // Sepatu & Perlengkapan (unit: pasang / pcs)
        Service::create([
            'category_id' => $catSpecial->id,
            'name' => 'Cuci Sepatu Premium (Deep Clean)',
            'unit' => 'pasang',
            'price_per_unit' => 30000,
            'estimated_duration_hours' => 72,
            'is_active' => true,
        ]);
        Service::create([
            'category_id' => $catSpecial->id,
            'name' => 'Cuci Tas Premium / Leather Bag',
            'unit' => 'pcs',
            'price_per_unit' => 75000,
            'estimated_duration_hours' => 96,
            'is_active' => true,
        ]);

        // 3. Generate Dummy Customers (Regular & VIP Members)
        Customer::create([
            'name' => 'Rendy Wijaya',
            'phone' => '081234567890',
            'email' => 'rendy9008@gmail.com',
            'address' => 'Perumahan Indah Asri Blok C No. 12, Deli Serdang',
            'member_status' => 'VIP',
            'loyalty_points' => 150,
        ]);
        Customer::create([
            'name' => 'Budi Santoso',
            'phone' => '085712345678',
            'email' => 'budi.santoso@example.com',
            'address' => 'Jl. Merdeka No. 88, Medan Baru',
            'member_status' => 'regular',
            'loyalty_points' => 45,
        ]);
        Customer::create([
            'name' => 'Siti Aminah',
            'phone' => '089987654321',
            'email' => 'siti.aminah@example.com',
            'address' => 'Kost Cantik Putri, Gang Damai No. 5, Deli Serdang',
            'member_status' => 'regular',
            'loyalty_points' => 15,
        ]);

        // 4. Generate Laundry Promos
        Promo::create([
            'name' => 'MEMBERBARU',
            'discount_type' => 'percentage',
            'discount_value' => 10,
            'is_active' => true,
        ]);
        Promo::create([
            'name' => 'LAUNDRYBERKAH',
            'discount_type' => 'nominal',
            'discount_value' => 10000,
            'is_active' => true,
        ]);
        Promo::create([
            'name' => 'VIPDISCOUNT',
            'discount_type' => 'percentage',
            'discount_value' => 15,
            'is_active' => true,
        ]);

        // 5. Generate Default Settings
        Setting::updateOrCreate(
            ['id' => 1],
            [
                'store_name' => 'LaundrySync POS',
                'phone' => '081234567890',
                'address' => 'Jl. Serdang Raya No. 45, Deli Serdang',
                'tax_rate' => 10,
            ]
        );

        // 6. Call System Roles & Permission seeders
        $this->call([
            RolePermissionSeeder::class,
            SuperAdminSeeder::class,
        ]);
    }
}
