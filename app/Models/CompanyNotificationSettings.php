<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyNotificationSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'whatsapp_number',
        'whatsapp_enabled',
        'notify_new_rental',
        'notify_rental_expiring',
        'notify_payment',
        'notify_new_fine',
        'notify_document_expiring',
        'notify_maintenance',
        'notify_driver_license',
        'notify_subscription',
    ];

    protected $casts = [
        'whatsapp_enabled' => 'boolean',
        'notify_new_rental' => 'boolean',
        'notify_rental_expiring' => 'boolean',
        'notify_payment' => 'boolean',
        'notify_new_fine' => 'boolean',
        'notify_document_expiring' => 'boolean',
        'notify_maintenance' => 'boolean',
        'notify_driver_license' => 'boolean',
        'notify_subscription' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
