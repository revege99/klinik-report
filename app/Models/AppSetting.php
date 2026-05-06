<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppSetting extends Model
{
    protected $fillable = [
        'setting_group',
        'setting_key',
        'setting_value',
        'label',
        'description',
        'input_type',
    ];

    public static function defaults(): array
    {
        return [
            [
                'setting_group' => 'branding',
                'setting_key' => 'login_brand_name',
                'setting_value' => 'Yayasan Karya Luhur Jaya',
                'label' => 'Nama Brand Login',
                'description' => 'Nama utama yang tampil pada halaman login.',
                'input_type' => 'text',
            ],
            [
                'setting_group' => 'branding',
                'setting_key' => 'login_brand_caption',
                'setting_value' => 'Sistem laporan operasional yayasan.',
                'label' => 'Caption Brand Login',
                'description' => 'Teks kecil pendamping brand pada halaman login.',
                'input_type' => 'text',
            ],
            [
                'setting_group' => 'branding',
                'setting_key' => 'login_welcome_message',
                'setting_value' => 'Bekerja dengan cinta akan menghasilkan keajaiban.',
                'label' => 'Kata Sambutan Login',
                'description' => 'Judul sambutan utama pada halaman login.',
                'input_type' => 'text',
            ],
        ];
    }
}
