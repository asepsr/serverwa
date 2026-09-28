<?php

namespace Database\Seeders;

use App\Models\Product;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        Product::create([
            'name' => 'Masker Wajah 3Pcs',
            'description' => 'Masker wajah premium dengan bahan alami',
            'price' => 25000,
            'stock' => 100,
            'category' => 'furniture',
            'is_active' => true,
        ]);
        Product::create([
            'name' => 'Kemeja Casual Polos',
            'description' => 'Kemeja casual ukuran L, warna biru navy',
            'price' => 125000,
            'stock' => 50,
            'category' => 'clothing',
            'is_active' => true,
        ]);
        Product::create([
            'name' => 'Organizer Meja Kayu',
            'description' => 'Organizer meja 3 laci, warna natural',
            'price' => 85000,
            'stock' => 30,
            'category' => 'electronics',
            'is_active' => true,
        ]);
        Product::create([
            'name' => 'Buku Catatan A5',
            'description' => 'Buku catatan 200 halaman, cover keras',
            'price' => 35000,
            'stock' => 200,
            'category' => 'books',
            'is_active' => true,
        ]);
        Product::create([
            'name' => 'Powerbank 10000mAh',
            'description' => 'Powerbank fast charging, hitam',
            'price' => 150000,
            'stock' => 25,
            'category' => 'electronics',
            'is_active' => true,
        ]);
        Product::create([
            'name' => 'Snack Kopi Kemasan',
            'description' => 'Kopi sachet 3in1, 10 bungkus',
            'price' => 15000,
            'stock' => 300,
            'category' => 'food',
            'is_active' => true,
        ]);
    }
}
