<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GlobalSetting;

class GlobalSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            // General Settings
            ['key' => 'app_name', 'value' => 'Aetheired SaaS Platform', 'type' => 'string', 'group' => 'general'],
            ['key' => 'support_email', 'value' => 'support@aetheired.com', 'type' => 'string', 'group' => 'general'],
            ['key' => 'timezone', 'value' => 'UTC', 'type' => 'string', 'group' => 'general'],

            // Branding Settings
            ['key' => 'primary_color', 'value' => '#06b6d4', 'type' => 'string', 'group' => 'branding'],
            ['key' => 'logo_url', 'value' => '', 'type' => 'string', 'group' => 'branding'],

            // Mail Settings
            ['key' => 'mail_from_address', 'value' => 'no-reply@aetheired.com', 'type' => 'string', 'group' => 'mail'],
            ['key' => 'mail_from_name', 'value' => 'Aetheired Operations', 'type' => 'string', 'group' => 'mail'],

            // Advanced Settings
            ['key' => 'maintenance_mode', 'value' => '0', 'type' => 'boolean', 'group' => 'advanced'],
            ['key' => 'max_login_attempts', 'value' => '5', 'type' => 'integer', 'group' => 'advanced'],
            ['key' => 'default_pagination_limit', 'value' => '15', 'type' => 'integer', 'group' => 'advanced'],
        ];

        foreach ($settings as $setting) {
            GlobalSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
