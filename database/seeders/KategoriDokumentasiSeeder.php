<?php

namespace Database\Seeders;

use App\Models\Kategori;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class KategoriDokumentasiSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['nama' => 'Monitor', 'tipe' => 'Hardware'],
            ['nama' => 'Keyboard', 'tipe' => 'Hardware'],
            ['nama' => 'Mouse', 'tipe' => 'Hardware'],
            ['nama' => 'Printer', 'tipe' => 'Hardware'],
            ['nama' => 'Processor', 'tipe' => 'Hardware'],
            ['nama' => 'Hardisk', 'tipe' => 'Hardware'],
            ['nama' => 'NIC', 'tipe' => 'Hardware'],
            ['nama' => 'VGA', 'tipe' => 'Hardware'],
            ['nama' => 'RAM', 'tipe' => 'Hardware'],
            ['nama' => 'Motherboard', 'tipe' => 'Hardware'],
            ['nama' => 'RJ45', 'tipe' => 'Hardware'],
            ['nama' => 'Kabel UTP/STP', 'tipe' => 'Hardware'],
            ['nama' => 'Switch HUB', 'tipe' => 'Hardware'],
            ['nama' => 'Lancard', 'tipe' => 'Hardware'],
            ['nama' => 'Switch Manageable', 'tipe' => 'Hardware'],
            ['nama' => 'Converter FO', 'tipe' => 'Hardware'],
            ['nama' => 'Patchcord FO', 'tipe' => 'Hardware'],
            ['nama' => 'SFP', 'tipe' => 'Hardware'],
            ['nama' => 'UPS', 'tipe' => 'Hardware'],

            ['nama' => 'Install OS', 'tipe' => 'Software'],
            ['nama' => 'Install Aplikasi', 'tipe' => 'Software'],

            ['nama' => 'Config AP', 'tipe' => 'Network'],
            ['nama' => 'Config Switch', 'tipe' => 'Network'],
            ['nama' => 'WIFI', 'tipe' => 'Network'],
        ];

        $badgeMap = [
            'Hardware' => 'blue',
            'Software' => 'emerald',
            'Network' => 'amber',
        ];

        foreach ($categories as $category) {
            $slug = Str::slug($category['nama']);

            Kategori::updateOrCreate(
                ['slug' => $slug],
                [
                    'nama' => $category['nama'],
                    'slug' => $slug,
                    'tipe' => $category['tipe'],
                    'warna_badge' => $badgeMap[$category['tipe']] ?? 'gray',
                    'is_active' => true,
                    'deskripsi' => null,
                ],
            );
        }
    }
}
