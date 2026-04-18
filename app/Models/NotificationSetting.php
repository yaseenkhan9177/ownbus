<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationSetting extends Model
{
    protected $connection = 'tenant';
    protected $fillable = [
        'twilio_sid',
        'twilio_token',
        'twilio_whatsapp_from',
        'smtp_host',
        'smtp_port',
        'smtp_encryption',
        'smtp_user',
        'smtp_pass',
        'smtp_from_address',
        'smtp_from_name',
        'admin_notification_email',
        'admin_whatsapp_number',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
