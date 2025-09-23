<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Setting::updateOrCreate(['key' => 'store_name'], ['value' => 'Tropis Tee']);
        Setting::updateOrCreate(['key' => 'logo'], ['value' => '']);
        Setting::updateOrCreate(['key' => 'welcome_greeting'], ['value' => 'Selamat Datang di Tropis Tee']);
        Setting::updateOrCreate(['key' => 'slogan'], ['value' => 'Slogan toko Anda akan tampil di sini.']);
        Setting::updateOrCreate(['key' => 'business_description'], ['value' => 'Deskripsi singkat mengenai usaha TropisTee.']);
        Setting::updateOrCreate(['key' => 'vision'], ['value' => 'Visi perusahaan Anda akan tampil di sini.']);
        Setting::updateOrCreate(['key' => 'mission'], ['value' => 'Misi perusahaan Anda akan tampil di sini.']);
        Setting::updateOrCreate(['key' => 'address'], ['value' => '[Alamat belum diatur]']);
        Setting::updateOrCreate(['key' => 'email'], ['value' => '[email@anda.com]']);
        Setting::updateOrCreate(['key' => 'phone_number'], ['value' => '6281234567890']);
        Setting::updateOrCreate(['key' => 'social_link'], ['value' => 'https://www.instagram.com/tropistee']);
        Setting::updateOrCreate(['key' => 'favicon'], ['value' => '']);
    }
}